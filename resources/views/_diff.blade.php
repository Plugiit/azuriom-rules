{{-- Summary of the differences: $diff = ['added' => [], 'removed' => [], 'modified' => []] --}}
@php($plugiitRulesDiffEmpty = empty($diff['added']) && empty($diff['removed']) && empty($diff['modified']))

@if($plugiitRulesDiffEmpty)
    <span class="text-body-secondary small">{{ $emptyText ?? trans('plugiit-rules::messages.no_changes') }}</span>
@else
    <ul class="list-unstyled small mb-0 plugiit-rules-diff">
        @foreach(['added' => 'success', 'modified' => 'warning', 'removed' => 'danger'] as $type => $color)
            @foreach($diff[$type] as $title)
                <li>
                    <span class="badge text-bg-{{ $color }}">{{ trans('plugiit-rules::messages.diff.'.$type) }}</span>
                    {{ $title }}
                </li>
            @endforeach
        @endforeach
    </ul>
@endif
