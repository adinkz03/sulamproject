# SulamProject Feature-Based Architecture

## Request Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     HTTP Request                                 │
│                    (e.g., /dashboard)                            │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      .htaccess                                   │
│               (URL Rewriting & Security)                         │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      index.php                                   │
│                  (Front Controller)                              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      routes.php                                  │
│              (Route Configuration & Dispatch)                    │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                 features/shared/lib/utilities/                   │
│                       Router.php                                 │
│                  (Route Matching & Handler Execution)            │
└────────────────────────────┬────────────────────────────────────┘
                             │
                ┌────────────┴────────────┐
                │                         │
                ▼                         ▼
    ┌───────────────────────┐   ┌───────────────────────┐
    │  Feature Controller   │   │  Middleware/Guards    │
    │  (e.g., Dashboard,    │   │  - Auth Check         │
    │   Users, Residents)   │   │  - CSRF Verification  │
    └───────────┬───────────┘   │  - Role Check         │
                │               └───────────────────────┘
                ▼
    ┌───────────────────────────────────────────┐
    │       BaseController                      │
    │       (Common Functionality)              │
    │  - requireAuth()                          │
    │  - requireAdmin()                         │
    │  - json()                                 │
    │  - renderView()                           │
    └───────────┬───────────────────────────────┘
                │
                ▼
    ┌───────────────────────────────────────────┐
    │     Business Logic Layer                  │
    │  - Models (shared/lib/)                   │
    │  - Services (AuthService, etc.)           │
    │  - Database (PDO wrapper)                 │
    └───────────┬───────────────────────────────┘
                │
                ▼
    ┌───────────────────────────────────────────┐
    │         View Layer                        │
    │  Feature Views → Dashboard Layout         │
    │                → Base Layout              │
    └───────────┬───────────────────────────────┘
                │
                ▼
    ┌───────────────────────────────────────────┐
    │         HTTP Response                     │
    │         (HTML/JSON)                       │
    └───────────────────────────────────────────┘
