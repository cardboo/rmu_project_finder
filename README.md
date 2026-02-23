# RMU Project Finder

A web-based repository system for searching, managing, and accessing academic student projects at Regent Maritime University (RMU). The platform serves as a centralized hub for students, departments, and administrators to organize and explore project submissions.

## Overview

The RMU Project Finder is a comprehensive project management and discovery platform designed specifically for academic institutions. It enables departments to manage projects, students to submit their work, and the university community to search and access student projects through an intuitive web interface.

## Features

### Public Features
- **Project Search**: Real-time search functionality to find projects by title, student name, tags, and keywords
- **Project Browsing**: Browse projects organized by department and academic year
- **Project Details**: View comprehensive project information including synopsis, abstract, and student information
- **File Downloads**: Download project files and documentation directly from the repository
- **Responsive Design**: Modern, mobile-friendly interface with a clean user experience

### Administrative Features

#### Admin Dashboard (Super Admin)
- **Department Management**: Create, update, and archive academic departments
- **Project Oversight**: View and manage all projects across departments
- **User Management**: Oversee all administrative users across the system
- **Bulk Operations**: Upload and process Excel files for batch department management
- **Dashboard Analytics**: View statistics and project overview

#### Department Admin Dashboard
- **Project Management**: Create, update, and manage projects within the department
- **Supervisor Management**: Add and manage project supervisors
- **Student Management**: Manage student information and supervisors
- **Bulk Uploads**: Import supervisor and project data via Excel files
- **Download Reports**: Generate and download project reports

### System Architecture

The application is built with a **multi-role architecture** consisting of:

1. **Public Portal** (`index.php`)
   - Main entry point for public project search
   - Real-time search with debounce optimization
   - Project card display with filtering capabilities

2. **Admin Portal** (`/admin/`)
   - Super admin control panel
   - Department and project management
   - User administration

3. **Department Portal** (`/dep_admin/`)
   - Department-level project management
   - Supervisor and student management
   - Report generation and downloads

### Modular Architecture (New)

As of the recent refactoring, the application now features a clean, component-based architecture:

- **Core System** (`app/core/`)
  - Centralized database configuration
  - Authentication utilities
  - Role-based middleware

- **Reusable Components** (`app/components/`)
  - Role-aware sidebar navigation
  - Shared navbar and footer
  - Reduces code duplication by ~60%

- **Layout Templates** (`app/layouts/`)
  - Admin, Department Admin, and Public layouts
  - Consistent UI across all roles

- **Modular Pages** (`modules/`)
  - Separated business logic (controllers) from presentation (views)
  - Easy to maintain and update

**See [ARCHITECTURE_GUIDE.md](./ARCHITECTURE_GUIDE.md) for detailed migration instructions and the new development patterns.**

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Libraries & Dependencies**:
  - PHPOffice/PHPSpreadsheet (Excel file handling)
  - PHPMailer (Email functionality)
- **Database Connection**: MySQLi (Object-Oriented)

## Project Structure

The project follows a modular architecture that separates concerns and eliminates code duplication.

```
rmu_project_finder/
├── app/                       # NEW - Core application framework
│   ├── core/
│   │   ├── config.php        # Single database configuration
│   │   ├── auth.php          # Authentication utilities
│   │   └── middleware.php    # Role-based access control
│   ├── layouts/
│   │   ├── admin.layout.php       # Admin page template
│   │   ├── dep_admin.layout.php   # Department admin template
│   │   └── public.layout.php      # Public portal template
│   ├── components/
│   │   ├── sidebar.php       # Role-aware sidebar
│   │   ├── navbar.php        # Shared navbar
│   │   └── footer.php        # Shared footer
│   └── partials/
│       ├── head.php          # HTML head includes
│       ├── styles.php        # CSS includes
│       └── scripts.php       # JavaScript includes
│
├── modules/                   # NEW - Feature modules
│   ├── admin/
│   │   ├── dashboard/
│   │   │   ├── dashboard.php      # Controller
│   │   │   └── dashboard.view.php # View
│   │   ├── view_projects/
│   │   ├── view_departments/
│   │   └── [more modules...]
│   ├── dep_admin/
│   │   └── [similar structure]
│   └── public/
│       └── [public features]
│
├── index.php                  # Public portal (legacy wrapper)
├── search_projects.php        # AJAX search endpoint
├── datacon.php                # Database config (backward compat layer)
│
├── admin/                     # Legacy admin interface (delegates to modules)
│   ├── dashboard/
│   ├── login/
│   ├── logout/
│   ├── view_projects/
│   ├── view_departments/
│   └── datacon.php
│
├── dep_admin/                 # Legacy dept admin interface (delegates to modules)
│   ├── dashboard/
│   ├── login/
│   ├── logout/
│   ├── view_projects/
│   ├── view_supervisors/
│   ├── download.php
│   └── datacon.php
│
├── assets/                    # Static resources
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript files
│   ├── images/                # Images and logos
│   └── libs/                  # Third-party libraries
│
├── vendor/                    # Composer dependencies
├── composer.json              # PHP dependency configuration
├── ARCHITECTURE_GUIDE.md      # NEW - Detailed architecture documentation
├── TEMPLATE_CONTROLLER_ADMIN.php  # NEW - Controller template
└── TEMPLATE_VIEW_ADMIN.php        # NEW - View template
```

