<<<<<<< HEAD
# rmu_project_finder

=======
# RMU Student Project Repository

A web-based project repository system built for **Regional Maritime University (RMU)** that enables departments to manage, archive, and showcase student academic projects. The system provides three interfaces: an **Admin Panel**, a **Department Admin Panel**, and a **Student Search Portal**.

---

## Table of Contents

1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [Features](#features)
4. [Database Schema](#database-schema)
5. [Directory Structure](#directory-structure)
6. [Installation & Setup](#installation--setup)
7. [User Roles & Access](#user-roles--access)
8. [Security Measures](#security-measures)
9. [Technology Stack](#technology-stack)

---

## Overview

The RMU Student Project Repository is designed to centralize the storage and retrieval of past student projects across all university departments. It solves the problem of scattered, hard-to-find academic work by providing a searchable, filterable, and browsable digital repository.

**Key objectives:**
- Allow departments to upload and manage their student projects
- Provide a public-facing search interface for students to discover past work
- Enable the central admin to oversee all departments and projects
- Maintain audit trails for accountability

---

## System Architecture

The application follows a traditional PHP/MySQL server-side architecture with AJAX-powered dynamic content loading on the frontend.

```
+------------------+     +-------------------+     +------------------+
|  Student Portal  |     |  Dept Admin Panel  |     |   Admin Panel    |
|   (index.php)    |     |   (dep_admin/)     |     |    (admin/)      |
+--------+---------+     +---------+----------+     +--------+---------+
         |                         |                          |
         +----------+--------------+--------------------------+
                    |
            +-------v--------+
            |   PHP Backend  |
            |  (MySQL via    |
            |   mysqli)      |
            +-------+--------+
                    |
            +-------v--------+
            |    MySQL DB    |
            | project_finder |
            +----------------+
```

---

## Features

### Admin Panel (`/admin`)

| Feature | Description |
|---------|-------------|
| **Dashboard** | Overview with total projects count, total departments count, and a recent activity/audit log table |
| **Department Management** | Add, edit, and archive departments. Upload departments via Excel files |
| **View All Projects** | Browse projects from every department with filters (department, year, supervisor, tag), search, sorting (by title, year, department), result count, and CSV export |
| **Project Details Modal** | View full project details including embedded PDF preview |
| **Forgot Password** | Token-based password reset via email (1-hour expiry) |
| **Audit Logging** | All sensitive operations (create, update, archive) are logged with timestamp, user, IP address |

### Department Admin Panel (`/dep_admin`)

| Feature | Description |
|---------|-------------|
| **Dashboard** | Department-specific overview showing department name and total project count |
| **Project Management** | Add, edit projects with title, description, year, participants (multiple), supervisors (multiple with dropdown filtering), tags, and PDF file upload |
| **Auto-Tag Generation** | Tags are auto-generated from project title keywords using stop-word filtering when the title field loses focus |
| **Supervisor Management** | Add, edit, and archive supervisors. Upload supervisors via Excel files |
| **PDF Preview** | Live PDF preview when uploading or editing project files |
| **File Replace** | Replace existing project files with detailed error reporting for upload failures |
| **Smart Supervisor Dropdown** | When adding multiple supervisors to a project, already-selected supervisors are automatically hidden from other dropdowns |
| **Forgot Password** | Token-based password reset via email |

### Student Search Portal (`/index.php`)

| Feature | Description |
|---------|-------------|
| **Modern Search UI** | Gradient hero header, search box with real-time debounced search |
| **Tag Cloud** | Browse projects by clicking popular tags (top 25 shown with project counts) |
| **Filters** | Filter by department, year, and tags simultaneously |
| **Search with Stemming** | English suffix-stripping stemmer for conflation -- searching "computing" also matches "computer", "computed", etc. |
| **Enhanced Project Cards** | Cards with gradient accent bar, RMU logo watermark, year badge, department label, and tag pills |
| **Read More** | Long descriptions open in a clean modal overlay for full reading |
| **PDF Preview & Download** | Preview project PDFs in an embedded modal viewer with download button |
| **Responsive Design** | Fully responsive layout that works on mobile, tablet, and desktop |

---

## Database Schema

The system uses a MySQL database named `project_finder` with the following tables:

### Core Tables

| Table | Purpose |
|-------|---------|
| `projects` | Stores project records (title, synopsis/description, year, file_path, dep_id) |
| `project_members` | Student participants for each project (student_name, index_number) |
| `project_supervisors` | Many-to-many link between projects and supervisors |
| `project_tags` | Many-to-many link between projects and tags |
| `tags` | Tag definitions (unique name) |
| `supervisors` | Supervisor records (first_name, last_name, email, dep_id, status) |
| `departments` | Department records with login credentials (dep_id, dep_name, username, password, email) |

### Admin & Security Tables

| Table | Purpose |
|-------|---------|
| `admin_logs` | Admin login credentials (username, hashed password, email) |
| `audit_log` | Activity audit trail (username, action, details, ip_address, created_at) |
| `password_resets` | Token-based password reset records (user_type, user_id, token, expires_at, used) |

### Archive Tables

| Table | Purpose |
|-------|---------|
| `departments_archive` | Archived (soft-deleted) departments |
| `supervisors_archive` | Archived (retired) supervisors |

### Entity Relationship Summary

```
departments (1) ----< (*) projects
departments (1) ----< (*) supervisors
projects    (1) ----< (*) project_members
projects    (*) >----< (*) supervisors       [via project_supervisors]
projects    (*) >----< (*) tags              [via project_tags]
```

---

## Directory Structure

```
rmu_project_finder/
|-- index.php                    # Student search portal
|-- search_projects.php          # AJAX: search with stemming/conflation
|-- get_filters.php              # AJAX: load departments, tags, years
|-- datacon.php                  # Database connection (root level)
|-- project_finder.sql           # Database schema & seed data
|
|-- admin/                       # Admin panel
|   |-- datacon.php              # DB connection
|   |-- csrf.php                 # CSRF token helpers
|   |-- audit_log.php            # Audit logging helper function
|   |-- assets/css/
|   |   |-- styles.min.css       # Base Bootstrap dashboard theme
|   |   +-- custom-theme.css     # Shared unified theme (navy palette)
|   |-- dashboard/
|   |   +-- index.php            # Admin dashboard with stats & audit log
|   |-- login/
|   |   |-- index.php            # Login page
|   |   |-- index.inc.php        # Login handler
|   |   |-- forgot_password.php  # Forgot password form
|   |   +-- reset_password.php   # Reset password handler
|   |-- view_departments/
|   |   |-- index.php            # Department CRUD page
|   |   |-- add_department.php   # Add department handler
|   |   |-- update_department.php# Update department handler
|   |   |-- archive_department.php# Archive department handler
|   |   +-- upload_excel.php     # Bulk upload via Excel
|   |-- view_projects/
|   |   |-- index.php            # All-projects view with filters
|   |   +-- fetch_projects.php   # AJAX: filtered project data
|   +-- logout/
|
|-- dep_admin/                   # Department admin panel
|   |-- datacon.php              # DB connection
|   |-- csrf.php                 # CSRF token helpers
|   |-- audit_log.php            # Audit logging helper function
|   |-- assets/css/
|   |   |-- styles.min.css       # Base Bootstrap dashboard theme
|   |   +-- custom-theme.css     # Shared unified theme
|   |-- dashboard/
|   |   +-- index.php            # Department dashboard
|   |-- login/
|   |   |-- index.php            # Login page
|   |   |-- index.inc.php        # Login handler
|   |   |-- forgot_password.php  # Forgot password form
|   |   +-- reset_password.php   # Reset password handler
|   |-- view_projects/
|   |   |-- index.php            # Project CRUD with add/edit modals
|   |   |-- add_project.php      # Add project handler
|   |   |-- update_project.php   # Update project handler
|   |   +-- upload_excel.php     # Bulk upload via Excel
|   |-- view_supervisors/
|   |   |-- index.php            # Supervisor CRUD page
|   |   |-- add_supervisor.php   # Add supervisor handler
|   |   |-- update_supervisor.php# Update supervisor handler
|   |   +-- upload_excel.php     # Bulk upload via Excel
|   |-- uploads/projects/        # Uploaded PDF files stored here
|   |-- download.php             # Secure file download handler
|   +-- logout/
|
+-- vendor/                      # PHPMailer library (for password resets)
```

---

## Installation & Setup

### Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server (or XAMPP/WAMP for local development)
- Composer (optional, for PHPMailer updates)

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url> rmu_project_finder
   ```

2. **Import the database**
   ```bash
   mysql -u root -p < project_finder.sql
   ```
   This creates the `project_finder` database with all tables and seed data.

3. **Configure database connection**

   Edit `datacon.php` (found in root, `/admin`, and `/dep_admin` directories):
   ```php
   $dbServername = "localhost";
   $dbUsername   = "root";
   $dbPassword   = "";        // Set your MySQL password
   $dbName       = "project_finder";
   ```

4. **Configure email for password resets**

   Update the SMTP settings in the forgot password files (`admin/login/forgot_password.php` and `dep_admin/login/forgot_password.php`):
   ```php
   $mail->Host     = 'smtp.gmail.com';
   $mail->Username = 'your-email@gmail.com';
   $mail->Password = 'your-app-password';
   ```

5. **Set file permissions**
   ```bash
   chmod 755 dep_admin/uploads/projects/
   ```

6. **Access the application**
   - Student portal: `http://localhost/rmu_project_finder/`
   - Admin login: `http://localhost/rmu_project_finder/admin/login/`
   - Department login: `http://localhost/rmu_project_finder/dep_admin/login/`

### Default Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | Admin | *(set during installation)* |
| Department | *(created by admin)* | *(auto-generated, 16 characters)* |

---

## User Roles & Access

### System Admin
- Manages all departments (create, edit, archive)
- Views projects across all departments
- Exports project data as CSV
- Views system audit logs on dashboard
- Resets own password via email

### Department Admin
- Manages projects for their specific department only
- Manages supervisors for their department
- Uploads project files (PDF abstracts)
- Bulk upload via Excel files
- Resets own password via email

### Students (Public)
- Search and browse projects (no login required)
- Filter by department, year, and tags
- Preview and download project PDFs
- Browse by tag cloud

---

## Security Measures

| Measure | Implementation |
|---------|---------------|
| **Password Hashing** | All passwords hashed with `password_hash()` using bcrypt (`PASSWORD_DEFAULT`) |
| **Secure Password Generation** | Department passwords generated using `random_int()` (cryptographically secure), 16 characters |
| **CSRF Protection** | All forms include CSRF tokens via `csrf.php` helpers, validated on submission |
| **Prepared Statements** | All database queries use parameterized prepared statements to prevent SQL injection |
| **Input Sanitization** | All output escaped with `htmlspecialchars()` to prevent XSS |
| **File Upload Validation** | PDF-only uploads, 10MB size limit, error handling for all PHP upload error types |
| **Excel Upload Validation** | 5MB size limit for Excel bulk uploads |
| **Information Hiding** | PHPMailer errors logged server-side via `error_log()`, generic messages shown to users |
| **Password Reset Tokens** | Cryptographically random 32-byte tokens, 1-hour expiry, single-use, one-token-per-user policy |
| **Audit Logging** | All sensitive operations logged with username, action, details, IP address, and timestamp |
| **Session Management** | Session-based authentication with redirect guards on all protected pages |

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.x |
| **Database** | MySQL 8.x |
| **Frontend Framework** | Bootstrap 5.3 (admin template with `styles.min.css`) |
| **Custom Styling** | Unified `custom-theme.css` with CSS custom properties (navy palette) |
| **JavaScript** | Vanilla JS with AJAX (Fetch API) for dynamic content |
| **Email** | PHPMailer 6.x with SMTP (Gmail) |
| **Icons** | Tabler Icons (via admin template) |
| **Search** | Custom English suffix-stripping stemmer for conflation |
| **Charts** | ApexCharts (available in admin dashboard) |

---

## Search & Conflation

The student search portal implements an English suffix-stripping stemmer that reduces words to their root forms before matching against the database. This means:

- Searching **"computing"** also finds projects about **"computer"**, **"computed"**, **"computation"**
- Searching **"systems"** also matches **"system"**
- Searching **"management"** also matches **"manage"**, **"managing"**

The stemmer handles common English suffixes including: `-ing`, `-tion`, `-sion`, `-ment`, `-ness`, `-ous`, `-ive`, `-able`, `-ible`, `-ful`, `-less`, `-ly`, `-er`, `-est`, `-ed`, `-es`, `-s`.

Both the original search terms and their stemmed versions are used in the query to maximize recall.

---

## Auto-Tag Generation

When department admins add or edit a project, tags are automatically suggested based on the project title. The system:

1. Splits the title into individual words
2. Removes common English stop words (the, a, is, for, of, etc.)
3. Removes academic stop words (study, analysis, development, design, system, etc.)
4. Filters out words shorter than 3 characters
5. Deduplicates and selects up to 6 keyword tags

Tags can be manually edited after auto-generation.

---

*Regional Maritime University - Student Project Repository System*
>>>>>>> bbf7dc6591ee2d7cac7d32a5613ce2a52ef5fa49
