# Methodology

## Research Design
The research design for the TransPass UG project follows a structured approach to software development, utilizing the Agile model. This model promotes iterative development, allowing for flexibility and continuous improvement throughout the project lifecycle. The Agile methodology facilitates regular feedback from stakeholders, ensuring that the system meets user requirements effectively.

## Software Development Lifecycle
The software development lifecycle (SDLC) for this project consists of the following phases:

1. **Planning**: Identifying project goals, scope, and resources required.
2. **Requirements Analysis**: Gathering functional and non-functional requirements through stakeholder interviews and surveys.
3. **System Design**: Creating design specifications, including use case models, entity-relationship diagrams, and data flow diagrams.
4. **Implementation**: Developing the system based on the design specifications using PHP and MySQL.
5. **Testing**: Conducting various testing phases to ensure system functionality and performance.
6. **Deployment**: Releasing the system for user access and feedback.
7. **Maintenance**: Providing ongoing support and updates based on user feedback and system performance.

## Requirements Analysis
### Functional Requirements
- User authentication and role-based access control.
- Management of bookings, routes, vehicles, and drivers.
- Real-time notifications and updates for users and drivers.
- Integration of maps and live data for route tracking.

### Non-Functional Requirements
- Performance: The system should handle multiple concurrent users without significant delays.
- Security: Implementation of secure coding practices and data protection measures.
- Usability: The user interface should be intuitive and accessible for all user roles.

## System Design
### Use Case Model
The use case model outlines the interactions between users and the system, detailing the various functionalities available to different user roles, including administrators, drivers, and passengers.

### Entity-Relationship Diagram
The entity-relationship diagram (ERD) illustrates the relationships between different entities in the system, such as users, bookings, routes, vehicles, and drivers. This diagram serves as a foundation for the database schema.

### Data Flow Diagrams
Data flow diagrams (DFDs) depict the flow of information within the system, highlighting how data is processed and transferred between different components.

## System Architecture
The system architecture is based on a client-server model, where the front-end is developed using PHP for server-rendered pages, and the back-end utilizes a MySQL database for data storage and retrieval. The architecture supports real-time data updates through JSON endpoints, enhancing user experience.

## Tools and Technologies
The following tools and technologies were utilized in the development of the TransPass UG project:
- **Programming Language**: PHP
- **Database Management System**: MySQL
- **Front-end Framework**: HTML, CSS, JavaScript
- **Mapping Library**: Leaflet for rendering maps and live data
- **Development Environment**: XAMPP for local server setup

This methodology ensures a comprehensive approach to developing a robust transport management system that meets the needs of its users while adhering to best practices in software development.