**Key Improvements:**
- `app/` directory contains shared framework code (no duplication)
- `modules/` directory contains feature logic (clean separation)
- Legacy files maintain backward compatibility by delegating to `modules/`
- See [ARCHITECTURE_GUIDE.md](./ARCHITECTURE_GUIDE.md) for detailed structure explanation

## Database Schema Overview

The system uses the following main tables:

### Core Tables
- **departments**: Academic departments
- **projects**: Student projects with metadata
- **students**: Student information
- **supervisors**: Project supervisors
- **users**: Admin users (super admin and department admins)

### Additional Tables
- **tags**: Project tags/categories
- **project_files**: Uploaded project files
- **project_status**: Project workflow status

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer (for dependency management)
- Web server (Apache, Nginx, etc.)

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/cardboo/rmu_project_finder.git
   cd rmu_project_finder
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Database Connection**
   - Edit `datacon.php` in the root and each admin folder
   - Update credentials:
     ```php
     $dbServername = "localhost";
     $dbUsername = "your_username";
     $dbPassword = "your_password";
     $dbName = "project_finder";
     ```

4. **Set Up Database**
   - Create a new MySQL database named `project_finder`
   - Import the database schema (if provided in `database/schema.sql`)
   - Or run migrations if available

5. **Configure Web Server**
   - Point your web root to the project directory
   - Ensure `.htaccess` rules are enabled (if using Apache)
   - Set proper file permissions (755 for directories, 644 for files)

6. **Access the Application**
   - Public: `http://localhost/rmu_project_finder/`
   - Admin: `http://localhost/rmu_project_finder/admin/`
   - Department Admin: `http://localhost/rmu_project_finder/dep_admin/`

## Usage Guide

### For Students & Public Users
1. Navigate to the public portal homepage
2. Use the search bar to find projects by:
   - Project title
   - Student name
   - Tags and keywords
3. Click on a project card to view details
4. Download project files using the download button

### For Super Administrators
1. Log in to `/admin/`
2. **Dashboard**: View system overview and statistics
3. **Department Management**: 
   - Add new departments
   - Update department information
   - Archive departments
4. **Project Management**: Review and manage all projects
5. **User Management**: Create and manage admin accounts

### For Department Administrators
1. Log in to `/dep_admin/`
2. **Dashboard**: View department-specific statistics
3. **Project Management**:
   - Submit new projects
   - Update project information
   - Manage project status
4. **Supervisor Management**: 
   - Add supervisors
   - Update supervisor details
   - Bulk upload supervisor data
5. **Download Reports**: Export project data

## Key Features Explained

### Real-Time Search
- **Debounce Optimization**: Search queries are debounced (300ms) to reduce server load
- **Multi-field Search**: Searches across project titles, student names, and tags
- **Instant Results**: Results display as you type

### File Management
- **Secure Downloads**: Files are served through a download handler (`dep_admin/download.php`)
- **Multiple Formats**: Support for various file formats (PDF, DOCX, etc.)

### Excel Import
- **Bulk Operations**: Import multiple records via Excel files
- **Data Validation**: Built-in validation for uploaded data
- **Error Handling**: Clear error messages for data issues

## Security Features

- **Authentication**: Role-based access control (Super Admin, Department Admin, Public)
- **Session Management**: Secure session handling
- **Database Security**: MySQLi prepared statements (recommended implementation)
- **File Upload Security**: Proper file validation and storage
- **Access Control**: Route-based access restrictions

## Best Practices for Developers

### Adding New Features
1. Maintain separation between public (`/`), admin (`/admin/`), and department (`/dep_admin/`) portals
2. Use separate database configuration files for security
3. Implement proper error handling and logging
4. Validate all user inputs on both client and server sides

### Database Queries
- Use prepared statements to prevent SQL injection
- Always validate and sanitize user inputs
- Index frequently searched columns for performance

### Frontend Development
- Follow the existing CSS and JavaScript patterns
- Use the provided UI components from Bootstrap and libraries
- Test responsiveness across devices

## Troubleshooting

### Common Issues

**Database Connection Errors**
- Verify database credentials in `datacon.php`
- Ensure MySQL server is running
- Check database user permissions

**Search Not Working**
- Verify `search_projects.php` is accessible
- Check browser console for JavaScript errors
- Ensure database has project records

**File Download Issues**
- Verify file paths in the database are correct
- Check file permissions (readable by web server)
- Ensure `dep_admin/download.php` has proper error handling

**Login Issues**
- Clear browser cookies and cache
- Verify user accounts exist in the database
- Check session configuration in PHP

## Performance Optimization

- **Search Debouncing**: Implemented to reduce server load during live search
- **Caching**: Consider implementing query caching for frequently accessed data
- **Indexing**: Database indexes on search fields for faster queries
- **Pagination**: Implement pagination for large result sets

## Future Enhancements

- Advanced filtering options (date range, supervisor, etc.)
- Full-text search capabilities
- Project categorization and tagging system
- Email notifications for project submissions
- Analytics and reporting dashboard
- API endpoints for third-party integration
- Mobile application

## Contributing

To contribute to this project:

1. Create a feature branch (`git checkout -b feature/AmazingFeature`)
2. Commit your changes (`git commit -m 'Add AmazingFeature'`)
3. Push to the branch (`git push origin feature/AmazingFeature`)
4. Open a Pull Request

## Support & Documentation

For issues, questions, or documentation:
- Check existing GitHub issues
- Create a new issue with detailed description
- Review code comments and inline documentation

## License

This project is proprietary to Regent Maritime University. Unauthorized use is prohibited.

## Contact

For support and inquiries, contact the IT department or project administrator.
