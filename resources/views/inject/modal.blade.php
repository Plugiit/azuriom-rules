@include('rules::inject.assets')

<div class="plugiit-rules-overlay" id="plugiit-rules-modal" role="dialog" aria-modal="true" aria-labelledby="plugiit-rules-modal-title">
    <div class="plugiit-rules-dialog">
        <div class="plugiit-rules-header">
            <div class="plugiit-rules-icon"><i class="bi bi-journal-check"></i></div>
            <div>
                <h2 class="plugiit-rules-title" id="plugiit-rules-modal-title">
                    {{ $lastAcceptance ? trans('rules::messages.accept.title') : trans('rules::messages.accept.title_first') }}
                </h2>
                <div class="plugiit-rules-meta">
                    {{ trans('rules::messages.version', ['version' => $version->version]) }}
                    · {{ trans('rules::messages.published_on', ['date' => format_date($version->published_at)]) }}
                </div>
            </div>
        </div>

        <div class="plugiit-rules-body" tabindex="0">
            <p class="plugiit-rules-intro">
                {{ $lastAcceptance ? trans('rules::messages.accept.intro') : trans('rules::messages.accept.intro_first') }}
            </p>

            @if($lastAcceptance && ($version->summary || ($diff && ! \Azuriom\Plugin\Rules\Models\Version::diffIsEmpty($diff))))
                <div class="plugiit-rules-changes">
                    <div class="plugiit-rules-changes-title"><i class="bi bi-stars"></i> {{ trans('rules::messages.changes') }}</div>
                    @if($version->summary)
                        <p>{{ $version->summary }}</p>
                    @endif
                    @if($diff)
                        <ul>
                            @foreach(['added', 'modified', 'removed'] as $type)
                                @foreach($diff[$type] as $title)
                                    <li><span class="plugiit-rules-tag plugiit-rules-tag-{{ $type }}">{{ trans('rules::messages.diff.'.$type) }}</span> {{ $title }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @foreach($version->sections as $section)
                <div class="plugiit-rules-modal-section">
                    <h3>{{ $section['title'] }}</h3>
                    <div class="plugiit-rules-content">{!! $section['content'] !!}</div>
                </div>
            @endforeach
        </div>

        <form class="plugiit-rules-footer" action="{{ route('rules.accept') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="version" value="{{ $version->id }}">
            <input type="hidden" name="source" value="modal">
            <input type="hidden" name="redirect" value="{{ request()->fullUrl() }}">

            <label class="plugiit-rules-check">
                <input type="checkbox" required data-plugiit-rules-check>
                <span>{{ trans('rules::messages.accept.checkbox', ['version' => $version->version]) }}</span>
            </label>

            <div class="plugiit-rules-actions">
                <a href="#" class="plugiit-rules-link" data-plugiit-rules-logout>{{ trans('rules::messages.accept.logout') }}</a>
                <a href="{{ route('rules.index') }}" class="plugiit-rules-link">{{ trans('rules::messages.accept.full_page') }}</a>
                <button type="submit" class="plugiit-rules-btn plugiit-rules-btn-primary" data-plugiit-rules-submit disabled>
                    <i class="bi bi-check2-circle"></i> {{ trans('rules::messages.accept.button') }}
                </button>
            </div>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="plugiit-rules-hidden" id="plugiit-rules-logout-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
    </div>
</div>
