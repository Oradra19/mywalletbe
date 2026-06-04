# DESIGN.md

## Business Context

This project is a Mini E-Wallet application.

Users can:

* Login
* View balance
* Transfer money
* View transaction history

The system is designed as the foundation of a future financial platform.

---

## System Architecture

Client (React)

↓

Laravel API

↓

MySQL Database

The frontend communicates only through REST APIs.

The backend is responsible for all business rules.

---

## Why Service Layer?

Transfer operations contain business rules.

Examples:

* Prevent self transfer
* Validate balance
* Generate transaction codes

Placing this logic inside services makes the system easier to maintain and test.

---

## Why Store Balance In Users Table?

Current balance is requested frequently.

Keeping balance directly in users table allows:

* Faster dashboard loading
* Simpler queries

Alternative approaches such as ledger calculations were intentionally avoided because they add complexity that is unnecessary for this assignment.

---

## Why Use Database Transactions?

A transfer changes multiple records:

1. Sender balance
2. Receiver balance
3. Transaction history

If one operation fails while another succeeds, balances become inconsistent.

Database transactions ensure all operations succeed or all operations fail.

---

## Why Use lockForUpdate()?

The application may be accessed by multiple users simultaneously.

Example:

User balance = 100000

Request A transfers 80000

Request B transfers 80000

Without row locking:

Both requests may succeed.

With lockForUpdate():

Only one request can modify the balance at a time.

This prevents race conditions.

---

## Transaction History Strategy

Transfers are stored permanently.

Transactions are treated as immutable records.

Reason:

Financial systems require traceability and auditability.

---

## Transaction Code Strategy

Each transfer receives a unique identifier.

Example:

TRX-20260604-A8F3K2

Purpose:

* Tracking
* Auditing
* Future integrations

---

## Scalability Considerations

The design allows future features:

* Top Up
* Withdrawal
* Notifications
* Audit Logs
* Payment Gateway Integration

Without major database redesign.

---

## Assumptions

* Currency is IDR
* One wallet per user
* Transfer only between registered users
* No top-up feature
* No withdrawal feature
* No transaction reversal
