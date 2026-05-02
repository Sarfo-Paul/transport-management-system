# Technical Documentation – UG TransPass

## Architecture Overview
- PHP (server-rendered pages) with shared includes for layout
- MySQL schema as provided (bookings, routes, route_schedules, vehicles, drivers, reports)
- Real-time navbar via small JSON endpoints

## Key PHP Includes
- `includes/header.php`: head, CSS/JS includes, `<html>` classes
- `includes/menu.php`: admin side menu with active item detection
- `includes/navbar.php`: admin navbar (dynamic notifications)
- `includes/driver-navbar.php`, `includes/user-navbar.php`: role navbars
- `includes/footer.php`, `includes/scripts.php`

## Endpoints
- `get-notifications.php`
  - Params: `scope=user|driver`
  - Returns: `{ items: [{ title, message, time }, ...] }`
  - Driver scope source: `driver_reports`
  - User scope source: latest `bookings` (filtered by session user when present)

- `get-navbar-data.php`
  - Returns: `{ notifications: [...], user: { name, role, avatar } }`
  - notifications source: latest from `user_reports` and `driver_reports`

## Data Access Patterns
- Prepared statements used on dynamic filters and identifiers
- List pages share a common pattern: build count + data query with optional filters, then render table + pagination

## Status-to-Badge Mapping
- Vehicles: Active (success), Maintenance (warning), Out of Service (danger)
- Drivers: Active (success), On Leave (warning), Suspended (danger), Terminated (secondary)
- Routes: Active (success), Inactive (secondary), Under Review (warning)
- Schedules: Scheduled (primary), In Progress (info), Completed (success), Cancelled (danger)
- Bookings: Confirmed (success), Pending (warning), Cancelled (danger), Completed (info)
- Reports: Pending (secondary), In Progress (primary), Resolved (success), Rejected (danger)

## Views (optional usage)
- `driver_assignments`: can simplify driver trip lists
- `passenger_bookings`: can simplify user booking lists

## Security & Roles (Modernized)
- Session hardening: HttpOnly + SameSite=Lax; secure flag automatically set when HTTPS.
- Session fixation defense: session_regenerate_id() on login.
- CSRF protection: `csrf_input()` helper for forms, verified by `csrf_verify()` on POST.
- Role-based access control: `enforceRole([...])` redirects users to the correct dashboard.
- Output escaping: `htmlspecialchars` for all dynamic output in templates.

## Maps & Live Data
- Leaflet renders campus routes from `/admin/get-all-routes.php` (active routes + ordered coordinates).
- Schedules endpoint `/admin/get-schedules.php` computes live seat availability combining `bookings` with vehicle capacity, optional by `travel_date`.
- `user-routes.php` shows all routes with legend, start/end markers, reset & locate controls; auto-refreshes every 30 seconds.

## Future Enhancements
- Enforce schedule capacity transactionally during booking.
- 2FA (TOTP) for admins/drivers using `two_factor_*` fields.
- Driver GPS pings → live bus markers on the map.
- Queue-based email delivery and background tasks.
