@csrf

@include('admin.elements.editor')

<div class="mb-3">
    <label class="form-label" for="titleInput">{{ trans('rules::admin.sections.fields.title') }}</label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="titleInput" name="title" value="{{ old('title', $section->title ?? '') }}" maxlength="150" required>
    @error('title')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label" for="contentArea">{{ trans('rules::admin.sections.fields.content') }}</label>
    <textarea class="form-control html-editor @error('content') is-invalid @enderror" id="contentArea" name="content" rows="8">{{ old('content', $section->content ?? '') }}</textarea>
    @error('content')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>
