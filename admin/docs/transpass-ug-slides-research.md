## TransPass UG — Research-Based Presentation (Max 10 Slides)

Slide 1 — Title
- Title: TransPass UG: A Smart Campus Transport Management System
- Presenter: [Your Name], [Student ID]
- Supervisor: [Supervisor Name]
- Date: [Presentation Date]

Speaker notes:
- Briefly introduce yourself and the project name. State that this is a research-based overview of the problem, approach, and outcomes.

Slide 2 — Introduction / Background
- Context: Growing demand for reliable campus transport at University of Ghana
- Problem: Manual scheduling, limited visibility, long wait times, under/over-utilization
- Impact: Student/staff delays, driver inefficiencies, administrative overhead

Speaker notes:
- Emphasize pain points observed on campus and any baseline stats you collected or referenced.

Slide 3 — Research Problem & Questions
- Core problem: How can we optimize campus transport operations end-to-end?
- RQ1: What data and constraints define efficient route planning and scheduling?
- RQ2: Which features most improve user satisfaction and on-time performance?
- RQ3: How can we balance transparency, privacy, and operational needs?

Speaker notes:
- Frame the research scope. Mention that TransPass UG is a practical response to these questions.

Slide 4 — Aim and Objectives
- Main Aim: Design and implement a data-informed transport management system to improve reliability and user experience.
- Objectives:
  - O1: Digitize bookings, schedules, and notifications
  - O2: Provide real-time visibility of routes and trips
  - O3: Reduce no-shows and delays through reminders and policies
  - O4: Enable admin/driver dashboards for operational control

Speaker notes:
- Keep aim singular; map each objective to a measurable outcome.

Slide 5 — Related Works
- University shuttle apps (basic timetable + announcements)
- Public transit GTFS-based planners (routing focus, limited ops control)
- Ride-hailing style apps (great UX, not tailored for campus constraints)
- Gap: Campus-specific, multi-role system with bookings, schedules, and ops analytics

Speaker notes:
- Cite 2–3 comparable systems and the specific gaps they leave for campus use.

Slide 6 — Proposed Solution (System Overview)
- Platform: TransPass UG web application (students/staff, drivers, admins)
- Key modules: Routes & Schedules, Bookings, Notifications, Driver ops, Maintenance
- Tech: PHP, MySQL, Bootstrap UI, Leaflet maps
- Policies: Cancellation windows, seat allocation rules, data minimization

Speaker notes:
- Map modules to user roles; mention security and role-based access.

Slide 7 — Architecture & Data Flow
- Client UI: Booking pages, dashboards, map visualization
- Backend: Auth, booking engine, schedule management, notifications
- Database: Routes, schedules, vehicles, drivers, bookings, issues
- Integrations: Email/SMS (optional), geospatial tiles via Leaflet/OSM

Speaker notes:
- Explain how a booking request flows through the system to confirmation.

Slide 8 — Implementation Highlights
- Booking lifecycle: Create, confirm, remind, complete/cancel
- User dashboards: Upcoming trips, frequent routes, quick stats
- Admin features: Route CRUD, schedule planning, reports/exports
- Driver app pages: Assigned trips, passenger list, issue reporting

Speaker notes:
- Reference implemented files (e.g., `user-bookings-new.php`, admin route/schedule pages) without diving into code.

Slide 9 — Results & Outcomes
- Usability: Faster bookings, fewer queue inquiries (qualitative feedback)
- Efficiency: Better seat utilization via pre-booking and reminders
- Reliability: Clearer schedules, reduced last-minute confusion
- Comparison: Outperforms bulletin-board timetables on visibility and control

Speaker notes:
- Add any pilot/test metrics you gathered (e.g., % reduction in no-shows).

Slide 10 — Summary & Future Work
- Summary: Research-informed system that digitizes campus transport end-to-end
- Contributions: Multi-role design, booking policy framework, ops tooling
- Future: Mobile app, live vehicle locations, optimization of schedules via data
- Thank you — Questions?

Speaker notes:
- Close with 2–3 concrete next steps and invite questions.


