# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SulamProject** is a PHP-based web application for managing mosque community activities including resident registry, zakat assistance, donations, death/funeral records, and events. It's built as a role-based system with admin and user interfaces.

### Technology Stack
- **Backend**: Plain PHP (no frameworks) with custom routing and session management
- **Database**: MySQL/MariaDB (auto-provisioned via `db.php`)
- **Frontend**: Plain HTML, CSS, and JavaScript (no frameworks)
- **Development Environment**: Laragon (Apache + MySQL on Windows)
- **Build Tools**: Vite for CSS/JS bundling

## Development Commands

### Database Setup
The application uses auto-provisioning. No manual database setup required:
```bash
# Database and tables are automatically created on first page load
# Just visit register.php to trigger the setup
```

### Development Workflow
```bash
# Start Laragon (Control Panel → Start All)
# Access application: http://localhost/sulamproject/
# Access database: http://localhost/phpmyadmin → database: masjidkamek
```

### Build Process
```bash
# Install dependencies
npm install

# Build assets for production
npm run build

# Development with hot reload
npm run dev
```

## Project Architecture

### Feature-Based Structure
The project follows a feature-based architecture where all related files (PHP, CSS, JS) are organized by feature module. Each feature maintains consistent structure with `shared/`, `admin/`, and `user/` subdirectories.

```
sulamproject/
├── index.php                      # Front controller
├── login.php                      # Authentication entry point
├── logout.php                     # Session termination
├── db.php                         # Database connection and auto-provisioning
├── features/                      # Feature-based modules
│   ├── residents/                 # Resident registry module
│   │   ├── shared/                # Shared logic and assets
│   │   ├── admin/                 # Admin-specific functionality
│   │   └── user/                  # User-specific functionality
│   ├── financial/                 # Financial assistance module
│   ├── donations/                 # Donations management module
│   ├── death-funeral/             # Death and funeral records
│   ├── events/                    # Events and announcements
│   ├── dashboard/                 # Dashboard functionality
│   ├── users/                     # User management
│   ├── reports/                   # Reporting system
│   └── shared/                    # Shared components and utilities
├── assets/                        # Compiled/bundled assets
├── database/                      # Migrations and seeds
├── storage/                       # Logs, cache, sessions
└── context-docs/                  # Project documentation
```

### Module Organization Pattern
Each feature module follows this structure:
```
feature-name/
├── shared/                        # Used by both admin and user
│   ├── lib/                      # Business logic and models
│   ├── api/                      # REST API endpoints
│   └── assets/                   # Shared CSS/JS
├── admin/                         # Admin-specific code
│   ├── controllers/              # Admin controllers
│   ├── views/                    # Admin views
│   ├── ajax/                     # Admin AJAX endpoints
│   ├── assets/                   # Admin CSS/JS
│   └── lib/                      # Admin-specific logic
└── user/                          # User-specific code
    ├── controllers/
    ├── views/
    ├── ajax/
    ├── assets/
    └── lib/
```

### Database Configuration
Database connection is managed in `db.php`:
- **Database**: `masjidkamek` (import from `database/schema.sql`)
- **Table**: `users` (auto-created)
- **Default credentials**: root@localhost, blank password
- **Environment variables**: Supported via `getenv()` fallbacks

### Key Design Principles
1. **No Frameworks Policy**: Minimal dependencies, only Vite for asset bundling
2. **Security First**: Prepared statements, password hashing, input validation
3. **Feature Isolation**: Each module self-contained with clear boundaries
4. **Direct File Routing**: Each PHP file corresponds to a route
5. **Role-Based Access**: Admin vs Regular User permissions
6. **Incremental Development**: System updated incrementally while maintaining feature-based structure

### Core Features
- **User Management**: Registration, authentication, and role-based access control
- **Resident Registry**: Household and individual resident management with relationship tracking
- **Financial Assistance**: Zakat applications, assessments, approvals, and disbursements
- **Donations Management**: Donor tracking, donation recording, and receipt generation
- **Death & Funeral Records**: Death notifications, verification, and funeral logistics
- **Events & Announcements**: Event creation, publishing, and schedule management
- **Reporting System**: Summary reports and data export functionality
- **Audit Logging**: Comprehensive audit trail for all operations

## Development Guidelines

### Code Patterns
- **Database Access**: Use prepared statements via mysqli extension
- **Password Security**: Always use `password_hash()` and `password_verify()`
- **Input Validation**: Server-side validation required for all user input
- **Feature Structure**: All features must follow `/features/{feature}/` organization
- **Controller Pattern**: Feature controllers handle business logic, views handle presentation

### Security Requirements
- **SQL Injection**: Use prepared statements for all database queries
- **XSS Prevention**: Escape all output, use `htmlspecialchars()`
- **CSRF Protection**: Implement CSRF tokens in all forms
- **Session Security**: Use secure session configuration with regenerative IDs
- **Input Validation**: Validate all user input server-side before processing
- **Role Verification**: Check user permissions before accessing admin features

### Testing Strategy
- **Local Development**: Use Laragon for full stack development
- **Database Testing**: Verify auto-provisioning works correctly
- **Security Testing**: Test all input validation and prepared statements

## File Organization

