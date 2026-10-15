@extends('admin.layouts.admin')

@section('title', trans('rules::admin.sections.title'))

@push('footer-scripts')
    <script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
    <script>
        const plugiitRulesSections = document.getElementById('sections');

        if (plugiitRulesSections) {
            Sortable.create(plugiitRulesSections, { animation: 150, handle: '.sortable-handle' });

            const saveButton = document.getElementById('save');
            const saveButtonIcon = saveButton.querySelector('.btn-spinner');

            saveButton.addEventListener('click', function () {
                saveButton.setAttribute('disabled', '');
                saveButtonIcon.classList.remove('d-none');

                axios.post('{{ route('rules.admin.sections.update-order') }}', {
                    sections: [].slice.call(plugiitRulesSections.children).map((child) => child.dataset.id),
                }).then(function (json) {
                    createAlert('success', json.data.message, true);
                }).catch(function (error) {
                    createAlert('danger', error.response.data.message ? error.response.data.message : error, true);
                }).finally(function () {
                    saveButton.removeAttribute('disabled');
                    saveButtonIcon.classList.add('d-none');
                });
            });
        }
    </script>
@endpush

@section('content')
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> {{ trans('rules::admin.sections.info') }}</div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($sections->isEmpty())
                <p class="text-body-secondary">{{ trans('rules::admin.sections.empty') }}</p>
            @else
                <ol class="list-unstyled sortable mb-3" id="sections">
                    @foreach($sections as $section)
                        <li class="sortable-item" data-id="{{ $section->id }}">
                            <div class="card">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-arrows-move sortable-handle"></i>
                                        {{ $section->title }}
                                        <small class="text-body-secondary">— {{ Str::limit(strip_tags($section->content), 80) }}</small>
                                    </span>
                                    <span class="text-nowrap">
                                        <a href="{{ route('rules.admin.sections.edit', $section) }}" class="m-1" title="{{ trans('messages.actions.edit') }}" data-bs-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                        <a href="{{ route('rules.admin.sections.destroy', $section) }}" class="m-1" title="{{ trans('messages.actions.delete') }}" data-bs-toggle="tooltip" data-confirm="delete"><i class="bi bi-trash"></i></a>
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>

                <button type="button" class="btn btn-success" id="save">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                    <span class="spinner-border spinner-border-sm btn-spinner d-none" role="status"></span>
                </button>
            @endif

            <a class="btn btn-primary" href="{{ route('rules.admin.sections.create') }}">
                <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
            </a>
            <a class="btn btn-outline-primary" href="{{ route('rules.admin.versions.create') }}">
                <i class="bi bi-send-check"></i> {{ trans('rules::admin.dashboard.publish') }}
            </a>
        </div>
    </div>
@endsection
