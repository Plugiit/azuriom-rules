@extends('admin.layouts.admin')

@section('title', trans('plugiit-rules::admin.sections.edit', ['title' => $section->title]))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('plugiit-rules.admin.sections.update', $section) }}" method="POST">
                @method('PUT')

                @include('plugiit-rules::admin.sections._form')

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>

                <a href="{{ route('plugiit-rules.admin.sections.destroy', $section) }}" class="btn btn-danger" data-confirm="delete">
                    <i class="bi bi-trash"></i> {{ trans('messages.actions.delete') }}
                </a>
            </form>
        </div>
    </div>
@endsection
