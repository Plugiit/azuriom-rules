{{-- $acceptances, $accepted (bool), $required (Version|null) --}}
@if($required)
    <p>
        @if($accepted)
            <span class="text-success"><i class="bi bi-check-circle"></i> {{ trans('plugiit-rules::admin.users.up_to_date', ['version' => $required->version]) }}</span>
        @else
            <span class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ trans('plugiit-rules::admin.users.outdated', ['version' => $required->version]) }}</span>
        @endif
    </p>
@endif

@if($acceptances->isEmpty())
    <p class="text-body-secondary mb-0">{{ trans('plugiit-rules::admin.users.empty') }}</p>
@else
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
            <tr>
                <th>{{ trans('plugiit-rules::admin.versions.fields.version') }}</th>
                <th>{{ trans('plugiit-rules::admin.versions.fields.date') }}</th>
                <th>{{ trans('plugiit-rules::admin.versions.fields.source') }}</th>
                <th>{{ trans('plugiit-rules::admin.versions.fields.ip') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($acceptances as $acceptance)
                <tr>
                    <td><a href="{{ route('plugiit-rules.admin.versions.show', $acceptance->version_id) }}">{{ $acceptance->version?->version }}</a></td>
                    <td>{{ format_date($acceptance->created_at, true) }}</td>
                    <td>{{ trans('plugiit-rules::admin.versions.sources.'.$acceptance->source) }}</td>
                    <td><code title="{{ $acceptance->ip_hash }}">{{ $acceptance->shortIpHash() ?? '—' }}</code></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
