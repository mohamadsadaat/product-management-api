# Product Management API

A RESTful Product Management API built with Laravel for managing products with authentication, CRUD operations, filtering, sorting, search, and pagination.

## Features

- User registration and login with Laravel Sanctum
- Token-based API authentication
- Protected product endpoints
- Full CRUD operations for products
- Form Request validation
- API Resources for clean JSON responses
- Search products by name or description
- Filter products by status, stock, and price range
- Sort products by multiple fields
- Pagination support
- Database seeders and factories
- Eloquent Query Scopes for cleaner filtering logic

---

## Tech Stack

- Laravel
- PHP
- MySQL
- Laravel Sanctum
- Eloquent ORM
- REST API

---

## Authentication

This project uses **Laravel Sanctum** for API authentication.

### Auth Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/login` | Login and receive token |
| GET | `/api/me` | Get authenticated user |
| POST | `/api/logout` | Logout and revoke current token |

### Authentication Header

For protected routes, send:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json