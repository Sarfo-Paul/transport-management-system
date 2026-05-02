# Status to Badge Mapping

This document outlines the mapping of various statuses within the transport management system to their corresponding visual badges. The purpose of this mapping is to provide a clear and intuitive representation of the current state of vehicles, drivers, routes, schedules, bookings, and reports within the system. Each status is associated with a specific badge color that conveys its meaning effectively.

## Vehicle Status Mapping
| Status          | Badge Color  |
|------------------|--------------|
| Active           | Success      |
| Maintenance      | Warning      |
| Out of Service   | Danger       |

## Driver Status Mapping
| Status          | Badge Color  |
|------------------|--------------|
| Active           | Success      |
| On Leave         | Warning      |
| Suspended        | Danger       |
| Terminated       | Secondary    |

## Route Status Mapping
| Status          | Badge Color  |
|------------------|--------------|
| Active           | Success      |
| Inactive         | Secondary    |
| Under Review     | Warning      |

## Schedule Status Mapping
| Status          | Badge Color  |
|------------------|--------------|
| Scheduled        | Primary      |
| In Progress      | Info         |
| Completed        | Success      |
| Cancelled        | Danger       |

## Booking Status Mapping
| Status          | Badge Color  |
|------------------|--------------|
| Confirmed        | Success      |
| Pending          | Warning      |
| Cancelled        | Danger       |
| Completed        | Info         |

## Report Status Mapping
| Status          | Badge Color  |
|------------------|--------------|
| Pending          | Secondary    |
| In Progress      | Primary      |
| Resolved         | Success      |
| Rejected         | Danger       |

This mapping serves as a reference for developers and users to understand the significance of each status within the system and aids in the design of user interfaces that effectively communicate the current state of various entities in the transport management system.