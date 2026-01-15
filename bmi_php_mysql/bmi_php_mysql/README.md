# BMI Management System (PHP + MySQL)

## Assignment Requirements Covered
PROJECT NAME=BMI MANAGEMENT SYSTEM 
NAMES :
1. C1220443 Abdimajid Abdiaziz Omar 
2. C1220644 Faysal Ahmed Mohamud  
3. C1220935 Zakeria Mohamed Abdullahi 
4.C1221192 Asad Ali Abdi  

BMI Management System – Project Description

The BMI Management System is a web-based application developed using PHP and MySQL to help users calculate, store, and manage their Body Mass Index (BMI) records. The system allows users to register, log in securely, calculate their BMI using height and weight, and maintain a personal BMI history over time.

The application implements core PHP and MySQL concepts, including database connection, CRUD operations (Create, Read, Update, Delete), user authentication, cookies, and sessions. A secure dashboard (control panel) is provided where authenticated users can manage their BMI records, view health tips based on BMI results, and update their profile information.

The system uses sessions to track logged-in users and control access to protected pages, and it includes an automatic session expiration feature to enhance security. All data displayed in the application is retrieved dynamically from the MySQL database.

The BMI Management System follows good programming practices with proper validation, structured code, and a clean user interface, making it suitable for academic projects and real-world learning of web application development using PHP and MySQL.

Key Features

User registration and login system

Secure authentication using sessions and cookies

BMI calculation based on height and weight

BMI status classification (Underweight, Normal, Overweight, Obese)

BMI history management (Insert, Update, Delete records)

Dashboard (control panel) for users

Automatic session expiration

MySQL database integration

- Public Home page (index.php) with header, horizontal navigation, content, footer
- External stylesheet (assets/css/style.css)
- Database connection (config/db.php)
- User registration (register.php): auto ID, first name, last name, sex, username, password, phone, email, profile picture, user type, user status
- Login (login.php): remember me (checkbox), sign up, forgot password
- Dashboard (control panel) with CRUD:
  - BMI Records CRUD (dashboard/bmi.php)
  - Users CRUD (dashboard/users.php) (admin only)
- Cookies & Sessions used to track user info and control access
- Logout (logout.php) destroys session and cookie
- Automatic session expiry after 5 minutes of inactivity (config/auth.php)

## Setup (XAMPP)
1) Copy folder to: C:\xampp\htdocs\bmi_php_mysql
2) Start Apache + MySQL
3) Import database.sql in phpMyAdmin
4) Visit: http://localhost/bmi_php_mysql/index.php

## Default Admin
username: admin
password: admin123

If admin doesn't login, create a new admin account using register.php (select user_type=admin).
