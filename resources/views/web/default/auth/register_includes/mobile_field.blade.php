<div class="row">
    <div class="col-5">
        <div class="form-group">
            <label class="input-label" for="mobile">
                Phone Number: <span style="color: red;">*</span>
            </label>
            <select name="country_code" class="form-control select2" required id="country_code_id">
                @foreach (getCountriesMobileCode() as $country => $code)
                    <option value="{{ $code }}" @if ($code == old('country_code')) selected @endif>
                        {{ $country }}</option>
                @endforeach
            </select>

            @error('mobile')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="col-7">
        <div class="form-group">
            <label class="input-label" for="mobile">
                @if (!empty($optional))
                    ({{ trans('public.optional') }})
                @endif
            </label>
            <input name="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror"
                value="{{ old('mobile') }}" id="mobile" aria-describedby="mobileHelp"
                {{ empty($optional) ? 'required' : '' }}>

            <small id="mobileHelp" class="form-text text-muted">
                <span id="nigeriaMessage" style="color: grey;">
                    For Nigerian numbers, enter your number without the first 0 (e.g 8023456789).
                </span>
            </small>

            @error('mobile')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>