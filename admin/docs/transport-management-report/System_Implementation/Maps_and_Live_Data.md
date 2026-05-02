# Maps and Live Data

## Overview
The Maps and Live Data feature of the UG TransPass transport management system enhances user experience by providing real-time information about routes, schedules, and vehicle locations. This section details the implementation of mapping functionalities and live data integration within the application.

## Leaflet Integration
The system utilizes the Leaflet library to render campus routes dynamically. The routes are fetched from the endpoint `/admin/get-all-routes.php`, which returns active routes along with their ordered coordinates. This integration allows users to visualize the transport routes effectively.

### Features
- **Dynamic Route Rendering**: Active routes are displayed on the map, providing users with a clear view of available transport options.
- **Interactive Controls**: Users can interact with the map through various controls, including zooming, panning, and resetting the view.

## Live Seat Availability
The application computes live seat availability for vehicles using the `/admin/get-schedules.php` endpoint. This endpoint combines booking data with vehicle capacity, allowing users to check available seats for specific travel dates.

### Functionality
- **Real-Time Updates**: Seat availability is updated in real-time, ensuring users have the most current information before making bookings.
- **User-Friendly Interface**: The interface displays seat availability in an intuitive manner, making it easy for users to understand their options.

## User Routes Display
The `user-routes.php` page showcases all available routes with a legend, start and end markers, and controls for resetting and locating routes. This page auto-refreshes every 30 seconds to provide users with the latest information.

### Key Features
- **Legend and Markers**: A legend helps users identify different routes, while start and end markers provide context for each journey.
- **Auto-Refresh**: The auto-refresh feature ensures that users receive timely updates without needing to manually refresh the page.

## Conclusion
The integration of maps and live data within the UG TransPass system significantly enhances the user experience by providing essential real-time information. This feature not only aids in route planning but also ensures that users are informed about seat availability, contributing to a more efficient transport management system. Future enhancements may include additional mapping features and improved data visualization techniques to further enrich user interaction.