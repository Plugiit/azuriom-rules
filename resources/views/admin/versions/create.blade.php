@extends('admin.layouts.admin')

@section('title', trans('rules::admin.versions.create'))

@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('rules.admin.versions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="versionInput">{{ trans('rules::admin.versions.fields.version') }}</label>
                            <input type="text" class="form-control @error('version') is-invalid @enderror" id="versionInput" name="version" value="{{ old('version', $suggested) }}" maxlength="20" required>
                            @error('version')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="summaryInput">{{ trans('rules::admin.versions.fields.summary') }}</label>
                            <textarea class="form-control @error('summary') is-invalid @enderror" id="summaryInput" name="summary" rows="3" maxlength="2000">{{ old('summary') }}</textarea>
                            @error('summary')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="requiresSwitch" name="requires_acceptance" aria-describedby="requiresHelp" @checked(old('requires_acceptance', true))>
                            <label class="form-check-label" for="requiresSwitch">{{ trans('rules::admin.versions.fields.requires_acceptance') }}</label>
                            <div id="requiresHelp" class="form-text">{{ trans('rules::admin.versions.fields.requires_acceptance_help') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary" @disabled($sectionsCount === 0)>
                            <i class="bi bi-send-check"></i> {{ trans('rules::admin.versions.create') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">{{ trans('rules::admin.versions.draft_changes') }}</h5></div>
                <div class="card-body">
                    @if($sectionsCount === 0)
                        <div class="alert alert-warning mb-0">{{ trans('rules::admin.versions.no_sections') }}</div>
                    @else
                        @include('rules::_diff', ['diff' => $diff, 'emptyText' => trans('rules::admin.versions.no_draft_changes')])
                    @endif
                    <a href="{{ route('rules.admin.sections.index') }}" class="btn btn-sm btn-outline-primary mt-3"><i class="bi bi-pencil-square"></i> {{ trans('rules::admin.dashboard.edit_sections') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
