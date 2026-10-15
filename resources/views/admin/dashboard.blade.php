@extends('admin.layouts.admin')

@section('title', trans('rules::admin.dashboard.title'))

@section('content')
    @if($current === null)
        <div class="alert alert-info"><i class="bi bi-info-circle"></i> {{ trans('rules::admin.dashboard.not_published') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="text-body-secondary small text-uppercase">{{ trans('rules::admin.dashboard.current') }}</div>
                    <div class="h3 mb-0">{{ $current?->version ?? trans('rules::admin.dashboard.none') }}</div>
                    @if($current)
                        <small class="text-body-secondary">{{ format_date($current->published_at, true) }}</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="text-body-secondary small text-uppercase">{{ trans('rules::admin.dashboard.required') }}</div>
                    <div class="h3 mb-0">{{ $required?->version ?? trans('rules::admin.dashboard.none') }}</div>
                    <small class="text-body-secondary">{{ trans('rules::admin.dashboard.mode') }} : {{ trans('rules::admin.settings.modes.'.$mode) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="text-body-secondary small text-uppercase">{{ trans('rules::admin.dashboard.accepted') }}</div>
                    <div class="h3 mb-1">{{ $percent }} %</div>
                    <div class="progress mb-1" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="height: .5rem">
                        <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                    </div>
                    @if($required)
                        <small class="text-body-secondary">{{ trans('rules::admin.dashboard.accepted_help', ['accepted' => $accepted, 'total' => $totalUsers, 'version' => $required->version]) }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">{{ trans('rules::admin.versions.draft_changes') }}</h5></div>
                <div class="card-body">
                    @if($hasDraftChanges)
                        <div class="alert alert-warning py-2"><i class="bi bi-pencil"></i> {{ trans('rules::admin.dashboard.draft_changes') }}</div>
                        @include('rules::_diff', ['diff' => $draftDiff])
                    @else
                        <p class="text-body-secondary"><i class="bi bi-check2"></i> {{ trans('rules::admin.dashboard.draft_clean') }}</p>
                    @endif

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a href="{{ route('rules.admin.sections.index') }}" class="btn btn-outline-primary"><i class="bi bi-pencil-square"></i> {{ trans('rules::admin.dashboard.edit_sections') }}</a>
                        <a href="{{ route('rules.admin.versions.create') }}" class="btn btn-primary"><i class="bi bi-send-check"></i> {{ trans('rules::admin.dashboard.publish') }}</a>
                    </div>

                    <hr>
                    <p class="mb-0 small">
                        <strong>{{ trans('rules::admin.dashboard.blocked') }} :</strong>
                        @forelse($blockedPatterns as $pattern)
                            <code>{{ $pattern }}</code>@if(! $loop->last), @endif
                        @empty
                            <span class="text-body-secondary">{{ trans('rules::admin.dashboard.nothing_blocked') }}</span>
                        @endforelse
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">{{ trans('rules::admin.dashboard.recent') }}</h5></div>
                @if($recent->isEmpty())
                    <div class="card-body text-body-secondary">{{ trans('rules::admin.dashboard.no_recent') }}</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recent as $acceptance)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('rules.admin.users.index', ['user' => $acceptance->user_id]) }}">
                                    <img src="{{ $acceptance->user?->getAvatar(24) }}" alt="" width="24" height="24" class="rounded me-1">
                                    {{ $acceptance->user?->name ?? '?' }}
                                </a>
                                <span class="small text-body-secondary">
                                    v{{ $acceptance->version?->version }} · {{ format_date($acceptance->created_at, true) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