```

## Directory Structure Tree

```
sulamproject/
│
├── 📁 features/                           # Feature-based modules
│   │
│   ├── 📁 shared/                        # Shared across all features
│   │   ├── 📁 components/
│   │   │   └── 📁 layouts/               # Layout templates
│   │   │       ├── base.php              # HTML base template
│   │   │       └── dashboard-layout.php  # Dashboard with sidebar
│   │   │
│   │   ├── 📁 controllers/
│   │   │   └── BaseController.php        # Common controller logic
│   │   │
│   │   ├── 📁 lib/
│   │   │   ├── 📁 auth/                  # Authentication
│   │   │   │   ├── session.php           # Session management
│   │   │   │   └── AuthService.php       # Auth business logic
│   │   │   │
│   │   │   ├── 📁 database/              # Database layer
│   │   │   │   ├── Database.php          # PDO wrapper (singleton)
│   │   │   │   └── db-bootstrap.php      # Auto-provisioning
│   │   │   │
│   │   │   ├── 📁 utilities/             # Helper utilities
│   │   │   │   ├── csrf.php              # CSRF protection
│   │   │   │   ├── validation.php        # Input validation
│   │   │   │   ├── functions.php         # Common helpers
│   │   │   │   └── Router.php            # Route matching
│   │   │   │
│   │   │   └── 📁 audit/                 # Audit logging
│   │   │       └── audit-log.php         # Audit trail
│   │   │
│   │   └── 📁 assets/
│   │       ├── 📁 css/                   # Shared styles
│   │       │   ├── variables.css         # CSS variables
│   │       │   └── base.css              # Base styles
│   │       └── 📁 js/                    # Shared scripts
│   │
│   ├── 📁 dashboard/                     # Dashboard feature
│   │   ├── 📁 shared/lib/               # Dashboard models
│   │   ├── 📁 admin/                    # Admin dashboard
│   │   │   ├── 📁 controllers/          # DashboardController.php
│   │   │   ├── 📁 views/                # admin-overview.php
│   │   │   ├── 📁 ajax/                 # AJAX endpoints
│   │   │   ├── 📁 assets/               # admin-dashboard.css
│   │   │   └── 📁 lib/                  # Admin-specific logic
│   │   └── 📁 user/                     # User dashboard
│   │       ├── 📁 controllers/
│   │       ├── 📁 views/                # user-overview.php
│   │       ├── 📁 ajax/
│   │       ├── 📁 assets/
│   │       └── 📁 lib/
│   │
│   ├── 📁 users/                        # User management & auth
│   │   └── 📁 shared/
│   │       ├── 📁 controllers/          # AuthController.php
│   │       ├── 📁 views/                # login.php, register.php
│   │       └── 📁 lib/                  # UserModel.php
│   │
│   ├── 📁 residents/                    # Residents module
│   │   ├── 📁 shared/lib/              # Resident, Household models
│   │   ├── 📁 admin/                   # Admin CRUD views
│   │   │   ├── 📁 controllers/
│   │   │   ├── 📁 views/               # manage-residents.php
│   │   │   ├── 📁 ajax/                # search-residents.php
│   │   │   ├── 📁 assets/
│   │   │   └── 📁 lib/
│   │   └── 📁 user/                    # User-facing views
│   │       ├── 📁 controllers/
│   │       ├── 📁 views/
│   │       ├── 📁 ajax/
│   │       ├── 📁 assets/
│   │       └── 📁 lib/
│   │
│   ├── 📁 assistance/                   # Financial assistance
│   │   ├── 📁 shared/lib/              # Application, Assessment models
│   │   ├── 📁 admin/                   # Approve, disburse
│   │   └── 📁 user/                    # Apply for assistance
│   │
│   ├── 📁 donations/                    # Donations tracking
│   │   ├── 📁 shared/lib/              # Donor, Donation models
│   │   ├── 📁 admin/                   # Manage, generate receipts
│   │   └── 📁 user/                    # Record donation
│   │
│   ├── 📁 death-funeral/                # Death & funeral records
│   │   ├── 📁 shared/lib/              # Notification, Logistics models
│   │   ├── 📁 admin/                   # Verify, manage logistics
│   │   └── 📁 user/                    # Report notification
│   │
│   ├── 📁 events/                       # Events management
│   │   ├── 📁 shared/lib/              # Event model
│   │   ├── 📁 admin/                   # Create, publish events
│   │   └── 📁 user/                    # View events
│   │
│   └── 📁 reports/                      # Reporting system
│       ├── 📁 shared/lib/               # ReportGenerator
│       └── 📁 admin/                    # Generate, export reports
│
├── 📁 database/
│   └── 📁 migrations/                   # Database migrations
│       └── migration-plan.md            # Schema & table definitions
│
├── 📁 storage/
│   └── 📁 logs/                         # Application logs
│       ├── error.log
│       ├── debug.log
│       └── audit.log
│
├── 📁 assets/                           # Old assets (reference)
│   └── 📁 css/
│       └── style.css
│
├── 📁 context-docs/                     # Project documentation
│   ├── Architecture.md
│   ├── Feature-Based-Structure.md
│   ├── PRD.md
│   ├── Security-and-Privacy.md
│   └── ...
│
├── 📄 index.php                         # Front controller entry point
├── 📄 routes.php                        # Route definitions
├── 📄 .htaccess                         # URL rewriting & security
├── 📄 db.php                            # Legacy (kept for compatibility)
│
├── 📄 login.php                         # Shim → AuthController
├── 📄 register.php                      # Shim → AuthController
├── 📄 dashboard.php                     # Shim → /dashboard
├── 📄 logout.php                        # Shim → AuthController
├── 📄 residents.php                     # Shim → /residents
├── 📄 donations.php                     # Shim → /donations
├── 📄 events.php                        # Shim → /events
│
├── 📄 IMPLEMENTATION-STATUS.md          # Detailed implementation guide
├── 📄 QUICK-START.md                    # Quick reference
├── 📄 plan-featureBasedSkeleton.prompt.md
├── 📄 AGENTS.md
├── 📄 README.md
└── 📄 .github/copilot-instructions.md
```

## Feature Module Pattern

Each feature follows this consistent structure:

```
feature-name/
├── shared/                    # Business logic shared by admin/user
│   └── lib/                  # Models, services, helpers
│       ├── ModelName.php     # Data models
│       └── ServiceName.php   # Business logic services
│
├── admin/                     # Admin-only functionality
│   ├── controllers/          # Admin controllers
│   │   └── FeatureController.php
│   ├── views/                # Admin views
│   │   ├── list.php
│   │   ├── create.php
│   │   └── edit.php
│   ├── ajax/                 # Admin AJAX endpoints
│   │   ├── search.php
│   │   └── delete.php
│   ├── assets/               # Admin-specific CSS/JS
│   │   ├── admin-feature.css
│   │   └── admin-feature.js
│   └── lib/                  # Admin-specific helpers
│
└── user/                      # Regular user functionality
    ├── controllers/          # User controllers
    ├── views/                # User views
    ├── ajax/                 # User AJAX endpoints
    ├── assets/               # User-specific CSS/JS
    └── lib/                  # User-specific helpers
