# UG TransPass – Final Year Project (Slides Outline)

1. Title
- Project: UG TransPass – Transport Management System
- Your name, index number, supervisor, date

2. Problem Statement
- Transport coordination challenges (routes, bookings, incidents)
- Manual processes → inefficiency, poor visibility

3. Objectives
- Digitize bookings, schedules, and fleet ops
- Provide dashboards for admin/driver/user
- Support reporting and maintenance tracking

4. Scope & Users
- Users: Admin, Drivers, Students/Staff
- Features in scope: vehicles, routes, schedules, bookings, reports, maintenance

5. System Architecture
- PHP 8.2 + MySQL/MariaDB
- Bootstrap-based responsive UI
- JSON endpoints for real-time navbar notifications
- Diagram: UI → PHP controllers/pages → MySQL

6. Database Design
- Key tables: users, drivers, vehicles, routes, route_schedules, bookings, user_reports, driver_reports, vehicle_maintenance
- Mention views: driver_assignments, passenger_bookings
- Highlight foreign keys and enums

7. Key Features – Admin
- Manage vehicles, routes, schedules
- Review bookings, maintenance, and reports
- Dashboard KPIs and recent activity

8. Key Features – Driver
- View assigned trips and vehicle info
- Report issues (driver_reports)
- Live navbar notifications

9. Key Features – User
- Create/manage bookings
- View upcoming trips and route map
- Live navbar notifications

10. UI/UX Consistency
- Collapsed side menu by default
- Card headers with search/filters/add
- Status badges aligned to DB enums

11. Security & Validation
- Session-based roles, server-side validation
- Prepared statements
- Audit fields (created_at/updated_at)

12. Demo Walkthrough
- Login
- Admin tasks (add vehicle/route, schedule, booking list)
- Driver dashboard & report issue
- User booking and notifications

13. Results & Evaluation
- Improved visibility and efficiency
- Consistent design and data integrity

14. Challenges & Lessons
- Schema alignment, role-based flows, UI consistency
- Handling real-time updates simply

15. Future Work
- Capacity enforcement, route polylines, avatars from users.photo_path
- SMS/email notifications, analytics dashboards

16. Q&A
- Contact info

