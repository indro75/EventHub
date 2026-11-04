# 📅 Event Management System

A comprehensive, database-driven web application designed to simplify and automate event creation, participant registration, and record-keeping. Built using **Native PHP** and **MySQL**, this system replaces inefficient manual tracking (spreadsheets, paper forms) with a secure, centralized, and responsive digital platform.

---

## 🌟 Key Features

### 👨‍💼 Administrator Portal
- **Secure Login & Dashboard:** Role-based authentication with a statistical overview of events, users, and registrations.
- **Event Management (CRUD):** Create, view, update, and delete events with image uploads.
- **Category Management:** Add, update, and delete event categories dynamically.
- **Participant Tracking:** View detailed lists of registered participants for any specific event.
- **User Management:** View all registered users and their registration history.

### 👤 User Portal
- **Registration & Login:** Secure account creation with hashed passwords.
- **Event Browsing:** View all active events with live seat availability progress bars.
- **Smart Registration:** Register for events with built-in guards against overbooking, double-booking on the same day, and past events.
- **Profile Management:** Update personal information and change passwords securely.
- **E-Ticket Generation:** View and print a unique, professional e-ticket with a scannable QR code and barcode.

### 🛡️ Security & System Features
- **Session Management:** Secure PHP sessions with auto-logout on inactivity and session regeneration to prevent hijacking.
- **SQL Injection Prevention:** All database queries use Prepared Statements (`bind_param`).
- **XSS Protection:** All user inputs are sanitized using `htmlspecialchars()`.
- **Normalized Database:** 5 structured tables ensuring zero data redundancy.

---

## 🛠️ Technology Stack

| Component | Technology |
| :--- | :--- |
| **Backend Logic** | Native PHP (No Frameworks) |
| **Database** | MySQL |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Web Server** | Apache |
| **Development Env** | XAMPP |

---

## 🗄️ Database Design

The system uses a normalized MySQL database consisting of **5 main tables**:
1. `admins` - Stores administrator credentials and info.
2. `users` - Stores registered user credentials and info.
3. `categories` - Stores dynamic event categories.
4. `events` - Stores event details, dates, locations, and images.
5. `registrations` - Maps users to events (Many-to-Many relationship).

---

## 🚀 Installation & Setup Guide

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/) (includes Apache, MySQL, and PHP).

### Step-by-Step Setup
1. **Clone/Download** this repository.
2. Copy the `event-management-system` folder into your XAMPP `htdocs` directory (usually `C:\xampp\htdocs\`).
3. Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
4. Open your browser and navigate to `http://localhost/phpmyadmin`.
5. Create a new database named **`event_management`**.
6. Select the new database, click the **Import** tab, and import the **`database.sql`** file located in the root of this project.
7. Open your browser and navigate to: 
   👉 **`http://localhost/event-management-system/`**

---

## 🔑 Default Credentials

### Admin Access
- **Role:** Select "Admin" on the login page
- **Email:** `admin@ems.com`
- **Password:** `admin123`

### User Access
- Click **"Register"** on the homepage to create a new user account.

---

## 📁 Project Structure

```text
event-management-system/
│
├── admin/                  # Administrator dashboard and CRUD files
├── user/                   # User dashboard, booking, and ticket files
├── assets/
│   ├── css/                # Custom responsive stylesheets
│   └── js/                 # Vanilla JavaScript for UI interactions
├── uploads/
│   └── events/             # Directory for uploaded event images
├── config.php              # Database connection and helper functions
├── session_check.php       # Security guard for protected pages
├── database.sql            # Complete database schema and default admin
└── README.md               # Project documentation
