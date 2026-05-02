# UG TransPass – Architecture

## Overview
A layered PHP/MySQL application:
- Presentation (Views): server-rendered PHP pages with modular includes
- Application (Controllers/Helpers): `admin/config.php`, `admin/auth_functions.php`
- Data (MySQL): normalized schema for users, routes, schedules, bookings, drivers, vehicles, reports

## Components
- Auth: registration, email verification, login with session regeneration
- RBAC: `enforceRole([...])` on protected pages
- Maps: Leaflet consuming `/admin/get-all-routes.php`
- Scheduling: `/admin/get-schedules.php` joins routes/vehicles/drivers and computes capacity
- Booking: `user-bookings-new.php` and related pages

## Data Flows
1. User logs in → session created → redirected by role
2. User views routes → `user-routes.php` fetches routes JSON → Leaflet renders
3. User checks schedules → `/admin/get-schedules.php?route_id=...&travel_date=...`
4. Booking creation enforces capacity (UI) and can be strengthened transactionally

## Extensibility
- Add APIs under `/admin/api/...` for mobile/SPA clients
- Introduce services (classes) for testability if needed
- Add worker/queue for email and long-running tasks

## Diagram (conceptual)
[Views] → [Helpers/Controllers] → [Database]
