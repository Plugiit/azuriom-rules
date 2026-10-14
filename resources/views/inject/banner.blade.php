<div class="plugiit-rules-banner" id="plugiit-rules-banner" data-plugiit-rules-version="{{ $version->id }}" role="region" aria-label="{{ trans('plugiit-rules::messages.title') }}">
    <div class="plugiit-rules-banner-inner">
        <span class="plugiit-rules-banner-text">
            <i class="bi bi-journal-text"></i>
            {{ $lastAcceptance ? trans('plugiit-rules::messages.banner.text', ['version' => $version->version]) : trans('plugiit-rules::messages.banner.text_first') }}
        </span>
        <span class="plugiit-rules-banner-actions">
            <a href="{{ route('plugiit-rules.index') }}#accept" class="plugiit-rules-btn plugiit-rules-btn-primary">{{ trans('plugiit-rules::messages.banner.button') }}</a>
            <button type="button" class="plugiit-rules-banner-close" data-plugiit-rules-dismiss aria-label="{{ trans('plugiit-rules::messages.banner.close') }}">&times;</button>
        </span>
    </div>
</div>
