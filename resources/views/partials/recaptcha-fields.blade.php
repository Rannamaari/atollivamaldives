<input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>
<input type="hidden" name="recaptcha_action" value="{{ $action }}" data-recaptcha-action>
<div style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">
    <label>
        Leave this field empty
        <input type="text" name="website" tabindex="-1" autocomplete="off">
    </label>
</div>
@if(config('services.recaptcha.enabled') && filled(config('services.recaptcha.site_key')))
    <p class="recaptcha-disclosure">
        This site is protected by reCAPTCHA and the Google
        <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy Policy</a>
        and
        <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a>
        apply.
    </p>
@endif
