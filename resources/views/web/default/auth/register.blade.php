@extends(getTemplate() . '.layouts.app')

@push('styles_top')
<link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
<link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
<style>
    .required-asterisk {
        color: red;
        font-weight: bold;
        margin-left: 2px;
    }
    .captcha-img {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 3px;
    }
    .refresh-captcha {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
@php
$registerMethod = getGeneralSettings('register_method') ?? 'mobile';
$showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
$showCertificateAdditionalInRegister = getFeaturesSettings('show_certificate_additional_in_register') ?? false;
$selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;
@endphp

<div class="container">
    <div class="row login-container">
        <div class="col-12 col-md-6 pl-0">
            <img src="{{ getPageBackgroundSettings('register') }}" class="img-cover" alt="Login">
        </div>
        <div class="col-12 col-md-6">
            <div class="login-card">
                <h1 class="font-20 font-weight-bold">{{ trans('auth.signup') }}</h1>

                <form method="post" action="/register" class="mt-35">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    @if (!empty($selectRolesDuringRegistration) and count($selectRolesDuringRegistration))
                    @php
                    $oldAccountType = old('account_type');
                    @endphp

                    <div class="form-group">
                        <label class="input-label">{{ trans('financial.account_type') }}</label>
                        <div class="d-flex align-items-center wizard-custom-radio mt-5">
                            <div class="wizard-custom-radio-item flex-grow-1">
                                <input type="radio" name="account_type" value="user" id="role_user"
                                    class=""
                                    {{ (empty($oldAccountType) or $oldAccountType == 'user') ? 'checked' : '' }}>
                                <label class="font-12 cursor-pointer px-15 py-10"
                                    for="role_user">{{ trans('update.role_user') }}</label>
                            </div>
                            @foreach ($selectRolesDuringRegistration as $selectRole)
                            <div class="wizard-custom-radio-item flex-grow-1">
                                <input type="radio" name="account_type" value="{{ $selectRole }}"
                                    id="role_{{ $selectRole }}" class=""
                                    {{ $oldAccountType == $selectRole ? 'checked' : '' }}>
                                <label class="font-12 cursor-pointer px-15 py-10"
                                    for="role_{{ $selectRole }}">{{ trans('update.role_' . $selectRole) }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- First Name -->
                    <div class="form-group">
                        <label class="input-label" for="full_name">First Name<span class="required-asterisk">*</span></label>
                        <input name="full_name" type="text" value="{{ old('full_name') }}"
                            class="form-control @error('full_name') is-invalid @enderror" id="full_name">
                        @error('full_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div class="form-group">
                        <label class="input-label" for="middle_name">Middle Name</label>
                        <input name="middle_name" type="text" value="{{ old('middle_name') }}"
                            class="form-control @error('middle_name') is-invalid @enderror" id="middle_name">
                        @error('middle_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label class="input-label" for="last_name">Last Name<span class="required-asterisk">*</span></label>
                        <input name="last_name" type="text" value="{{ old('last_name') }}"
                            class="form-control @error('last_name') is-invalid @enderror" id="last_name">
                        @error('last_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    @if ($registerMethod == 'mobile')
                    @include('web.default.auth.register_includes.mobile_field')

                    @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.email_field', [
                    'optional' => true,
                    ])
                    @endif
                    @else
                    @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.mobile_field', [
                    'optional' => true,
                    ])
                    @endif
                    @endif

                    <!-- Email -->
                    <div class="form-group">
                        <label class="input-label" for="email">{{ trans('auth.email') }}<span class="required-asterisk">*</span></label>
                        <input name="email" type="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" id="email">
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="input-label" for="password">{{ trans('auth.password') }}<span class="required-asterisk">*</span></label>
                        <input name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" id="password">
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="input-label" for="confirm_password">{{ trans('auth.retype_password') }}<span class="required-asterisk">*</span></label>
                        <input name="password_confirmation" type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="confirm_password">
                        @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    @if ($showCertificateAdditionalInRegister)
                    <div class="form-group">
                        <label class="input-label"
                            for="certificate_additional">{{ trans('update.certificate_additional') }}<span
                                class="required-asterisk">*</span></label>
                        <input name="certificate_additional" id="certificate_additional"
                            class="form-control @error('certificate_additional') is-invalid @enderror" />
                        @error('certificate_additional')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (!empty($referralSettings) and $referralSettings['status'])
                    <div class="form-group">
                        <label class="input-label" for="referral_code">Referral Code:</label>
                        <input name="referral_code" type="text"
                            class="form-control @error('referral_code') is-invalid @enderror" id="referral_code"
                            value="{{ !empty($referralCode) ? $referralCode : old('referral_code') }}">
                        @error('referral_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (config('settings.use_age_field') == 'yes')
                    @if (config('settings.use_custom_age_field') == 'yes')
                    <div class="form-group">
                        <label class="input-label" for="age">Age:</label>
                        <select name="age"
                            class="form-control select2 @error('gender') is-invalid @enderror" id="age_new">
                            <option value="" disabled selected>select an option</option>
                            <option value="Above 35">Above 35</option>
                            <option value="Below 35">Below 35</option>
                        </select>
                        @error('age')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @else
                    <div class="form-group">
                        <label class="input-label" for="age">Age:</label>
                        <input name="age" type="text"
                            class="form-control @error('age') is-invalid @enderror" id="age"
                            value="{{ !empty($age) ? $age : old('age') }}">
                        @error('age')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif
                    @endif

                    @if (config('settings.use_phonenumber') == 'yes')
                    @include('web.default.auth.register_includes.mobile_field')
                    @endif

                    @if (config('settings.use_gender_field') == 'yes')
                    <!-- Gender -->
                    <div class="form-group">
                        <label class="input-label" for="gender">Gender<span class="required-asterisk">*</span></label>
                        <select name="gender" class="form-control select2 @error('gender') is-invalid @enderror"
                            id="gender">
                            <option value="" disabled selected>select an option</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (config('settings.use_country_field') == 'yes')
                    <!-- Country of Residence -->
                    <div class="form-group">
                        <label class="input-label" for="country">Country of Residence<span class="required-asterisk">*</span></label>
                        <select name="country"
                            class="form-control select2 @error('country') is-invalid @enderror" id="country">
                            @foreach (getListOfCountries() as $country)
                            <option value="{{ $country }}"
                                {{ old('country') == $country || $country == 'Nigeria' ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                            @endforeach
                        </select>
                        @error('country')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (config('settings.use_state_field') == 'yes')
                    <!-- State of Origin -->
                    <div class="form-group" id="state-wrapper" style="display: none;">
                        <label class="input-label" for="state">State of Origin<span class="required-asterisk">*</span></label>
                        <select name="state" class="form-control select2 @error('state') is-invalid @enderror"
                            id="state">
                            <option value="" disabled selected>Select an option</option>
                            @foreach (getListOfStates() as $state)
                            <option value="{{ $state }}"
                                {{ old('state') == $state ? 'selected' : '' }}>
                                {{ $state }}
                            </option>
                            @endforeach
                        </select>
                        @error('state')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (config('settings.use_lga_field') == 'yes')
                    <!-- LGA -->
                    <div class="form-group" id="lga-wrapper">
                        <label class="input-label" for="lga">LGA<span class="required-asterisk">*</span></label>
                        <select name="lga" class="form-control select2 @error('lga') is-invalid @enderror"
                            id="lga">
                            <option value="" disabled selected>Select an option</option>
                            @php
                            $statesAndLgas = getNigeriaStatesAndLGAs();
                            @endphp
                            @foreach ($statesAndLgas as $state => $lgas)
                            <optgroup label="{{ $state }}" style="display:none;">
                                @foreach ($lgas as $lga)
                                <option value="{{ $lga }}"
                                    {{ old('lga') == $lga ? 'selected' : '' }}>
                                    {{ $lga }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        @error('lga')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (config('settings.use_msme_sector') == 'yes')
                    <!-- MSME Sector -->
                    <div class="form-group" id="msme-wrapper">
                        <label class="input-label" for="state">MSME Sector<span class="required-asterisk">*</span></label>
                        <select name="msme_sector"
                            class="form-control select2 @error('msme_sector') is-invalid @enderror"
                            id="msme_sector">
                            <option value="" disabled selected>Select an option</option>
                            @foreach (getMsmeSector() as $sector)
                            <option value="{{ $sector }}"
                                {{ old('msme_sector') == $sector ? 'selected' : '' }}>
                                {{ $sector }}
                            </option>
                            @endforeach
                        </select>
                        @error('msme_sector')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    @if (getFeaturesSettings('timezone_in_register'))
                    @php
                    $selectedTimezone = getGeneralSettings('default_time_zone');
                    @endphp

                    <div class="form-group">
                        <label class="input-label">{{ trans('update.timezone') }}</label>
                        <select name="timezone" class="form-control select2" data-allow-clear="false">
                            <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>
                                {{ trans('public.select') }}
                            </option>
                            @foreach (getListOfTimezones() as $timezone)
                            <option value="{{ $timezone }}"
                                @if ($selectedTimezone==$timezone) selected @endif>{{ $timezone }}
                            </option>
                            @endforeach
                        </select>
                        @error('timezone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                   @if (!empty(getGeneralSecuritySettings('captcha_for_register')))
                    @include('web.default.includes.captcha_input')
                    @endif

                    <!-- Terms and Conditions -->
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="term" value="1"
                            {{ (!empty(old('term')) and old('term') == '1') ? 'checked' : '' }}
                            class="custom-control-input @error('term') is-invalid @enderror" id="term">
                        <label class="custom-control-label font-14"
                            for="term">{{ trans('auth.i_agree_with') }}<span style="color: red;">*</span>
                            <a href="pages/terms" target="_blank"
                                class="text-secondary font-weight-bold font-14">{{ trans('auth.terms_and_rules') }}</a>
                        </label>
                        @error('term')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.signup') }}</button>
                </form>

                <div class="text-center mt-20">
                    <span class="text-secondary">
                        {{ trans('auth.already_have_an_account') }}
                        <a href="/login" class="text-secondary font-weight-bold">{{ trans('auth.login') }}</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts_bottom')
<script src="/assets/default/vendors/select2/select2.min.js"></script>
<script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="/assets/default/js/parts/forms.min.js"></script>
<script src="/assets/default/js/parts/register.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize select2 if it's being used
        if ($.fn.select2) {
            $('.select2').select2();
        }

        // CAPTCHA refresh
        $('.refresh-captcha').on('click', function() {
            $.ajax({
                url: '/refresh-captcha',
                type: 'GET',
                success: function(data) {
                    $('.captcha-img').html(data.captcha);
                }
            });
        });

        const countrySelect = $("#country");
        const stateWrapper = $("#state-wrapper");
        const stateSelect = $("#state");
        const lgaWrapper = $("#lga-wrapper");
        const lgaSelect = $("#lga");

        // Function to update location visibility and filtering
        function updateLocationVisibility() {
            if (countrySelect.val() === "Nigeria") {
                stateWrapper.show();

                // If a state is already selected, update LGA options
                if (stateSelect.val()) {
                    updateLGAOptions(stateSelect.val());
                }
            } else {
                stateWrapper.hide();
                lgaWrapper.hide();
                stateSelect.val("").trigger('change.select2');
                lgaSelect.val("").trigger('change.select2');
            }
        }

        function updateLGAOptions(selectedState) {
            // Clear current LGA options
            lgaSelect.empty().append('<option value="" disabled selected>Select an LGA</option>');

            // Use the statesAndLgas object to get LGAs for the selected state
            const statesAndLgas = @json(getNigeriaStatesAndLGAs());
            const lgas = statesAndLgas[selectedState] || [];

            // Populate the LGA dropdown with the corresponding LGAs
            if (lgas.length > 0) {
                lgas.forEach(function(lga) {
                    lgaSelect.append(`<option value="${lga}">${lga}</option>`);
                });

                lgaWrapper.show();
                lgaSelect.val('').trigger('change.select2');
            } else {
                lgaWrapper.hide();
            }
        }

        // Country change event
        countrySelect.on("change", updateLocationVisibility);

        // State change event
        stateSelect.on("change", function() {
            const selectedState = $(this).val();

            if (selectedState) {
                updateLGAOptions(selectedState);
            } else {
                lgaWrapper.hide();
                lgaSelect.find('option:not([disabled])').remove();
            }
        });

        // Initial setup on page load
        updateLocationVisibility();

        const $countryCodeSelect = $('#country_code_id');
        const $mobileInput = $('#mobile');
        const nigeriaCode = '+234'; // Replace with the actual code for Nigeria if different

        // Function to enforce 10-digit limit for Nigerian numbers
        function enforceNigeriaMobileLimit() {
            const selectedCode = $countryCodeSelect.val();
            if (selectedCode === nigeriaCode) {
                $mobileInput.attr('maxlength', '10');
                $mobileInput.on('input', function() {
                    if ($mobileInput.val().length > 10) {
                        $mobileInput.val($mobileInput.val().slice(0, 10));
                    }
                });
            } else {
                $mobileInput.removeAttr('maxlength');
            }
        }

        // Trigger validation on country code change
        $countryCodeSelect.on('change', enforceNigeriaMobileLimit);

        // Initial validation on page load
        enforceNigeriaMobileLimit();
    });
</script>
@endpush