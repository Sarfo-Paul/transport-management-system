## TransPass UG — Project-Based Presentation (Max 8 Slides)

Slide 1 — Title
- Title: TransPass UG: Campus Transport Management System
- Presenter: [Your Name], [Student ID]
- Supervisor: [Supervisor Name]
- Date: [Presentation Date]

Notes:
- Introduce the project and set expectations for a practical demo-oriented deck.

Slide 2 — Introduction
- Context: Campus transport challenges (visibility, delays, manual coordination)
- Users: Students/Staff, Drivers, Admins
- Problem: Inefficient booking and schedule management

Notes:
- Keep concise; lead into objectives and solution.

Slide 3 — Aims and Objectives
- Aim: Improve reliability and user experience of campus transport
- Objectives:
  - Digitize bookings and schedules
  - Provide notifications and dashboards
  - Empower admins/drivers with operational tools

Notes:
- Tie each objective to a feature to be shown in the demo.

Slide 4 — Related Works
- Shuttle timetable portals (limited interactivity)
- Public transit planners (routing focus)
- Ride-hailing UX patterns (not campus-specific)
- Gap: Unified, campus-tailored operations + user booking flow

Notes:
- Set context for why your build choices matter.

Slide 5 — Proposed Solution
- Web app with role-based access
- Modules: Routes, Schedules, Bookings, Notifications, Issue reporting, Maintenance
- Stack: PHP, MySQL, Bootstrap, Leaflet

Notes:
- Point to major screens that implement each module.

Slide 6 — Demo (Screens / Flow)
- Entry URL: `http://localhost/transport_management/admin/lander.php`
- User Flow:
  1. Open the landing page → navigate to Login
  2. Sign in as User → go to `user-dashboard.php`
  3. Create Booking → `user-bookings-new.php` (choose route/date/schedule, confirm)
  4. View Bookings → `user-bookings.php` → open `booking-details.php`
  5. Check Map → on dashboard (Leaflet route visualization)
- Admin Flow:
  1. Sign in as Admin → `app-route-list.php`
  2. Create/Edit Routes → `app-route-add.php` / `app-route-edit.php`
  3. Manage Schedules → `app-route-schedule.php` (assign vehicles/drivers)
  4. Reports → `app-reports-list.php`
- Driver Flow:
  1. Sign in as Driver → `driver-dashboard.php`
  2. View Assigned Trips → `driver-schedule.php` and passenger list

Notes:
- Live demo checklist:
  - Landing → Login works at `http://localhost/transport_management/admin/lander.php`
  - As User: create a booking, show availability and confirmation
  - Open booking details, then cancel or keep
  - Show dashboard upcoming trips and map
  - As Admin: adjust a schedule, refresh availability
  - As Driver: show assigned trip view

Slide 7 — Summary & Conclusion
- What we built: Role-based campus transport system with bookings, schedules, and maps
- Key results: Faster booking flow, improved seat utilization, clearer operations
- Why it matters: Replaces manual processes with reliable, transparent coordination
- Conclusion: TransPass UG meets core objectives and is ready for operational use
- Next steps: Mobile app, live tracking, data-driven schedule optimization

Notes:
- Open with the one‑sentence value proposition, then recap outcomes and immediate next steps.

Slide 8 — End / Q&A
- Thank you
- Questions?
- Contact: [email/phone]

Notes:
- Invite feedback; mention planned enhancements (mobile, live location, optimization).


