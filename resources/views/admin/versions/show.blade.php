@extends('admin.layouts.admin')

@section('title', trans('rules::admin.versions.show', ['version' => $version->version]))

@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ trans('rules::admin.versions.content') }}</h5>
                    <span>
                        @if($isCurrent)
                            <span class="badge text-bg-success">{{ trans('rules::admin.versions.current') }}</span>
                        @endif
                        <a href="{{ $isCurrent ? route('rules.index') : route('rules.versions.show', $version) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i></a>
                    </span>
                </div>
                <div class="card-body">
                    @foreach($version->sections as $section)
                        <h3 class="h5">{{ $section['title'] }}</h3>
                        <div class="mb-3">{!! $section['content'] !!}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ trans('rules::admin.versions.fields.date') }}</dt>
                        <dd class="col-sm-7">{{ format_date($version->published_at, true) }}</dd>
                        <dt class="col-sm-5">{{ trans('rules::admin.versions.author') }}</dt>
                        <dd class="col-sm-7">{{ $version->author?->name ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ trans('rules::admin.versions.acceptances') }}</dt>
                        <dd class="col-sm-7">{{ $acceptances->total() }}</dd>
                        <dt class="col-sm-5">{{ trans('rules::admin.versions.fields.requires_acceptance') }}</dt>
                        <dd class="col-sm-7">{{ trans('messages.'.($version->requires_acceptance ? 'yes' : 'no')) }}</dd>
                    </dl>
                    @if($version->summary)
                        <hr>
                        <p class="mb-0" style="white-space: pre-line">{{ $version->summary }}</p>
                    @endif
                    <hr>
                    <h6>{{ trans('rules::admin.versions.changes') }}</h6>
                    @include('rules::_diff', ['diff' => $diff])
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">{{ trans('rules::admin.versions.acceptances') }}</h5></div>
                <div class="card-body">
                    @if($acceptances->isEmpty())
                        <p class="text-body-secondary mb-0">{{ trans('rules::admin.versions.no_acceptances') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                <tr>
                                    <th>{{ trans('rules::admin.versions.fields.user') }}</th>
                                    <th>{{ trans('rules::admin.versions.fields.date') }}</th>
                                    <th>{{ trans('rules::admin.versions.fields.source') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($acceptances as $acceptance)
                                    <tr>
                                        <td><a href="{{ route('rules.admin.users.index', ['user' => $acceptance->user_id]) }}">{{ $acceptance->user?->name ?? '?' }}</a></td>
                                        <td>{{ format_date($acceptance->created_at, true) }}</td>
                                        <td>{{ trans('rules::admin.versions.sources.'.$acceptance->source) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $acceptances->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