```

## Technology Stack

```
┌─────────────────────────────────────────┐
│            Frontend Layer                │
│  - HTML5                                 │
│  - CSS3 (Feature-prefixed classes)      │
│  - Vanilla JavaScript                    │
│  - Vite (for bundling - planned)        │
└──────────────────┬──────────────────────┘
                   │
┌──────────────────▼──────────────────────┐
│          Application Layer               │
│  - Plain PHP 8+ (No frameworks)         │
│  - Feature-based architecture           │
│  - Front controller pattern             │
│  - MVC-like separation                  │
└──────────────────┬──────────────────────┘
                   │
┌──────────────────▼──────────────────────┐
│            Data Layer                    │
│  - PDO (PHP Data Objects)               │
│  - Prepared statements                  │
│  - MySQL/MariaDB                        │
└──────────────────┬──────────────────────┘
                   │
┌──────────────────▼──────────────────────┐
│          Infrastructure                  │
│  - Apache 2.4                           │
│  - MySQL 8.0 / MariaDB 10.x             │
│  - Laragon (Dev environment)            │
└─────────────────────────────────────────┘
```

## Security Layers

```
┌─────────────────────────────────────────────────────────┐
│                   Security Layers                        │
├─────────────────────────────────────────────────────────┤
│  1. Transport Layer                                      │
│     - HTTPS (production)                                 │
│     - Security headers (.htaccess)                       │
├─────────────────────────────────────────────────────────┤
│  2. Request Validation                                   │
│     - CSRF tokens (all forms)                            │
│     - Input validation (server-side)                     │
│     - Output escaping (XSS prevention)                   │
├─────────────────────────────────────────────────────────┤
│  3. Authentication                                       │
│     - Password hashing (password_hash)                   │
│     - Secure session management                          │
│     - Session regeneration                               │
├─────────────────────────────────────────────────────────┤
│  4. Authorization                                        │
│     - Role-based access control (RBAC)                   │
│     - Feature-level permissions                          │
│     - Deny-by-default policy                             │
├─────────────────────────────────────────────────────────┤
│  5. Data Layer                                           │
│     - Prepared statements (SQL injection prevention)     │
│     - Database user permissions                          │
│     - Encrypted connections                              │
├─────────────────────────────────────────────────────────┤
│  6. Audit & Monitoring                                   │
│     - Audit logs (all sensitive operations)              │
│     - Error logging                                      │
│     - Failed login tracking                              │
└─────────────────────────────────────────────────────────┘
```

## Data Flow Example: Login

```
1. User visits /login
   └─> Router matches route
       └─> Calls AuthController::showLogin()
           └─> Generates CSRF token
               └─> Renders login view with token

2. User submits form (POST /login)
   └─> Router matches POST route
       └─> Calls AuthController::handleLogin()
           └─> Verifies CSRF token
               └─> Validates input
                   └─> AuthService::login()
                       └─> Database::fetchOne() [PDO prepared statement]
                           └─> password_verify()
                               ├─> Success:
                               │   └─> Session regeneration
                               │       └─> Set session variables
                               │           └─> AuditLog::logLogin()
                               │               └─> Redirect to /dashboard
                               │
                               └─> Failure:
                                   └─> AuditLog::logLogin(failed)
                                       └─> Set error message
                                           └─> Redirect to /login
```

## Convention Summary

### Naming Conventions
- **Classes**: PascalCase (e.g., `AuthController`, `Database`)
- **Methods**: camelCase (e.g., `handleLogin`, `fetchAll`)
- **Files**: PascalCase for classes, kebab-case for views (e.g., `login-form.php`)
- **CSS Classes**: Feature-prefixed (e.g., `.dashboard-card`, `.residents-table`)
- **Database Tables**: snake_case (e.g., `users`, `assistance_applications`)

### File Organization
- Controllers: `<feature>/<role>/controllers/`
- Views: `<feature>/<role>/views/`
- Models/Services: `<feature>/shared/lib/`
- Assets: `<feature>/<role>/assets/`

### URL Structure
- Clean URLs via routing: `/dashboard`, `/residents/123`
- No `.php` extensions in URLs
- RESTful-like patterns where applicable

---

**Visual architecture complete** ✓

This diagram provides a comprehensive view of the entire system architecture.
