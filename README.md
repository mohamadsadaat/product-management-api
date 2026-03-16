# Product Management API

A RESTful API built with Laravel 11 for managing products with full CRUD operations.

## Features

- **Product Management**: Complete CRUD operations for products
- **RESTful API**: Clean and standard REST endpoints
- **Data Validation**: Request validation for create and update operations
- **API Resources**: Structured JSON responses using Laravel API Resources
- **Database Migrations**: Well-structured database schema
- **Authentication Ready**: Laravel Sanctum configured for API authentication

## Product Schema

| Field | Type | Description |
|-------|------|-------------|
| id | BigInt | Primary Key |
| name | String | Product name |
| sku | String | Unique SKU identifier |
| description | Text | Product description (optional) |
| price | Decimal | Product price (10,2) |
| stock | Integer | Stock quantity (default: 0) |
| status | Enum | Product status (active/inactive) |
| created_at | Timestamp | Creation time |
| updated_at | Timestamp | Last update time |

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | Get all products |
| GET | `/api/products/{id}` | Get a specific product |
| POST | `/api/products` | Create a new product |
| PUT/PATCH | `/api/products/{id}` | Update a product |
| DELETE | `/api/products/{id}` | Delete a product |

## Installation

1. Clone the repository
```bash
git clone https://github.com/mohamadsadaat/product-management-api.git
cd product-management-api
```

2. Install dependencies
```bash
composer install
```

3. Copy environment file
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Configure your database in `.env` file

6. Run migrations
```bash
php artisan migrate
```

7. Start the development server
```bash
php artisan serve
```

## Usage Examples

### Get All Products
```bash
curl -X GET http://localhost:8000/api/products
```

### Create a Product
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Sample Product",
    "sku": "SP001",
    "description": "A sample product description",
    "price": 29.99,
    "stock": 100,
    "status": "active"
  }'
```

### Update a Product
```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Product Name",
    "price": 39.99
  }'
```

### Delete a Product
```bash
curl -X DELETE http://localhost:8000/api/products/1
```

## Technologies Used

- **Laravel 11** - PHP Framework
- **MySQL** - Database (configurable)
- **Laravel Sanctum** - API Authentication
- **Laravel API Resources** - Response Transformation

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── productController.php
│   ├── Requests/
│   │   ├── StoreProductRequest.php
│   │   └── UpdateProductRequest.php
│   └── Resources/
│       └── ProductResource.php
├── Models/
│   └── Product.php
database/
└── migrations/
    └── 2026_03_15_083341_create_products_table.php
routes/
└── api.php
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
