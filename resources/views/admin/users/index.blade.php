@extends('admin.layouts.admin')

@section('title', trans('rules::admin.users.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form class="row g-2" action="{{ route('rules.admin.users.index') }}" method="GET">
                <div class="col-md-9">
                    <input type="search" class="form-control" name="search" value="{{ $search }}" placeholder="{{ trans('rules::admin.users.search') }}" required>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> {{ trans('messages.actions.search') }}</button>
                </div>
            </form>

            @if($search !== '' && $user === null)
                <div class="mt-3">
                    @if($users->isEmpty())
                        <p class="text-body-secondary mb-0">{{ trans('rules::admin.users.no_results') }}</p>
                    @else
                        <p class="mb-1">{{ trans('rules::admin.users.results') }}</p>
                        <div class="list-group">
                            @foreach($users as $result)
                                <a href="{{ route('rules.admin.users.index', ['user' => $result->id]) }}" class="list-group-item list-group-item-action">
                                    <img src="{{ $result->getAvatar(24) }}" alt="" width="24" height="24" class="rounded me-1"> {{ $result->name }}
                                    <small class="text-body-secondary">{{ $result->email }}</small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($user)
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <img src="{{ $user->getAvatar(32) }}" alt="" width="32" height="32" class="rounded me-1"> {{ $user->name }}
                </h5>
                @can('admin.users')
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-person"></i></a>
                @endcan
            </div>
            <div class="card-body">
                @include('rules::admin.users._acceptances', ['accepted' => $hasAccepted])
                <p class="form-text mt-3 mb-0"><i class="bi bi-shield-lock"></i> {{ trans('rules::admin.users.ip_help') }}</p>
            </div>
        </div>
    @endif
@endsection
