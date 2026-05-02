# Endpoints Documentation for Transport Management System

## Overview
This document provides a detailed description of the various endpoints available in the Transport Management System (UG TransPass). Each endpoint is designed to facilitate specific functionalities within the application, ensuring efficient data retrieval and interaction.

## Endpoints

### 1. Get Notifications
- **Endpoint:** `get-notifications.php`
- **Method:** GET
- **Parameters:**
  - `scope`: Defines the user type. Acceptable values are `user` or `driver`.
- **Response:**
  - Returns a JSON object containing an array of notification items.
  - **Structure:**
    ```json
    {
      "items": [
        {
          "title": "Notification Title",
          "message": "Notification Message",
          "time": "Timestamp"
        },
        ...
      ]
    }
    ```
- **Description:** This endpoint retrieves notifications based on the user type. For drivers, notifications are sourced from `driver_reports`, while for users, they are derived from the latest `bookings`.

### 2. Get Navbar Data
- **Endpoint:** `get-navbar-data.php`
- **Method:** GET
- **Response:**
  - Returns a JSON object containing user information and notifications.
  - **Structure:**
    ```json
    {
      "notifications": [...],
      "user": {
        "name": "User Name",
        "role": "User Role",
        "avatar": "Avatar URL"
      }
    }
    ```
- **Description:** This endpoint provides the necessary data for rendering the admin navbar, including the latest notifications and user details.

## Data Access Patterns
- All endpoints utilize prepared statements to prevent SQL injection and ensure secure data access.
- Common patterns include building a count query followed by a data retrieval query, often with optional filters applied based on user input.

## Security Considerations
- Each endpoint is secured with appropriate access controls to ensure that only authorized users can access sensitive data.
- CSRF protection is implemented for any POST requests to safeguard against cross-site request forgery attacks.

## Conclusion
The endpoints outlined in this document are integral to the functionality of the UG TransPass system. They facilitate real-time data access and user interaction, contributing to a seamless user experience. Future enhancements may include additional endpoints to support new features and improve system performance.