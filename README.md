# Al Amad School Platform

A modern, responsive school management system built with PHP and Firebase. This platform provides dedicated portals for Administrators, Teachers, and Parents to streamline school operations, track student progress, and manage communications in real-time.

## Features

- **Role-Based Dashboards**: 
  - **Admin**: Overall school statistics, enrollment tracking, revenue management, and global announcements.
  - **Teacher**: Class management, attendance tracking, grading, and direct communication with students/parents.
  - **Parent**: Access to children's grades, attendance history, school notices, and payment tracking.
- **Modern User Interface**: Responsive design with a polished aesthetic, featuring both Dark and Light modes.
- **Real-time Synchronization**: Powered by Firebase Realtime Database for instant updates across all portals.
- **AI Chatbot Integration**: Integrated premium AI assistant widget for automated help and support.
- **Secure Authentication**: Firebase Auth for secure login and role-based access control.

## Technologies Used

- **Backend**: PHP 8.2+ (Required extensions: `openssl`, `mbstring`, `curl`)
- **Database**: Firebase Realtime Database
- **Authentication**: Firebase Auth
- **Dependencies**: Composer (for Kreait Firebase SDK)
- **Frontend**: Vanilla HTML5, CSS3, JavaScript

## Project Structure

- `/api/` - Backend API endpoints (Firebase interaction)
- `/assets/` - Static files (CSS, JS, images)
- `/auth/` - Authentication pages (login, logout)
- `/dashboard/` - Role-specific portal portals
- `/includes/` - Shared components (Firebase connection, auth checks, sidebar, etc.)
- `firebase-credentials.json` - **CRITICAL**: Your Firebase service account credentials.

## Getting Started

To run this project locally, you need PHP and Composer installed.

### 1. Install Dependencies
Run the following command in the project root to install the Firebase SDK:
```bash
composer install
```

### 2. Configure Firebase
Ensure you have a `firebase-credentials.json` file in the project root. This file contains your Firebase Service Account keys.

### 3. Initialize the Database (Optional Seeding)
To seed initial demo data (Admin, Teachers, Classes), run:
```bash
php setup_db.php
```

### 4. Start the Development Server
```bash
php -S localhost:8000
```

### 5. Access the Application
Open your web browser and navigate to: http://localhost:8000/

## Windows Setup Guide
For a detailed step-by-step guide on setting up this project on Windows, please refer to the `windows_setup.md` document.

## License

All rights reserved.

