@extends(getTemplate().'.layouts.app')

@section('content')
    <div class="container">
        <div class="row login-container">
            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('verification') }}" class="img-cover" alt="Login">
            </div>

            <div class="col-12 col-md-6">
                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('auth.account_verification') }}</h1>

                    <p>{{ trans('auth.account_verification_hint',['username' => $username]) }}</p>
                    <form method="post" action="/verification" class="mt-35">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="username" value="{{ $usernameValue }}">

                        <div class="form-group">
                            <label class="input-label" for="code">{{ trans('auth.code') }}:</label>
                            <input type="tel" name="code" class="form-control @error('code') is-invalid @enderror" id="code"
                                   aria-describedby="codeHelp">
                            @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.verification') }}</button>
                    </form>

                    <div class="text-center mt-20">
                        <span class="text-secondary">
                            <a href="/verification/resend" id="resendButton" class="font-weight-bold">{{ trans('auth.resend_code') }}</a>
                            <span id="timerDisplay" class="ml-2 text-muted"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const resendButton = document.getElementById('resendButton');
    const timerDisplay = document.getElementById('timerDisplay');
    const cooldownPeriod = 15; // Cooldown period in seconds (adjust as needed)
    let isFirstClick = true; // Track if this is the first click
    let timerInterval; // Variable to store the interval ID

    // Function to handle the resend action
    async function handleResend(event) {
        // For the first click, let the normal href navigation happen
        if (isFirstClick) {
            isFirstClick = false; // Mark that first click has happened

            // Start the cooldown timer after a slight delay to allow the request to process
            setTimeout(() => {
                // Change the href to void for future clicks
                resendButton.href = 'javascript:void(0)';

                // Disable the button immediately
                resendButton.style.pointerEvents = 'none';
                resendButton.style.opacity = '0.5';

                // Start the countdown timer
                startCooldownTimer();
            }, 100);

            // Don't prevent default - let the first click go through normally
            return true;
        }

        // For subsequent clicks, handle with AJAX
        event.preventDefault();

        try {
            // Disable the button immediately
            resendButton.style.pointerEvents = 'none';
            resendButton.style.opacity = '0.5';

            // Make the AJAX request to resend the code
            const response = await fetch('/verification/resend', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                // Start the countdown timer
                startCooldownTimer();
            } else {
                // If there's an error, re-enable the button
                resendButton.style.pointerEvents = 'auto';
                resendButton.style.opacity = '1';
                alert('Failed to resend code. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            resendButton.style.pointerEvents = 'auto';
            resendButton.style.opacity = '1';
            alert('An error occurred. Please try again.');
        }
    }

    // Function to start the cooldown timer
    function startCooldownTimer() {
        let timeLeft = cooldownPeriod;

        // Update the timer display immediately
        timerDisplay.textContent = `(${timeLeft}s)`;

        // Start the countdown
        timerInterval = setInterval(() => {
            timeLeft--;
            timerDisplay.textContent = `(${timeLeft}s)`;

            if (timeLeft <= 0) {
                // Clear the interval
                clearInterval(timerInterval);

                // Reset the button state
                resendButton.style.pointerEvents = 'auto';
                resendButton.style.opacity = '1';
                timerDisplay.textContent = '';
            }
        }, 1000);
    }

    // Add click event listener to the resend button
    resendButton.addEventListener('click', handleResend);
});</script>
