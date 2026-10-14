<div class="mb-3 plugiit-rules-register">
    <div class="form-check">
        <input class="form-check-input @error('plugiit_rules_accept') is-invalid @enderror" type="checkbox" name="plugiit_rules_accept" id="plugiitRulesAccept" value="1" required @checked(old('plugiit_rules_accept'))>
        <label class="form-check-label" for="plugiitRulesAccept">
            {!! trans('plugiit-rules::messages.register.checkbox', ['url' => e(route('plugiit-rules.index'))]) !!}
        </label>
        @error('plugiit_rules_accept')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
