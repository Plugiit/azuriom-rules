{{-- Usable in themes: @include('plugiit-rules::widget') or @plugiitRulesStatus --}}
@auth
    @php($plugiitRules = app(\Azuriom\Plugin\PlugiitRules\Support\RulesManager::class))
    @if($plugiitRules->currentVersion() !== null)
        @if($plugiitRules->hasAccepted(auth()->user()))
            <a href="{{ route('plugiit-rules.index') }}" class="plugiit-rules-widget badge text-bg-success text-decoration-none">
                <i class="bi bi-journal-check"></i> {{ trans('plugiit-rules::messages.widget.accepted') }}
            </a>
        @else
            <a href="{{ route('plugiit-rules.index') }}#accept" class="plugiit-rules-widget badge text-bg-warning text-decoration-none">
                <i class="bi bi-journal-text"></i> {{ trans('plugiit-rules::messages.widget.pending') }}
            </a>
        @endif
    @endif
@endauth