### Key Application Files
- `db.php` - Database connection and auto-provisioning
- `index.php` - Front controller and routing
- `login.php` - Authentication entry point
- `logout.php` - Session termination
- `register.php` - User registration implementation
- `features/` - All feature modules with controllers, views, and assets
- `features/shared/` - Shared components and utilities across all features

### Documentation Reference
**Always start here**: The `context-docs/` directory contains the complete project documentation and should be your first reference for any implementation:

- `context-docs/Architecture.md` - Comprehensive system architecture and technical details
- `context-docs/PRD.md` - Product requirements, vision, and feature scope
- `context-docs/Feature-Based-Structure.md` - Directory organization and architectural patterns
- `context-docs/Security-and-Privacy.md` - Security requirements and implementation guidelines
- `context-docs/Use-Cases-and-Flows.md` - User workflows and interaction patterns
- `context-docs/Roles-and-Permissions.md` - Role-based access control specifications

**Implementation workflow**: Before coding any feature, search through `context-docs/` for related keywords to understand requirements, constraints, and existing patterns. Use the documentation as your primary source of truth.

## Database Schema

The system uses a comprehensive schema with tables for users, residents, financial assistance, donations, events, and audit trails. Database is auto-provisioned through `db.php` and includes:

- **Users**: Authentication and role management
- **Residents**: Household and individual registry
- **Assistance**: Zakat applications and disbursements
- **Donations**: Donor and donation tracking
- **Events**: Event management and announcements
- **Audit**: Comprehensive audit logging

Complete schema details available in `context-docs/Architecture.md`.

## Environment Setup

### Local Development (Laragon)
1. Install Laragon (includes Apache + MySQL + PHP)
2. Place project in `C:\laragon\www\sulamproject\`
3. Start Apache and MySQL services
4. Visit `http://localhost/sulamproject/`

### Database Access
- **phpMyAdmin**: `http://localhost/phpmyadmin`
- **Database**: `masjid`
- **Username**: `root` (default)
- **Password**: (blank by default)

### File Permissions
- Ensure web server can write to uploads directory (when implemented)
- Check database connection permissions
- Verify session storage directory permissions

## Common Development Tasks

### Feature Development
1. **Research Phase**: Start by searching `context-docs/` for feature-specific requirements and patterns
2. **Structure Setup**: Create feature directory following `/features/{feature}/` structure with `shared/`, `admin/`, and `user/` subdirectories
3. **Business Logic**: Implement shared business logic in `shared/lib/` with appropriate model classes
4. **Controller Development**: Create feature controllers in `admin/controllers/` and `user/controllers/`
5. **View Implementation**: Develop views in respective `views/` directories with proper templating
6. **Dynamic Features**: Add AJAX endpoints in `ajax/` subdirectories for dynamic functionality
7. **Asset Management**: Include feature-specific CSS/JS assets in `assets/` directories
8. **Integration**: Update routing and navigation components
9. **Documentation Check**: Verify implementation matches documented requirements and security standards

### Database Management
1. **Requirements Research**: Search `context-docs/` for database schema requirements and data models
2. **Migration Creation**: Create migration files in `/database/migrations/` for schema changes
3. **Testing**: Test migrations with development database before production
4. **Auto-provisioning**: Update auto-provisioning logic in `db.php` for new tables
5. **Schema Design**: Maintain proper foreign key relationships and indexes
6. **Documentation Sync**: Ensure schema changes match documented requirements
7. **Verification**: Cross-reference implementation with `context-docs/Architecture.md` database specifications

### Security Implementation
1. **Security Standards**: Review `context-docs/Security-and-Privacy.md` for current security requirements
2. **Database Security**: Ensure all database queries use prepared statements without exception
3. **Input Validation**: Validate all user input server-side before processing
4. **Output Escaping**: Escape all output using `htmlspecialchars()` or appropriate functions
5. **CSRF Protection**: Implement CSRF tokens in all forms and verify on submission
6. **Session Management**: Use secure session configuration with regenerative IDs
7. **Access Control**: Apply role-based access control checks before admin feature access
8. **Audit Logging**: Log all sensitive operations for audit purposes
9. **Compliance Check**: Verify implementation meets all documented security requirements

## Development Standards

### Core Principles
- **Documentation First**: Always start implementation by researching `context-docs/` for requirements and patterns
- **Feature Isolation**: Each feature module is self-contained with clear boundaries
- **Incremental Updates**: System updated incrementally while maintaining feature-based structure
- **No Framework Dependencies**: Minimal external dependencies, only Vite for asset management
- **Auto-provisioning**: Database setup handled automatically through bootstrap
- **Security First**: All operations must follow security requirements without exception
- **Direct Routing**: Clean file-based routing with intuitive URL structure
- **Audit Compliance**: All sensitive operations must create audit trail entries

### Research Workflow
Before implementing any feature or change:
1. **Search Documentation**: Use grep/search in `context-docs/` for relevant keywords
2. **Understand Requirements**: Review PRD, Architecture, and specific feature documentation
3. **Check Patterns**: Look for existing implementation patterns in related features
4. **Security Review**: Verify `context-docs/Security-and-Privacy.md` requirements
5. **Role Verification**: Check `context-docs/Roles-and-Permissions.md` for access control requirements

### Implementation Verification
- Cross-reference all code against documented requirements
- Ensure security standards are met without exception
- Verify role-based access control is properly implemented
- Test audit logging for sensitive operations
- Validate against use cases and flows documented