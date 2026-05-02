# Key PHP Includes

This document outlines the key PHP includes utilized in the UG TransPass transport management system. These includes are essential for maintaining a modular and organized codebase, facilitating the separation of concerns, and enhancing the maintainability of the application.

## 1. `includes/header.php`
- **Purpose**: This file is responsible for rendering the head section of the HTML document. It includes necessary CSS and JavaScript files, as well as setting up any required `<html>` classes.
- **Key Features**:
  - Links to stylesheets and scripts.
  - Meta tags for responsive design and character set.

## 2. `includes/menu.php`
- **Purpose**: This file generates the admin side menu, which allows users to navigate through different sections of the application. It also includes logic for active item detection to highlight the current page.
- **Key Features**:
  - Dynamic menu generation based on user roles.
  - Active state management for menu items.

## 3. `includes/navbar.php`
- **Purpose**: This file creates the admin navbar, which includes dynamic notifications for users. It is designed to provide quick access to important alerts and messages.
- **Key Features**:
  - Displays real-time notifications.
  - Integrates with the notifications endpoint to fetch updates.

## 4. `includes/driver-navbar.php` and `includes/user-navbar.php`
- **Purpose**: These files define the navigation bars specific to driver and user roles, respectively. They ensure that users see relevant options based on their role within the system.
- **Key Features**:
  - Role-specific links and actions.
  - Enhances user experience by providing tailored navigation.

## 5. `includes/footer.php`
- **Purpose**: This file is responsible for rendering the footer section of the web pages. It typically includes copyright information and links to privacy policies or terms of service.
- **Key Features**:
  - Consistent footer across all pages.
  - Easy to update for site-wide changes.

## 6. `includes/scripts.php`
- **Purpose**: This file includes additional JavaScript files that are necessary for the functionality of the application. It is typically placed at the end of the body to ensure that the page loads efficiently.
- **Key Features**:
  - Consolidates script imports for better performance.
  - Allows for easy management of JavaScript dependencies.

## Conclusion
The key PHP includes play a vital role in the structure and functionality of the UG TransPass transport management system. By organizing code into modular components, the system enhances maintainability, scalability, and user experience. Each include serves a specific purpose, contributing to the overall architecture and design of the application.