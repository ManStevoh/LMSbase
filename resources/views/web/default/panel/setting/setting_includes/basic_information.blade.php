<section>
    <h2 class="section-title after-line">{{ trans('financial.account') }}</h2>

    <div class="row mt-20">
        <div class="col-12 col-lg-4">
            <div class="form-group">
                <label class="input-label">{{ trans('public.email') }}</label>
                <input type="text" name="email"
                    value="{{ (!empty($user) and empty($new_user)) ? $user->email : old('email') }}"
                    class="form-control @error('email')  is-invalid @enderror" placeholder="" />
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Original name field commented out as it's replaced by first/middle/last name fields -->
            <!-- <div class="form-group">
                <label class="input-label">{{ trans('auth.name') }}</label>
                <input type="text" name="full_name" value="{{ (!empty($user) and empty($new_user)) ? $user->full_name : old('full_name') }}" class="form-control @error('full_name')  is-invalid @enderror" placeholder="" />
                @error('full_name')
    <div class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </div>
@enderror
            </div> -->

            <div class="row">
                <!-- First Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label" for="full_name">First Name<span style="color: red;">*</span></label>
                        <input name="full_name" type="text"
                            value="{{ (!empty($user) and empty($new_user)) ? $user->full_name : old('full_name') }}"
                            class="form-control @error('full_name') is-invalid @enderror" placeholder="">
                        @error('full_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Middle Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label" for="middle_name">Middle Name<span
                                style="color: red;">*</span></label>
                        <input name="middle_name" type="text"
                            value="{{ (!empty($user) and empty($new_user)) ? $user->middle_name : old('middle_name') }}"
                            class="form-control @error('middle_name') is-invalid @enderror" placeholder="">
                        @error('middle_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label" for="last_name">Last Name<span style="color: red;">*</span></label>
                        <input name="last_name" type="text"
                            value="{{ (!empty($user) and empty($new_user)) ? $user->last_name : old('last_name') }}"
                            class="form-control @error('last_name') is-invalid @enderror" placeholder="">
                        @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('auth.password') }}</label>
                <input type="password" name="password" value="{{ old('password') }}"
                    class="form-control @error('password')  is-invalid @enderror" placeholder="" />
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('auth.password_repeat') }}</label>
                <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}"
                    class="form-control @error('password_confirmation')  is-invalid @enderror" placeholder="" />
                @error('password_confirmation')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('public.mobile') }}</label>
                <input type="tel" name="mobile"
                    value="{{ (!empty($user) and empty($new_user)) ? $user->mobile : old('mobile') }}"
                    class="form-control @error('mobile')  is-invalid @enderror" placeholder="" />
                @error('mobile')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            @if (config('settings.use_gender_field') == 'yes')
                <!-- Add gender field matching the registration page -->
                <div class="form-group">
                    <label class="input-label" for="gender">Gender<span style="color: red;">*</span></label>
                    <select name="gender" class="form-control select2 @error('gender') is-invalid @enderror"
                        id="gender">
                        <option value="" disabled {{ empty($user->gender) ? 'selected' : '' }}>select an option
                        </option>
                        <option value="male" {{ !empty($user) && $user->gender == 'male' ? 'selected' : '' }}>Male
                        </option>
                        <option value="female" {{ !empty($user) && $user->gender == 'female' ? 'selected' : '' }}>
                            Female
                        </option>
                        <!-- <option value="other">{{ trans('public.other') }}</option> -->
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            @if (config('settings.use_age_field') == 'yes')
                <div class="form-group">
                    <label class="input-label">Age</label>
                    <input type="text" name="age"
                        value="{{ (!empty($user) and empty($new_user)) ? $user->age : old('age') }}"
                        class="form-control @error('age')  is-invalid @enderror" placeholder="" />
                    @error('age')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            <!-- Change Nationality to match registration form structure -->
            <div class="form-group">
                <label class="input-label">Nationality</label>
                <input type="text" name="nationality"
                    value="{{ (!empty($user) and empty($new_user)) ? $user->nationality : old('nationality') }}"
                    class="form-control @error('nationality')  is-invalid @enderror" placeholder="" />
                @error('nationality')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            @if (config('settings.use_country_field') == 'yes')
                <!-- Country of Residence -->
                <div class="form-group">
                    <label class="input-label" for="country">Country of Residence<span
                            style="color: red;">*</span></label>
                    <select name="country" class="form-control select2 @error('country') is-invalid @enderror"
                        id="country">
                        <option value="" disabled {{ empty($user->country) ? 'selected' : '' }}>Select an option
                        </option>
                        @foreach (getListOfCountries() as $country)
                            <option value="{{ $country }}"
                                {{ !empty($user) && $user->country == $country ? 'selected' : '' }}>
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
                <!-- State field - only shows for Nigeria -->
                <div class="form-group" id="state-wrapper">
                    <label class="input-label">State of Origin<span style="color: red;">*</span></label>

                    <select name="state" class="form-control select2 @error('state') is-invalid @enderror"
                        id="state">
                        <option value="" disabled selected>Select an option</option>
                        @foreach (getListOfStates() as $state)
                            <option value="{{ $state }}"
                                {{ !empty($user) && $user->state == $state ? 'selected' : '' }}>
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
                <div class="form-group" id="lga-wrapper" style="display: none;">
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
                                    <option value="{{ $lga }}" {{ $user->lga == $lga ? 'selected' : '' }}>
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
                <!-- State of MSME -->
                <div class="form-group" id="msme-wrapper">
                    <label class="input-label" for="msme_sector">MSME Sector<span
                            class="required-asterisk">*</span></label>
                    <select name="msme_sector"
                        class="form-control select2 @error('msme_sector') is-invalid @enderror" id="msme_sector">
                        <option value="" disabled selected>Select an option</option>
                        @foreach (getMsmeSector() as $sector)
                            <option value="{{ $msme_sector }}"
                                {{ !empty($user) && $user->msme_sector == $sector ? 'selected' : '' }}>
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

            @if (!empty($userLanguages))
                <div class="form-group">
                    <label class="input-label">{{ trans('auth.language') }}</label>
                    <select name="language" class="form-control">
                        <option value="">{{ trans('auth.language') }}</option>
                        @foreach ($userLanguages as $lang => $language)
                            <option value="{{ $lang }}" @if (!empty($user) and mb_strtolower($user->language) == mb_strtolower($lang)) selected @endif>
                                {{ $language }}</option>
                        @endforeach
                    </select>
                    @error('language')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <label class="input-label">{{ trans('update.timezone') }}</label>
                <select name="timezone" class="form-control select2" data-allow-clear="false">
                    <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>
                        {{ trans('public.select') }}</option>
                    @foreach (getListOfTimezones() as $timezone)
                        <option value="{{ $timezone }}" @if (!empty($user) and $user->timezone == $timezone) selected @endif>
                            {{ $timezone }}</option>
                    @endforeach
                </select>
                @error('timezone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            @if (!empty($currencies) and count($currencies))
                @php
                    $userCurrency = currency();
                @endphp

                <div class="form-group">
                    <label class="input-label">{{ trans('update.currency') }}</label>
                    <select name="currency" class="form-control select2" data-allow-clear="false">
                        @foreach ($currencies as $currencyItem)
                            <option value="{{ $currencyItem->currency }}"
                                {{ $userCurrency == $currencyItem->currency ? 'selected' : '' }}>
                                {{ currenciesLists($currencyItem->currency) }}
                                ({{ currencySign($currencyItem->currency) }})
                            </option>
                        @endforeach
                    </select>
                    @error('currency')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label"
                    for="newsletterSwitch">{{ trans('auth.join_newsletter') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="join_newsletter" class="custom-control-input" id="newsletterSwitch"
                        {{ (!empty($user) and $user->newsletter) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="newsletterSwitch"></label>
                </div>
            </div>

            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label"
                    for="publicMessagesSwitch">{{ trans('auth.public_messages') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="public_messages" class="custom-control-input"
                        id="publicMessagesSwitch" {{ (!empty($user) and $user->public_message) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="publicMessagesSwitch"></label>
                </div>
            </div>
        </div>
    </div>

    <!-- Add JavaScript to handle the dynamic form fields (country, state, LGA) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2 if it's being used
            if ($.fn.select2) {
                $('.select2').select2();
            }

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
                lgaSelect.empty().append('<option value="" disabled>Select an LGA</option>');

                // Use the statesAndLgas object to get LGAs for the selected state
                const statesAndLgas = @json(getNigeriaStatesAndLGAs());
                const lgas = statesAndLgas[selectedState] || [];

                // Populate the LGA dropdown with the corresponding LGAs
                if (lgas.length > 0) {
                    lgas.forEach(function(lga) {
                        const isSelected = lga === "{{ $user->lga }}" ? 'selected' : '';
                        lgaSelect.append(`<option value="${lga}" ${isSelected}>${lga}</option>`);
                    });

                    lgaWrapper.show();
                    lgaSelect.trigger('change.select2');
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
        });
    </script>

</section>
