# ⚽ Beijing FC — Football Club Management System

Welcome to the **Beijing FC Football Club Management System**, a comprehensive web and RESTful API application built with Laravel 12, Bootstrap 5, and MySQL.

---

## 📋 Features & Functionalities

- **MVC Architecture**: Structured separation of concern with Laravel Controllers, Models, and Blade views.
- **Role-Based Access Control (RBAC)**: Distinct interfaces and permissions for Admin, Coach, Treasurer, and Members.
- **RESTful APIs & Mobile Support**: Sanctum-authenticated API endpoints (`/api/*`) for mobile and third-party integrations.
- **MySQL Stored Procedures**: Pre-compiled SQL stored procedures for complex reporting and transactional operations (Player Stats, Squad List, Upcoming Matches, Match Recording, User Activity).
- **Progressive Web Application (PWA)**: Desktop/mobile installable with service worker offline support and dynamic app manifest.
- **Secure Authentication**: Password hashing with Bcrypt/Salting, active session checks, and login rate limiting.
- **M-Pesa STK Push Integration**: Real-time payment processing for club member contributions and match fees.
- **Automated Team Generator**: Algorithmic team balancing for internal friendly matches.
- **Audit Logging**: Comprehensive activity log system for monitoring administrative changes.

---

## 🛠️ Technology Stack

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL / MariaDB (SQLite supported for testing)
- **Frontend**: Blade, Bootstrap 5, JavaScript (Fetch/AJAX), FontAwesome
- **Authentication**: Laravel Sanctum & Breeze
- **PWA**: Service Worker (`sw.js`) & App Manifest (`manifest.json`)

---

## ⚙️ Installation & Setup Guide

### 1. Requirements
Ensure you have the following installed on your machine:
- PHP >= 8.2
- Composer
- Node.js & NPM
- XAMPP / WAMP (MySQL Server)

### 2. Clone & Setup
```bash
git clone https://github.com/MWANZIAMUTINDA/BEIJING_FC.git
cd BEIJING_FC
composer install
npm install && npm run build
```

### 3. Environment Configuration
Copy the `.env.example` file and configure your database settings:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your MySQL connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beijingg1
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup & Stored Procedures
Run the migrations to set up tables and compile the MySQL stored procedures:
```bash
php artisan migrate --seed
```

Alternatively, import the pre-packaged SQL dump:
```bash
mysql -u root beijingg1 < beijing_fc.sql
```

### 5. Running the Application
Start the local server:
```bash
php artisan serve
```
Visit `http://localhost:8000` in your browser.

---

## 🧪 Automated Testing

Run the full automated test suite (56 tests):
```bash
php artisan test --env=testing
```

---

## 📚 API Endpoints Overview

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/login` | Authenticate & get Sanctum Token |
| `GET` | `/api/dashboard` | Role-based dashboard stats |
| `GET` | `/api/matches` | Get list of scheduled fixtures |
| `POST` | `/api/payments/pay` | Initiate M-Pesa STK Push |
| `GET` | `/api/sp/player/{id}/stats` | Invoke `sp_get_player_stats` procedure |
| `GET` | `/api/sp/team/{id}/squad` | Invoke `sp_get_team_squad` procedure |
| `GET` | `/api/sp/matches/upcoming` | Invoke `sp_get_upcoming_matches` procedure |

---

## 📄 License
This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
