@extends('admin.layouts.admin')

@section('title', trans('plugiit-rules::admin.sections.create'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('plugiit-rules.admin.sections.store') }}" method="POST">
                @include('plugiit-rules::admin.sections._form')

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
