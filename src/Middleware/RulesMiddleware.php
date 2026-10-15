<?php

namespace Azuriom\Plugin\Rules\Middleware;

use Azuriom\Plugin\Rules\Support\RulesManager;
use Azuriom\Plugin\Rules\Support\RulesSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

/**
 * Pushed at the end of the "web" middleware group, so the session, the
 * authenticated user and the matched route are available.
 *
 * - blocks configured actions (non-GET requests) until the rules are accepted;
 * - requires the acceptance checkbox on the registration form (if enabled);
 * - injects the acceptance modal/banner (and the register checkbox) into HTML pages.
 */
class RulesMiddleware
{
    public function __construct(protected RulesManager $rules) {}

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        if ($request->is('admin', 'admin/*', 'api/*', '_debugbar/*')) {
            return $next($request);
        }

        if ($response = $this->checkRegistration($request)) {
            return $response;
        }

        if ($response = $this->checkBlockedAction($request)) {
            return $response;
        }

        $response = $next($request);

        try {
            return $this->inject($request, $response);
        } catch (Throwable $e) {
            report($e);

            return $response;
        }
    }

    protected function checkRegistration(Request $request): ?SymfonyResponse
    {
        if ($request->isMethod('POST')
            && $this->isRegisterUrl($request)
            && RulesSettings::requireOnRegister()
            && $this->rules->currentVersion() !== null
            && ! $request->boolean('rules_accept')) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['rules_accept' => trans('rules::messages.register.required')]);
        }

        return null;
    }

    protected function checkBlockedAction(Request $request): ?SymfonyResponse
    {
        $user = $request->user();

        if ($user === null || $request->isMethodSafe() || $request->routeIs('rules.*', 'logout')) {
            return null;
        }

        $patterns = RulesSettings::blockedPatterns();

        if (empty($patterns) || ! $request->routeIs(...$patterns) || $this->rules->hasAccepted($user)) {
            return null;
        }

        $message = trans('rules::messages.blocked', ['url' => route('rules.index')]);

        if ($request->expectsJson()) {
            return response()->json(['message' => strip_tags($message)], 403);
        }

        return back()->with('error', strip_tags($message));
    }

    protected function inject(Request $request, SymfonyResponse $response): SymfonyResponse
    {
        if (! $this->isInjectable($request, $response)) {
            return $response;
        }

        $content = $response->getContent();

        if ($this->isRegisterUrl($request)) {
            return $this->injectRegisterCheckbox($response, $content);
        }

        $user = $request->user();
        $mode = RulesSettings::mode();

        if ($user === null || $mode === 'none' || $request->routeIs('rules.*')
            || $this->rules->hasAccepted($user)) {
            return $response;
        }

        $data = [
            'version' => $this->rules->currentVersion(),
            'diff' => null,
            'lastAcceptance' => $this->rules->lastAcceptance($user),
        ];

        if ($data['version'] === null) {
            return $response;
        }

        if ($data['lastAcceptance'] !== null) {
            $data['diff'] = $data['version']->diffWith($data['lastAcceptance']->version);
        }

        if ($mode === 'banner') {
            $html = view('rules::inject.banner', $data)->render();
            $content = preg_replace('/<body\b[^>]*>/i', '$0'.$this->escapeReplacement($html), $content, 1, $count);

            if ($count === 0) {
                return $response;
            }

            $html = view('rules::inject.assets')->render();
        } else {
            $html = view('rules::inject.modal', $data)->render();
        }

        return $response->setContent($this->insertBeforeBodyEnd($content, $html));
    }

    protected function injectRegisterCheckbox(SymfonyResponse $response, string $content): SymfonyResponse
    {
        if (! RulesSettings::requireOnRegister() || $this->rules->currentVersion() === null) {
            return $response;
        }

        $action = preg_quote(route('register'), '/');

        if (! preg_match('/<form\b[^>]*action="'.$action.'"[^>]*>.*?<\/form>/is', $content, $form, PREG_OFFSET_CAPTURE)) {
            return $response;
        }

        [$formHtml, $offset] = $form[0];
        $checkbox = view('rules::inject.register')->render();

        // Before the submit button (and its wrapper, if any), or else before </form>.
        if (preg_match_all('/(?:<div\b[^>]*>\s*)?<button\b[^>]*type="submit"/i', $formHtml, $buttons, PREG_OFFSET_CAPTURE)) {
            $position = end($buttons[0])[1];
        } else {
            $position = strripos($formHtml, '</form>');
        }

        $formHtml = substr_replace($formHtml, $checkbox, $position, 0);

        return $response->setContent(substr_replace($content, $formHtml, $offset, strlen($form[0][0])));
    }

    protected function isInjectable(Request $request, SymfonyResponse $response): bool
    {
        if (! $request->isMethod('GET') || $request->ajax() || $request->pjax() || $request->expectsJson()) {
            return false;
        }

        if (! $response instanceof Response || $response->getStatusCode() !== 200) {
            return false;
        }

        $type = $response->headers->get('Content-Type');

        return ($type === null || str_contains($type, 'text/html'))
            && stripos((string) $response->getContent(), '</body>') !== false;
    }

    protected function isRegisterUrl(Request $request): bool
    {
        return Route::has('register') && $request->url() === route('register');
    }

    protected function insertBeforeBodyEnd(string $content, string $html): string
    {
        $position = strripos($content, '</body>');

        return substr_replace($content, $html, $position, 0);
    }

    protected function escapeReplacement(string $value): string
    {
        return str_replace(['\\', '$'], ['\\\\', '\\$'], $value);
    }
}
