<?php

namespace Azuriom\Plugin\Rules\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\Rules\Models\Acceptance;
use Azuriom\Plugin\Rules\Support\RulesManager;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search a user and display their acceptances.
     */
    public function index(Request $request, RulesManager $rules)
    {
        $search = trim((string) $request->input('search'));
        $users = collect();
        $user = null;

        if ($request->filled('user')) {
            $user = User::find($request->integer('user'));
        } elseif ($search !== '') {
            $users = User::registered()
                ->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                ->orderBy('name')
                ->take(20)
                ->get();

            $user = $users->count() === 1 ? $users->first() : null;
        }

        return view('rules::admin.users.index', [
            'search' => $search,
            'users' => $users,
            'user' => $user,
            'acceptances' => $user !== null
                ? Acceptance::with('version')->where('user_id', $user->id)->latest('version_id')->get()
                : collect(),
            'hasAccepted' => $user !== null ? $rules->hasAccepted($user) : null,
            'required' => $rules->requiredVersion(),
        ]);
    }
}
