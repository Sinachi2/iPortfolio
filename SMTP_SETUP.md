# SMTP & SendGrid Setup for `forms/contact.php`

Last updated: 2026-01-03

This guide explains how to configure email sending for the contact form. `forms/contact.php` supports three methods (in order of preference):

1. PHPMailer with SMTP (recommended for reliability)
2. SendGrid API (recommended if you prefer an API)
3. PHP `mail()` fallback (may be unreliable on shared hosts)

---

## A. PHPMailer + SMTP (Recommended)

1. Install PHPMailer via Composer in the project root:

```bash
composer require phpmailer/phpmailer
```

2. Set environment variables on your hosting panel (cPanel, Plesk, etc.) or in Apache `.htaccess` (example):

```
SetEnv SMTP_HOST smtp.example.com
SetEnv SMTP_PORT 587
SetEnv SMTP_USER your_username
SetEnv SMTP_PASS your_password
SetEnv SMTP_SECURE tls
SetEnv FROM_ADDRESS noreply@yourdomain.com
```

3. `forms/contact.php` will auto-detect PHPMailer if `vendor/autoload.php` exists and use SMTP when `SMTP_HOST`/`SMTP_USER`/`SMTP_PASS` are provided.

4. Test by submitting the contact form.


## B. SendGrid API

1. Create a SendGrid account and generate an API Key with Mail Send permissions.
2. Set the environment variable `SENDGRID_API_KEY` in your hosting control panel.
3. `forms/contact.php` will use SendGrid if `SENDGRID_API_KEY` is present.

Notes:
- SendGrid often requires domain authentication (recommended) to prevent emails landing in spam.

## C. PHP `mail()` fallback

If neither PHPMailer nor SendGrid are available, the script will use PHP's `mail()` function as a last resort. Many shared hosts support `mail()`, but it's less reliable.

---

## Testing Tips

- Use a test recipient (`RECIPIENT_EMAIL`) in `.env` or your hosting environment when testing.
- Check the server `error.log` created at `forms/error.log` when `ini_set('error_log', ...)` is enabled.
- If emails don't arrive, check spam folders and verify SPF/DKIM records for your sending domain.

## Troubleshooting

- "Authentication failed" with SMTP: verify host, port, user, pass and security (`tls` vs `ssl`).
- SendGrid 401 errors: ensure the API key is correct and has Mail Send permission.
- If PHPMailer isn't detected, ensure `vendor/autoload.php` exists (run `composer install`).

---

## Quick Example: local testing with dotenv (optional)

For local testing, you can use a `.env` loader in PHP (not included). Alternatively set environment variables in your shell:

**Windows (PowerShell)**

```powershell
$env:SENDGRID_API_KEY="YOUR_KEY"
$env:SMTP_HOST="smtp.example.com"
$env:SMTP_USER="user"
$env:SMTP_PASS="pass"
```

**macOS / Linux**

```bash
export SENDGRID_API_KEY="YOUR_KEY"
export SMTP_HOST="smtp.example.com"
export SMTP_USER="user"
export SMTP_PASS="pass"
```

---

If you want, I can generate a minimal `composer.json` and run `composer require phpmailer/phpmailer` instructions for you to run locally.