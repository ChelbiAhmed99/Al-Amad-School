# Al Amad School Platform

A modern, responsive school management system built with PHP and SQLite. This platform provides dedicated portals for Administrators, Teachers, and Parents to streamline school operations, track student progress, and manage communications.

## Features

- **Role-Based Dashboards**: 
  - **Admin**: Overall school statistics, enrollment tracking, revenue management, and global announcements.
  - **Teacher**: Class management, attendance tracking, grading, and direct communication with students/parents.
  - **Parent**: Access to children's grades, attendance history, school notices, and payment tracking.
- **Modern User Interface**: Responsive design with a polished aesthetic, featuring both Dark and Light modes.
- **AI Chatbot Integration**: Integrated premium AI assistant widget for automated help and support.
- **Secure Authentication**: Role-based access control protecting secure routes and dashboard functionality.

## Technologies Used

- **Backend**: PHP 8+
- **Database**: SQLite (managed via PDO)
- **Frontend**: Vanilla HTML5, CSS3, and JavaScript
- **Fonts**: Google Fonts (Outfit)

## Project Structure

- `/api/` - Backend API endpoints (e.g., login handling)
- `/assets/` - Static files (CSS, JS, images)
- `/auth/` - Authentication pages (login, logout)
- `/dashboard/` - Role-specific portal pages
- `/includes/` - Shared components (database connection, auth checks, sidebar, etc.)

## Getting Started

To run this project locally, you need PHP installed on your system.

### Prerequisites: Installing PHP
If you do not have PHP installed, follow the guide for your operating system:

**Windows:**

**Option A: Using XAMPP (Recommended for beginners)**
1. Download **XAMPP for Windows** from [apachefriends.org](https://www.apachefriends.org/index.html).
2. Run the installer and finish the setup (default settings are fine).
3. Open the **XAMPP Control Panel**.
4. Click the **"Shell"** button on the right side. This will open a command prompt where PHP is already configured.
5. In this shell, navigate to your project folder using the `cd` command (e.g., `cd Desktop\Olfa Project`).
6. You can now run the initialization commands below.

**Option B: Native PHP Installation (Command Line)**
1. Download the latest PHP `.zip` file from [windows.php.net](https://windows.php.net/download/).
2. Create a new folder named `php` in your C: drive (`C:\php`) and extract the ZIP contents into it.
3. Open the Windows Start menu, search for **"Environment variables"**, and click **"Edit the system environment variables"**.
4. Click the **"Environment Variables..."** button at the bottom.
5. Under the "System variables" section, scroll down to find the `Path` variable, select it, and click **"Edit..."**.
6. Click **"New"**, type `C:\php`, and click **"OK"** on all windows to apply the changes.
7. Open a new Command Prompt (`cmd`) to run the initialization commands below.

**macOS:**
You can easily install PHP using [Homebrew](https://brew.sh/):
```bash
brew install php
```

**Linux (Ubuntu/Debian):**
Install PHP and the required SQLite extension:
```bash
sudo apt update
sudo apt install php php-sqlite3
```

Verify your installation by opening a terminal and running `php -v`.

### 1. Initialize the Database
If this is your first time setting up the project, or if you need to reset the database, run the setup script to create the necessary tables and seed initial data:

```bash
php setup_db.php
```

### 2. Start the Development Server
Use PHP's built-in web server to run the application locally without needing Apache or Nginx:

```bash
php -S localhost:8000
```

### 3. Access the Application
Open your web browser and navigate to:
- **Homepage:** http://localhost:8000/
- **Default Login Portal:** http://localhost:8000/auth/login.php

> **Note**: For development purposes, session checks can be temporarily bypassed via `includes/auth_check.php`.

## License

All rights reserved.
