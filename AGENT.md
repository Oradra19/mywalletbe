# AGENT.md

## Purpose

This document defines coding rules and implementation standards for AI coding agents working on this project.

Always follow these rules before creating or modifying code.

---

## Development Rules

### Controllers

Controllers must remain thin.

Allowed:

* Receive request
* Call service
* Return response

Not allowed:

* Transfer logic
* Authentication logic
* Complex database operations

---

### Services

Business logic belongs in services.

Examples:

* AuthService
* TransferService

All balance calculations and transfer operations must be handled in services.

---

### Validation

Always use Form Requests.

Never write validation directly inside controllers.

Examples:

* LoginRequest
* TransferRequest

---

### Responses

Use consistent API responses.

Success:

{
"success": true,
"message": "",
"data": {}
}

Error:

{
"success": false,
"message": ""
}

---

### Database Operations

Use Eloquent ORM.

Prefer relationships over manual queries when possible.

---

### Security

Never expose passwords.

Always hash passwords.

Always validate incoming requests.

Protected endpoints must use Sanctum middleware.

---

### Code Style

Prioritize:

* Readability
* Simplicity
* Maintainability

Avoid unnecessary abstractions.

Follow Laravel conventions whenever possible.

---

### Forbidden Practices

Do not:

* Place business logic in controllers
* Skip request validation
* Update balances without transactions
* Duplicate code

Always reuse services and helpers.
