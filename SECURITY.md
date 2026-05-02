# UG TransPass – Security Overview

## Threat Model
- Session hijacking / fixation
- Cross-Site Request Forgery (CSRF)
- Cross-Site Scripting (XSS)
- SQL Injection
- Privilege escalation / unauthorized access
- Sensitive data exposure (cookies, credentials)

## Implemented Mitigations
- Sessions: HttpOnly, SameSite=Lax, secure on HTTPS; session ID regeneration on login
- CSRF: `csrf_input()` in forms, verified by `csrf_verify()` on POST
- XSS: `htmlspecialchars` on dynamic output; trusted third-party assets only
- SQLi: Prepared statements on all dynamic queries
- RBAC: `enforceRole([...])` with redirects to role dashboards
- Error handling: user-friendly errors, server logs record stack details

## Operational Hardening
- Use HTTPS in production so secure cookies are enforced
- Rotate SMTP and DB credentials; avoid committing secrets
- Regular DB backups; principle of least privilege for DB user
- Set `display_errors=0` in production; keep `error_reporting` high for dev

## Future Work
- 2FA (TOTP) for admin/driver logins
- Rate limiting and lockouts on auth endpoints
- Content Security Policy (CSP) to further reduce XSS risk
- Formal input validation library and centralized sanitization helpers
- Audit trail tables for critical operations (bookings, schedule changes)
