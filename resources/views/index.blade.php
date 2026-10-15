@extends('layouts.app')

@section('title', trans('rules::messages.title'))

@push('styles')
    <link href="{{ plugin_asset('rules', 'css/style.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="plugiit-rules-page">
        <h1>{{ trans('rules::messages.title') }}</h1>

        @if($version === null)
            <div class="alert alert-info"><i class="bi bi-info-circle"></i> {{ trans('rules::messages.empty') }}</div>
        @else
            @unless($isCurrent)
                <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span><i class="bi bi-clock-history"></i> {{ trans('rules::messages.old_version', ['version' => $version->version]) }}</span>
                    <a href="{{ route('rules.index') }}" class="btn btn-sm btn-warning">{{ trans('rules::messages.see_current') }}</a>
                </div>
            @endunless

            @if($mustAccept)
                <div class="card border-primary mb-4" id="accept">
                    <div class="card-body">
                        <h2 class="h5"><i class="bi bi-journal-check"></i> {{ trans('rules::messages.status.must_accept') }}</h2>
                        @if($version->summary)
                            <p class="mb-2"><strong>{{ trans('rules::messages.changes') }} :</strong> {{ $version->summary }}</p>
                        @endif
                        <form action="{{ route('rules.accept') }}" method="POST" class="d-flex flex-wrap align-items-center gap-3">
                            @csrf
                            <input type="hidden" name="version" value="{{ $version->id }}">
                            <input type="hidden" name="source" value="page">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="plugiitRulesPageCheck" required>
                                <label class="form-check-label" for="plugiitRulesPageCheck">{{ trans('rules::messages.accept.checkbox', ['version' => $version->version]) }}</label>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> {{ trans('rules::messages.accept.button') }}</button>
                        </form>
                    </div>
                </div>
            @elseif($isCurrent && $lastAcceptance)
                <div class="alert alert-success"><i class="bi bi-check-circle"></i>
                    {{ trans('rules::messages.status.accepted', ['version' => $lastAcceptance->version?->version, 'date' => format_date($lastAcceptance->created_at)]) }}
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="plugiit-rules-toc card">
                        <div class="card-header">
                            <i class="bi bi-list-ul"></i> {{ trans('rules::messages.summary') }}
                        </div>
                        <nav class="list-group list-group-flush">
                            @foreach($version->sections as $section)
                                <a class="list-group-item list-group-item-action" href="#{{ $section['anchor'] }}">{{ $section['title'] }}</a>
                            @endforeach
                            @if($versions->count() > 1)
                                <a class="list-group-item list-group-item-action text-body-secondary" href="#plugiit-rules-history"><i class="bi bi-clock-history"></i> {{ trans('rules::messages.history') }}</a>
                            @endif
                        </nav>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <span class="badge text-bg-primary fs-6">{{ trans('rules::messages.version', ['version' => $version->version]) }}</span>
                                <span class="text-body-secondary small">{{ trans('rules::messages.published_on', ['date' => format_date($version->published_at)]) }}</span>
                            </div>

                            @foreach($version->sections as $section)
                                <section class="plugiit-rules-section" id="{{ $section['anchor'] }}">
                                    <h2 class="h4">
                                        <a href="#{{ $section['anchor'] }}" class="plugiit-rules-anchor text-reset">{{ $section['title'] }}</a>
                                    </h2>
                                    <div class="plugiit-rules-content">{!! $section['content'] !!}</div>
                                </section>
                            @endforeach
                        </div>
                    </div>

                    @if($versions->count() > 1)
                        <div class="card" id="plugiit-rules-history">
                            <div class="card-header"><i class="bi bi-clock-history"></i> {{ trans('rules::messages.history') }}</div>
                            <ul class="list-group list-group-flush">
                                @foreach($versions as $item)
                                    <li class="list-group-item">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div>
                                                <strong>{{ trans('rules::messages.version', ['version' => $item->version]) }}</strong>
                                                <span class="text-body-secondary small">— {{ format_date($item->published_at) }}</span>
                                                @if($loop->first)
                                                    <span class="badge text-bg-success">{{ trans('rules::messages.current') }}</span>
                                                @endif
                                                @if($item->requires_acceptance && ! $loop->last)
                                                    <span class="badge text-bg-secondary">{{ trans('rules::messages.requires_acceptance') }}</span>
                                                @endif
                                            </div>
                                            @unless($item->is($version))
                                                <a href="{{ $loop->first ? route('rules.index') : route('rules.versions.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ trans('rules::messages.see') }}</a>
                                            @endunless
                                        </div>
                                        @if($item->summary)
                                            <p class="mb-1 mt-1">{{ $item->summary }}</p>
                                        @endif
                                        @include('rules::_diff', [
                                            'diff' => $diffs[$item->id],
                                            'emptyText' => $loop->last ? trans('rules::messages.first_version') : null,
                                        ])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
