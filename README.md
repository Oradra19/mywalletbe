# Mini E-Wallet Backend

Backend API for Mini E-Wallet built with Laravel 12 and Laravel Sanctum.

## Tech Stack

* Laravel 12
* Laravel Sanctum
* MySQL
* PHP 8.2+

---

## Installation

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

Copy environment file:

```bash
cp .env.example .env
```

Update database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mywallet
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Database Migration & Seeder

```bash
php artisan migrate --seed
```

### 5. Start Development Server

```bash
php artisan serve
```

Backend URL:

```text
http://127.0.0.1:8000
```

---

## Demo Accounts

| Email                                         | Password    |
| --------------------------------------------- | ----------- |
| [usera@example.com](mailto:usera@example.com) | password123 |
| [userb@example.com](mailto:userb@example.com) | password123 |
| [userc@example.com](mailto:userc@example.com) | password123 |

---

## Features

* Authentication with Laravel Sanctum
* User Balance
* Transfer Funds
* Transaction History
* Pagination
* Logout
* Request Validation
* Concurrency Protection

---

## Security

* Token-based Authentication
* Password Hashing
* Protected API Routes
* Form Request Validation
* Database Transactions
* Row-Level Locking (lockForUpdate)

---

## API Endpoints

### Authentication

```http
POST /api/login
POST /api/logout
```

### User

```http
GET /api/user
GET /api/balance
```

### Transactions

```http
GET /api/transactions
POST /api/transfer
```

---

## Scalability Considerations

This application is designed to support concurrent users by using:

* Service Layer Architecture
* Database Transactions
* Row-Level Locking
* Paginated Transaction History

These measures help maintain data consistency and prevent race conditions during simultaneous transfers.
