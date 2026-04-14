# Library Management System

A web-based library management system with separate admin and student portals, built with PHP and MySQL.

## Features

- **Admin Dashboard**: Manage books, students, issue/return transactions, and view statistics
- **Student Portal**: Browse available books, view borrowing history, and manage profile
- **Authentication**: Secure login with role-based access control
- **Fine System**: Automatic fine calculation for overdue books (₹5/day)

## Tech Stack

- PHP 7.4+
- MySQL
- HTML/CSS/JavaScript
- PDO for database operations

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/library-management-system.git


Import the database schema from database.sql

Configure database credentials in config.php:

php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'library_management_pro';
Run admin_setup.php once to create the admin account, then delete it for security

Access the application via login.php

Default Credentials
After running admin_setup.php, use the credentials you created to login.

Project Structure
File	Description
admin_dashboard.php	Admin panel interface
student_dashboard.php	Student portal interface
login.php	Authentication page
books.php	Book management API
transactions.php	Issue/return handling
users.php	User management API
config.php	Database configuration
auth.php	Authentication utilities
Security Notes
Delete admin_setup.php after creating the first admin account

Ensure config.php has proper database credentials

Use HTTPS in production

License
MIT License - See LICENSE for details.

Authors
Kaiser Mohiuddin - team leader
Auzair
Momin
Nawazish
