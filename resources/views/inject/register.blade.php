<div class="mb-3 plugiit-rules-register">
    <div class="form-check">
        <input class="form-check-input @error('rules_accept') is-invalid @enderror" type="checkbox" name="rules_accept" id="plugiitRulesAccept" value="1" required @checked(old('rules_accept'))>
        <label class="form-check-label" for="plugiitRulesAccept">
            {!! trans('rules::messages.register.checkbox', ['url' => e(route('rules.index'))]) !!}
        </label>
        @error('rules_accept')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
