# Security Setup Guide

This guide walks you through setting up the newly enhanced security features.

## 🚀 Quick Setup

### 1. Update Dependencies
```bash
composer dump-autoload
```

### 2. Get reCAPTCHA Keys
1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Create a new site
3. Choose **reCAPTCHA v2 > "I'm not a robot" Checkbox**
4. Add your domain(s)
5. Copy the Site Key and Secret Key

### 3. Configure Environment
Add to your `.env` file:
```env
# Google reCAPTCHA v2
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

### 4. Test reCAPTCHA Locally
If testing locally with reCAPTCHA, you can use test keys:
```env
# Local/Development Test Keys (don't use in production!)
RECAPTCHA_SITE_KEY=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
RECAPTCHA_SECRET_KEY=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
```

> ⚠️ **WARNING:** These test keys always pass validation! Only use them for local development.

---

## 🔐 New Security Features

### reCAPTCHA Validation
- **Login page:** Now requires reCAPTCHA verification
- **Registration page:** Now requires reCAPTCHA verification
- **Backend:** Server-side validation with Google API
- **Error handling:** Proper error messages if verification fails

**Files:**
- `app/Rules/RecaptchaV2.php` - Validation rule
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Registration with CAPTCHA
- `app/Http/Requests/Auth/LoginRequest.php` - Login with CAPTCHA

### Security Headers
All responses now include critical security headers:
- **X-Frame-Options** - Prevents clickjacking
- **X-Content-Type-Options** - Prevents MIME sniffing
- **X-XSS-Protection** - Legacy XSS protection
- **Referrer-Policy** - Privacy protection
- **Permissions-Policy** - Browser feature restrictions
- **Strict-Transport-Security** - Forces HTTPS
- **Content-Security-Policy** - XSS and injection protection

**File:**
- `app/Http/Middleware/SecurityHeaders.php` - Applied globally to all routes

### Input Sanitization Helpers
New helper functions available throughout your app:

```php
// Escape HTML for safe output
sanitize_input($userInput);

// Validate URLs
validate_url($url);

// Validate emails
validate_email($email);

// Mask sensitive data
mask_email('user@example.com');     // user@*****.com
mask_phone('08123456789');          // 08**4567 89

// And more...
```

**File:**
- `app/Helpers/SecurityHelper.php` - Helper functions

### Webhook Security
Enhanced webhook verification with service class:

```php
use App\Services\WebhookSignatureVerifier;

// Verify webhook signature
$isValid = WebhookSignatureVerifier::verifyMidtransSignature(
    $data,
    $signature,
    config('midtrans.server_key')
);

// Sanitize webhook data
$sanitized = WebhookSignatureVerifier::sanitizeWebhookData($data);
```

**File:**
- `app/Services/WebhookSignatureVerifier.php` - Webhook security

---

## ✅ Pre-Deployment Checklist

Before going live, ensure:

- [ ] reCAPTCHA keys configured in `.env`
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Database migrated to PostgreSQL/MySQL (not SQLite)
- [ ] Backup system configured
- [ ] Email service configured (for password resets)
- [ ] HTTPS/SSL certificate installed
- [ ] Security headers verified (F12 > Network > Headers)
- [ ] Rate limiting tested
- [ ] Webhook signatures tested with real Midtrans
- [ ] All dependencies updated (`composer update`)

---

## 🧪 Testing

### Test reCAPTCHA
1. Open registration or login page
2. Try submitting without checking reCAPTCHA → Should fail
3. Check reCAPTCHA → Should allow submission
4. Monitor logs for validation errors: `storage/logs/laravel.log`

### Test Security Headers
1. Open your site in Chrome/Firefox
2. Press F12 to open Developer Tools
3. Go to Network tab
4. Refresh page and click any request
5. Check Response Headers for:
   - `X-Frame-Options: SAMEORIGIN`
   - `X-Content-Type-Options: nosniff`
   - `Content-Security-Policy: ...`
   - `Strict-Transport-Security: ...` (only HTTPS)

### Test Rate Limiting
1. Try login 6 times with wrong password → Should be blocked
2. Error message should show seconds to wait
3. Check logs for rate limit events

---

## 📊 Monitoring & Logs

Check these logs regularly for security events:

```bash
# View recent logs
tail -f storage/logs/laravel.log

# Search for security events
grep -i "recaptcha" storage/logs/laravel.log
grep -i "rate.limit" storage/logs/laravel.log
grep -i "signature" storage/logs/laravel.log
grep -i "webhook" storage/logs/laravel.log
```

---

## 🔧 Troubleshooting

### reCAPTCHA Not Working
- **Issue:** "Unable to verify reCAPTCHA"
- **Solution:** Check that `RECAPTCHA_SECRET_KEY` is correct in `.env`
- **Check:** `LOG_LEVEL=debug` and review logs

### Headers Not Appearing
- **Issue:** Security headers missing in responses
- **Solution:** Ensure SecurityHeaders middleware is registered in `bootstrap/app.php`
- **Check:** Run `php artisan route:list` to verify middleware is loaded

### Webhook Signature Failing
- **Issue:** Midtrans webhooks rejected
- **Solution:** Verify `config('midtrans.server_key')` matches production
- **Check:** Enable debug logging and review webhook payloads

---

## 📚 Additional Resources

- [Google reCAPTCHA Documentation](https://developers.google.com/recaptcha)
- [OWASP Security Headers](https://owasp.org/www-project-secure-headers/)
- [Content Security Policy Guide](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
- [Laravel Security Documentation](https://laravel.com/docs/security)
- [Midtrans Webhook Documentation](https://docs.midtrans.com/en/after-payment/http-notification)

---

## ❓ Questions?

If you encounter issues:
1. Check `SECURITY_AUDIT.md` for detailed security implementation
2. Review logs in `storage/logs/laravel.log`
3. Check browser console (F12) for CSP violations
4. Verify all `.env` variables are set correctly

---

**Last Updated:** January 27, 2026
