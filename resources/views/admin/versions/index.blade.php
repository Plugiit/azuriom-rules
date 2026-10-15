@extends('admin.layouts.admin')

@section('title', trans('rules::admin.versions.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            @if($versions->isEmpty())
                <p class="text-body-secondary">{{ trans('rules::admin.versions.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ trans('rules::admin.versions.fields.version') }}</th>
                            <th scope="col">{{ trans('rules::admin.versions.fields.date') }}</th>
                            <th scope="col">{{ trans('rules::admin.versions.changes') }}</th>
                            <th scope="col">{{ trans('rules::admin.versions.acceptances') }}</th>
                            <th scope="col">{{ trans('messages.fields.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($versions as $version)
                            <tr>
                                <td>
                                    <strong>{{ $version->version }}</strong>
                                    @if($loop->first)
                                        <span class="badge text-bg-success">{{ trans('rules::admin.versions.current') }}</span>
                                    @endif
                                    @if($version->requires_acceptance)
                                        <i class="bi bi-person-check text-primary" title="{{ trans('rules::admin.versions.fields.requires_acceptance') }}" data-bs-toggle="tooltip"></i>
                                    @endif
                                </td>
                                <td>{{ format_date($version->published_at, true) }}<br><small class="text-body-secondary">{{ $version->author?->name }}</small></td>
                                <td>
                                    @if($version->summary)
                                        <div class="small mb-1">{{ Str::limit($version->summary, 140) }}</div>
                                    @endif
                                    @include('rules::_diff', ['diff' => $diffs[$version->id], 'emptyText' => $loop->last ? trans('rules::messages.first_version') : null])
                                </td>
                                <td>{{ $version->acceptances_count }}</td>
                                <td>
                                    <a href="{{ route('rules.admin.versions.show', $version) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> {{ trans('messages.actions.show') }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <a href="{{ route('rules.admin.versions.create') }}" class="btn btn-primary"><i class="bi bi-send-check"></i> {{ trans('rules::admin.versions.create') }}</a>
        </div>
    </div>
@endsection
