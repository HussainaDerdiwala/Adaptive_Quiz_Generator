# Adaptive Quiz Generator

A dynamic, web-based application designed to host, manage, and process interactive quizzes. This project features a complete user authentication system, personal dashboards, and a dedicated interface for taking and evaluating quizzes.

## Features

* **User Authentication:** Complete registration, login, and secure session management.
* **User Portal:** Dedicated dashboards and profile management for registered users.
* **Quiz Engine:** Interactive quiz-taking interface with automated scoring and result tracking.
* **Modular Design:** Clean architecture utilizing reusable components for database connections and UI layouts.

## Tech Stack

* **Backend:** PHP
* **Database:** MySQL / MariaDB
* **Frontend:** HTML, CSS, JavaScript

## Repository Structure

```text
adaptive_quiz_generator/
├── css/
│   └── style.css            # Application stylesheets
├── includes/
│   ├── db.php               # Database connection configuration
│   └── header.php           # Reusable UI header
├── js/
│   └── main.js              # Client-side scripts
├── dashboard.php            # User dashboard interface
├── database.sql             # SQL schema for database setup
├── index.php                # Main landing page
├── login.php                # User login page
├── logout.php               # Session termination script
├── profile.php              # User profile management
├── quiz.php                 # Core quiz-taking interface
├── register.php             # New user registration page
├── register_action.php      # Backend registration processor
├── result.php               # Quiz results and scoring display
└── submit_quiz.php          # Backend quiz submission handler
