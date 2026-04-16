# Mobus - Bus Booking & Management System

Mobus is a comprehensive web-based platform designed to streamline bus ticket bookings, trip scheduling, and ticket verification. Built with **PHP** and **MySQL**, it provides a professional solution for transport companies to manage their fleet and for passengers to book tickets conveniently.

## 🚀 Key Features

### For Passengers (The App)
- **Account Registration:** Simple signup and secure login.
- **Trip Search:** Find buses based on origin, destination, and travel date.
- **Seat Selection:** A visual seat picker to choose exactly where you want to sit.
- **Mobile Money Payment:** Simulated secure checkout for UGX payments.
- **Digital Tickets:** Automatic generation of tickets with QR codes for easy boarding.

### For Operators (Transport Companies)
- **Fleet Management:** Add, edit, or remove buses from your fleet.
- **Trip Scheduling:** Create and manage bus trips, fares, and departure times.
- **Custom Routes:** Define your own routes unique to your company.

### For Verifiers (Conductors)
- **QR Code Scanning:** Quickly verify passenger tickets using a phone camera or laptop.
- **Real-time Validation:** Instantly check if a ticket is valid, already used, or unpaid.

### For Super Admins
- **User Provisioning:** Create and manage accounts for staff (Operators and Verifiers).
- **System Metrics:** View total users, active buses, and system-wide revenue at a glance.
- **Global Routes:** Define standard routes available to all operators.

---

## 🛠️ Technology Stack

- **Backend:** PHP (PDO for secure database interactions)
- **Frontend:** Vanilla HTML5, CSS3 (Modern dark-mode focused design)
- **Database:** MySQL
- **Interactions:** JavaScript (AJAX for live searching and seat polling)
- **Authentication:** Session-based with Role-Based Access Control (RBAC)

---

## 📂 Project Structure

- `admin/`: Super Admin management dashboard and user provisioning.
- `operator/`: Logistics and scheduling tools for transport managers.
- `passenger/`: The customer-facing booking application.
- `verifier/`: Conductor-focused ticket verification tool.
- `includes/`: Core reusable logic like authentication checks and theme helpers.
- `config/`: Database connection and global settings.
- `api/`: AJAX endpoints for real-time seat availability and search.

---

## 📖 Presentation Guide (For Students)

If you are asked about the system during your project defense, here are some key concepts to explain:

### 1. Database Security (PDO)
We use **PDO (PHP Data Objects)** instead of standard `mysqli`. Explain that PDO uses **Prepared Statements**, which protect the system from **SQL Injection** attacks by separating the SQL command from the user data.

### 2. Password Hashing
When a user registers, we don't save their plain password. We use `password_hash()`. On login, we use `password_verify()`. This ensures that even if someone steals the database, they cannot see user passwords.

### 3. Real-time Seat Polling
In `passenger/book.php`, the system checks for booked seats every 5 seconds using `setInterval()`. This prevents two people from booking the same seat at the same time.

### 4. Role-Based Access Control (RBAC)
Every page checks the user's role before loading. If a passenger tries to access the Admin dashboard, the system detects this and redirects them to the correct page using the `checkRole()` function in `includes/auth_check.php`.

---

## ⚙️ Installation & Setup

1.  **Database Setup:** Import the provided `.sql` file into your MySQL database (e.g., via phpMyAdmin).
2.  **Configuration:** Open `config/db.php` and update your database credentials (host, dbname, username, password).
3.  **Run:** Open the project in your local server (XAMPP/WAMP) and navigate to `register.php` or `login.php`.

---

*Full project developed for academic submission. Focus on clean code, security best practices, and user experience.*
