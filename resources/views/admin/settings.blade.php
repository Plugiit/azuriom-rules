@extends('admin.layouts.admin')

@section('title', trans('plugiit-rules::admin.settings.title'))

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('plugiit-rules.admin.settings.save') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ trans('plugiit-rules::admin.settings.mode') }}</label>
                    @foreach(\Azuriom\Plugin\PlugiitRules\Support\RulesManager::MODES as $value)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" id="mode-{{ $value }}" value="{{ $value }}" @checked(old('mode', $mode) === $value)>
                            <label class="form-check-label" for="mode-{{ $value }}">{{ trans('plugiit-rules::admin.settings.modes.'.$value) }}</label>
                        </div>
                    @endforeach
                    @error('groups.*')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    @error('mode')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="blockSwitch" name="block" aria-describedby="blockHelp" @checked(old('block', $block))>
                    <label class="form-check-label fw-semibold" for="blockSwitch">{{ trans('plugiit-rules::admin.settings.block') }}</label>
                    <div id="blockHelp" class="form-text">{{ trans('plugiit-rules::admin.settings.block_help') }}</div>
                </div>

                <div class="card card-body mb-4">
                    @foreach(array_keys(\Azuriom\Plugin\PlugiitRules\Support\RulesManager::ACTION_GROUPS) as $group)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="groups[]" value="{{ $group }}" id="group-{{ $group }}" @checked(in_array($group, old('groups', $groups), true))>
                            <label class="form-check-label" for="group-{{ $group }}">{{ trans('plugiit-rules::admin.settings.groups.'.$group) }}</label>
                        </div>
                    @endforeach

                    <label class="form-label mt-3" for="routesInput">{{ trans('plugiit-rules::admin.settings.routes') }}</label>
                    <textarea class="form-control font-monospace @error('routes') is-invalid @enderror" id="routesInput" name="routes" rows="3">{{ old('routes', implode("\n", $customPatterns)) }}</textarea>
                    <div class="form-text">{{ trans('plugiit-rules::admin.settings.routes_help') }}</div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="registerSwitch" name="register" aria-describedby="registerHelp" @checked(old('register', $register))>
                    <label class="form-check-label fw-semibold" for="registerSwitch">{{ trans('plugiit-rules::admin.settings.register') }}</label>
                    <div id="registerHelp" class="form-text">{{ trans('plugiit-rules::admin.settings.register_help') }}</div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
