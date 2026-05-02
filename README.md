# UG TransPass – Transport Management System

A full-stack PHP/MySQL system to manage campus and city transportation: routes, schedules, vehicles, drivers, bookings, and reports. Built as a modern, responsive admin portal with dedicated user and driver dashboards.

## Highlights
- Vehicle, route and schedule management with role-based dashboards
- Bookings (search, filter, pagination, add/edit/view)
- Driver assignments, maintenance tracking and reports (users/drivers)
- Consistent UI with collapsed side menu and dynamic navbars (live notifications)
- Real-time navbar data served via lightweight JSON endpoints

## Tech Stack
- PHP 8.2, MySQL/MariaDB (XAMPP/LAMP/WAMP)
- UI: Bootstrap 5 + Sneat-style components
- JS: Vanilla + small fetch/AJAX helpers

## Project Structure (key parts)
- `admin/` – all app pages
  - `includes/` – shared `header.php`, `menu.php`, `navbar.php`, `footer.php`, `scripts.php`
  - `assets/` – vendor CSS/JS and images
  - `index.php` – admin dashboard
  - `user-dashboard.php`, `driver-dashboard.php`
  - Feature pages: `app-*` (fleet, route, schedule, booking, reports, maintenance)
  - JSON endpoints: `get-notifications.php`, `get-navbar-data.php`

## Setup
1) Requirements
- PHP 8.1+ and MySQL/MariaDB (XAMPP on Windows recommended)
- Composer optional (vendor PHP libs already bundled)

2) Database
- Create DB `transport_management`
- Import SQL: `admin/db/db.sql` or the provided phpMyAdmin dump
- Verify tables (bookings, drivers, routes, route_schedules, vehicles, vehicle_maintenance, user_reports, driver_reports, users)

3) Configuration
- Update DB creds in `admin/config.php`:
```php
// Example
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_pass = '';
$db_name = 'transport_management';
```
- Ensure `session_start()` is active in `config.php`

4) Run
- Visit `http://localhost/transport_management/admin/login.php`
- Test as admin/driver/user depending on your seed data

## Roles & Flows
- Administrator
  - Manage vehicles, routes, schedules
  - Review bookings and maintenance
  - View reports (users/drivers)
- Driver
  - View today’s trips, vehicle info
  - Report incidents/issues
- User (student/staff)
  - Create/manage bookings, view route map

## UI Conventions
- Side menu: collapsed by default; consistent sections
- Cards with unified headers (title, search/filters, add button)
- Tables: responsive, `bg-light` headers, status badges per enums
- Dropdown actions: View, Edit, Delete order

## Database Mapping
- Vehicles
  - Table: `vehicles`
  - Image field: `image_path` (used in `app-fleet-list.php`)
  - Status badges: Active, Maintenance, Out of Service → green / warning / danger
- Drivers
  - Table: `drivers`
  - Photo field: `photo_path`
  - Status: Active, On Leave, Suspended, Terminated
- Routes
  - Table: `routes` (+ optional `route_coordinates`)
  - Duration in minutes, distance in km
- Schedules
  - Table: `route_schedules` (joins `routes`, `vehicles`, `drivers`)
  - Status: Scheduled, In Progress, Completed, Cancelled
  - Capacity fields: `available_seats`, `max_capacity`
- Bookings
  - Table: `bookings` (joins `users`, `routes`, `route_schedules`; optional `vehicles`, `drivers`)
  - Status: Pending, Confirmed, Cancelled, Completed
- Reports
  - User Reports: `user_reports`
  - Driver Reports: `driver_reports`

## Real-time Navbar Data
- Admin
  - Endpoint: `get-navbar-data.php`
  - Returns latest `user_reports` + `driver_reports` and session user info
- Driver/User
  - Endpoint: `get-notifications.php?scope=driver|user`
  - Driver scope: latest `driver_reports`
  - User scope: latest bookings (scoped by `$_SESSION['user_id']` if available)

## Key Pages
- Admin dashboard: `admin/index.php`
- Menus (active state aware):
  - `includes/menu.php` (admin)
  - `includes/driver-menu.php`
  - `includes/user-menu.php`
- Navbars (dynamic):
  - `includes/navbar.php` (admin)
  - `includes/driver-navbar.php`
  - `includes/user-navbar.php`

## Security & Validation
- Prepared statements on all parameterized queries
- Role-gated pages should check `$_SESSION['user_type']`
- Server-side validation for forms; client-side helps but never trust input
- Hardened sessions (HttpOnly, SameSite=Lax, secure on HTTPS) and session ID regeneration on login
- CSRF protection for POST forms using `csrf_input()` and `csrf_verify()`
- Role-based redirects via `enforceRole([...])` to correct dashboards

## Testing Checklist
- CRUD across Vehicles, Drivers, Routes, Schedules, Bookings
- Bookings reflect `route_schedules` capacity when creating
- Navbar notifications update without page reload
- Status filters and pagination operate correctly
- Images resolve using `image_path`

## Troubleshooting
- If nav badges don’t change: check `get-notifications.php` / `get-navbar-data.php` responses in DevTools
- If images don’t load: verify `vehicles.image_path` value and file existence
- If menu doesn’t collapse: hard refresh; ensure `assets/js/main.js` loaded and `layout-menu-collapsed` class on `<html>`

## Roadmap (Suggested Improvements)
- Use DB views `driver_assignments`, `passenger_bookings` for simpler lists
- Enforce booking capacity with `available_seats` updates
- Avatar sourcing from `users.photo_path`
- Add route polyline rendering from `route_coordinates`

## Architecture & Security Docs
- See `admin/docs/README-TECH.md` for detailed technical overview
- See `ARCHITECTURE.md` for a high-level system diagram and component responsibilities
- See `SECURITY.md` for security model, threats, and mitigations


