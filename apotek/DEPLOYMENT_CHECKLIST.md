# Production Deployment Checklist

## Pre-Deployment Security Verification

### Environment Setup
- [ ] Copy `.env.example` to `.env` in production
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_URL=https://your-domain.com`
- [ ] Ensure `APP_KEY` is set (should be from `php artisan key:generate`)

### reCAPTCHA Configuration
- [ ] Create account at https://www.google.com/recaptcha/admin
- [ ] Select reCAPTCHA v2 (I'm not a robot)
- [ ] Register your domain
- [ ] Add to `.env`:
  ```
  RECAPTCHA_SITE_KEY=your_site_key
  RECAPTCHA_SECRET_KEY=your_secret_key
  ```

### Database Setup
- [ ] Switch from SQLite to PostgreSQL or MySQL
- [ ] Update `DB_*` variables in `.env`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Verify database backups configured

### Email Configuration
- [ ] Configure mail service (for password resets)
- [ ] Update `MAIL_*` variables in `.env`
- [ ] Test by triggering password reset

### Midtrans Payment
- [ ] Get production credentials from Midtrans
- [ ] Add to `.env`:
  ```
  MIDTRANS_IS_PRODUCTION=true
  MIDTRANS_SERVER_KEY=your_prod_server_key
  MIDTRANS_CLIENT_KEY=your_prod_client_key
  ```
- [ ] Configure webhook URL in Midtrans dashboard

### HTTPS & SSL
- [ ] Install valid SSL certificate
- [ ] Update `APP_URL` to https://
- [ ] Test HTTPS at https://www.ssllabs.com/ssltest/
- [ ] Verify `Strict-Transport-Security` header present

### Dependencies
- [ ] Run `composer update` to get latest patches
- [ ] Run `npm audit fix` for JavaScript vulnerabilities
- [ ] Verify all dependencies are secure: `composer audit`

### Session & Cache
- [ ] Set `SESSION_ENCRYPT=true` in `.env`
- [ ] Set `SESSION_DRIVER=database` (already set)
- [ ] Set `CACHE_STORE=database` (already set)
- [ ] Create database tables: `php artisan migrate`

### Logging
- [ ] Set `LOG_LEVEL=error` (don't expose debug info)
- [ ] Configure log rotation
- [ ] Set up monitoring/alerts for logs
- [ ] Never log sensitive data (passwords, tokens)

### File Permissions
```bash
chmod 755 bootstrap/cache
chmod 755 storage
chmod 755 storage/app
chmod 755 storage/framework
chmod 755 storage/logs
```

### Security Headers Verification
Open browser DevTools (F12) and check Response Headers:
- [ ] `X-Frame-Options: SAMEORIGIN`
- [ ] `X-Content-Type-Options: nosniff`
- [ ] `X-XSS-Protection: 1; mode=block`
- [ ] `Referrer-Policy: strict-origin-when-cross-origin`
- [ ] `Permissions-Policy: ...`
- [ ] `Strict-Transport-Security: max-age=31536000;...`
- [ ] `Content-Security-Policy: ...`

### Rate Limiting Test
- [ ] Try login 6 times with wrong password → Should be blocked
- [ ] Wait for timeout, retry → Should work
- [ ] Verify throttle key uses email + IP

### reCAPTCHA Test
- [ ] Go to /register
- [ ] Submit without checking reCAPTCHA → Should fail with "Please complete the reCAPTCHA"
- [ ] Check reCAPTCHA
- [ ] Submit → Should work
- [ ] Go to /login
- [ ] Repeat test

### Payment Integration Test
- [ ] Create test transaction
- [ ] Verify Snap modal opens
- [ ] Process payment with test card
- [ ] Verify webhook received
- [ ] Verify transaction status updated in DB
- [ ] Verify PDF invoice generated

### Admin Panel Test
- [ ] Login as admin
- [ ] Verify rate limiting (30 req/min)
- [ ] Test product CRUD operations
- [ ] Verify file uploads work
- [ ] Check image processing

### User Profile Test
- [ ] Login as regular user
- [ ] Update profile information
- [ ] Change password
- [ ] Verify email verification (if needed)
- [ ] Delete account (if supported)

### Email Delivery Test
- [ ] Trigger password reset
- [ ] Verify email received
- [ ] Click reset link
- [ ] Set new password
- [ ] Login with new password

### Error Handling
- [ ] Trigger 404 error → Should show generic error page (not stack trace)
- [ ] Trigger 500 error → Should show generic error page
- [ ] Check logs have error details
- [ ] Verify no sensitive info in error messages

### Database Security
- [ ] Strong password set for DB user
- [ ] DB user has minimal required privileges
- [ ] Database is backed up regularly
- [ ] Backups are encrypted
- [ ] Backup restoration tested

### Monitoring & Alerts
- [ ] Application error monitoring enabled (e.g., Sentry)
- [ ] Log aggregation configured (e.g., Datadog, LogRocket)
- [ ] Alert thresholds set:
  - [ ] High error rate (>5%)
  - [ ] Failed login attempts (>10/min)
  - [ ] Payment processing failures
  - [ ] Webhook failures
- [ ] On-call rotation established

### Final Deployment
- [ ] All checklist items complete
- [ ] Test environment passed all tests
- [ ] Database backed up
- [ ] Deployment rollback plan documented
- [ ] Team notified of deployment
- [ ] Monitoring dashboard active
- [ ] Post-deployment verification plan ready

---

## Post-Deployment (First 24 Hours)

- [ ] Monitor error logs for issues
- [ ] Monitor user feedback
- [ ] Verify payment processing working
- [ ] Check performance metrics
- [ ] Verify backups running
- [ ] Confirm email delivery working
- [ ] Monitor failed login attempts
- [ ] Check SSL certificate validity

---

## Critical URLs to Test

```
https://your-domain.com/                     # Homepage
https://your-domain.com/login                # Login
https://your-domain.com/register             # Registration
https://your-domain.com/dashboard            # Dashboard (auth required)
https://your-domain.com/payment              # Payment form (auth required)
https://your-domain.com/admin                # Admin panel (auth required)
https://your-domain.com/profile              # Profile (auth required)
```

---

## Emergency Contacts

- **Midtrans Support:** support@midtrans.com
- **Google reCAPTCHA:** recaptcha-support@google.com
- **Your Host:** [your-hosting-support]

---

## Quick Commands Reference

```bash
# Migrations
php artisan migrate --force

# Cache clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Logs
tail -f storage/logs/laravel.log

# Test
php artisan test

# Backup
php artisan db:backup

# Queue processing
php artisan queue:work --tries=3
```

---

## Rollback Plan

If critical issue found:
1. Alert the team immediately
2. Check error logs for specific error
3. If data corruption: restore from backup
4. If code issue: `git revert <commit>`
5. Run migrations if needed
6. Verify fix works in staging first
7. Deploy fix to production
8. Notify users of resolution

---

**Status:** Ready for Production Deployment ✅

**Last Updated:** January 27, 2026
