{{-- Usable in themes: @include('rules::widget') or @plugiitRulesStatus --}}
@auth
    @php($plugiitRules = app(\Azuriom\Plugin\Rules\Support\RulesManager::class))
    @if($plugiitRules->currentVersion() !== null)
        @if($plugiitRules->hasAccepted(auth()->user()))
            <a href="{{ route('rules.index') }}" class="plugiit-rules-widget badge text-bg-success text-decoration-none">
                <i class="bi bi-journal-check"></i> {{ trans('rules::messages.widget.accepted') }}
            </a>
        @else
            <a href="{{ route('rules.index') }}#accept" class="plugiit-rules-widget badge text-bg-warning text-decoration-none">
                <i class="bi bi-journal-text"></i> {{ trans('rules::messages.widget.pending') }}
            </a>
        @endif
    @endif
@endauth
