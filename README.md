# Product Management API

A comprehensive RESTful Product Management API built with Laravel 12 for managing products with advanced features including authentication, CRUD operations, filtering, sorting, search, pagination, categories, and soft deletes.

## 🌐 Live Demo

**API Base URL**: [https://product-management-api-main-xoagtm.free.laravel.cloud/](https://product-management-api-main-xoagtm.free.laravel.cloud/)

**API Documentation**: [Swagger UI](https://product-management-api-main-xoagtm.free.laravel.cloud/api/documentation)

The API is now live and ready for testing! Use the endpoints below with the base URL above.

## 🚀 Features

### Authentication & Security
- **User Registration & Login** with Laravel Sanctum
- **Token-based API Authentication** with Bearer tokens
- **Protected Routes** for all product and category operations
- **Authorization Policies** for CRUD operations
- **User-specific Data** isolation

### Product Management
- **Full CRUD Operations** (Create, Read, Update, Delete)
- **Soft Deletes** with trash, restore, and force delete functionality
- **Image Upload** with automatic file management
- **Product Categories** with relationship management
- **Stock Management** with in-stock filtering
- **Status Management** (active/inactive products)
- **SKU Generation** and unique identification

### Advanced Querying & Filtering
- **Search** products by name or description
- **Filter** by status, stock quantity, price range, and category
- **Sort** by multiple fields (id, name, price, stock, created_at)
- **Pagination** with customizable per-page limits (1-50)
- **Query Scopes** for clean and reusable filtering logic

### Data Management
- **API Resources** for consistent JSON responses
- **Form Request Validation** for data integrity
- **Database Seeders** and Factories for testing
- **Eloquent Relationships** (Product-Category, Product-User)
- **Database Migrations** for structured schema

## 🛠 Tech Stack

- **Backend**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **ORM**: Eloquent
- **API Style**: RESTful
- **Validation**: Laravel Form Requests
- **File Storage**: Laravel Storage (Public Disk)
- **Testing**: PHPUnit

## 📋 API Endpoints

### Authentication Endpoints

| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| POST | `/api/register` | Register a new user | No |
| POST | `/api/login` | Login and receive token | No |
| GET | `/api/me` | Get authenticated user info | Yes |
| POST | `/api/logout` | Logout and revoke token | Yes |

### Product Endpoints

| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| GET | `/api/products` | List products with filters | Yes |
| POST | `/api/products` | Create new product | Yes |
| GET | `/api/products/{id}` | Get single product | Yes |
| POST | `/api/products/{id}/update` | Update product (supports multipart) | Yes |
| DELETE | `/api/products/{id}` | Soft delete product | Yes |
| GET | `/api/products/trash` | List trashed products | Yes |
| POST | `/api/products/{id}/restore` | Restore trashed product | Yes |
| DELETE | `/api/products/{id}/force` | Permanently delete product | Yes |

### Category Endpoints

| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| GET | `/api/categories` | List all categories | Yes |
| POST | `/api/categories` | Create new category | Yes |
| GET | `/api/categories/{id}` | Get single category | Yes |
| PUT | `/api/categories/{id}` | Update category | Yes |
| DELETE | `/api/categories/{id}` | Delete category | Yes |

## 🔧 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/MariaDB
- Node.js & NPM (for assets)

### Quick Setup

1. **Clone the repository**
```bash
git clone https://github.com/mohamadsadaat/product-management-api.git
cd product-management-api
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
```bash
# Edit .env file with your database credentials
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Build assets**
```bash
npm run build
```

7. **Start the development server**
```bash
php artisan serve
```

### Using Composer Scripts

The project includes convenient composer scripts:

```bash
# Complete setup (install, migrate, build)
composer run setup

# Development with hot reload
composer run dev

# Run tests
composer run test
```

## 📖 Usage Examples

### Authentication

#### Register
```bash
curl -X POST https://product-management-api-main-xoagtm.free.laravel.cloud/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Login
```bash
curl -X POST https://product-management-api-main-xoagtm.free.laravel.cloud/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Product Operations

#### Create Product
```bash
curl -X POST https://product-management-api-main-xoagtm.free.laravel.cloud/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "name=Product Name" \
  -F "sku=PRD-001" \
  -F "description=Product description" \
  -F "price=29.99" \
  -F "stock=100" \
  -F "status=active" \
  -F "category_id=1" \
  -F "image=@path/to/image.jpg"
```

#### List Products with Filters
```bash
curl -X GET "https://product-management-api-main-xoagtm.free.laravel.cloud/api/products?search=laptop&status=active&min_price=500&max_price=2000&sort_by=price&sort_direction=asc&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Update Product
```bash
curl -X POST https://product-management-api-main-xoagtm.free.laravel.cloud/api/products/1/update \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "name=Updated Product Name" \
  -F "price=39.99"
```

## 🔍 Query Parameters

### Products Index

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `search` | string | Search in name and description | `laptop` |
| `status` | string | Filter by status | `active` |
| `min_stock` | integer | Minimum stock quantity | `10` |
| `min_price` | decimal | Minimum price | `50.00` |
| `max_price` | decimal | Maximum price | `500.00` |
| `category_id` | integer | Filter by category | `1` |
| `in_stock` | boolean | Only products in stock | `true` |
| `only_active` | boolean | Only active products | `true` |
| `sort_by` | string | Sort field | `price` |
| `sort_direction` | string | Sort direction | `asc`/`desc` |
| `per_page` | integer | Items per page (1-50) | `20` |

## 📁 Project Structure

```
product-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   └── CategoryController.php
│   │   ├── Requests/
│   │   │   ├── StoreProductRequest.php
│   │   │   └── UpdateProductRequest.php
│   │   └── Resources/
│   │       ├── ProductResource.php
│   │       └── CategoryResource.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   └── User.php
│   └── Policies/
│       └── ProductPolicy.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   └── api.php
├── storage/
│   └── app/public/products/
└── tests/
```

## 🧪 Testing

### Local Testing
Run the test suite locally:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter ProductTest

# Generate coverage report
php artisan test --coverage
```

### Live API Testing
Test the live API using the base URL: `https://product-management-api-main-xoagtm.free.laravel.cloud/`

**Interactive Documentation**: Visit [Swagger UI](https://product-management-api-main-xoagtm.free.laravel.cloud/api/documentation) for interactive API testing with built-in authentication.

You can use tools like:
- **Swagger UI** - Interactive API documentation with testing capabilities
- **Postman** - Import the API endpoints
- **Insomnia** - REST client for API testing
- **curl** - Command line examples provided above

## 🔒 Security Features

- **Token-based Authentication** with Laravel Sanctum
- **Request Validation** using Form Requests
- **Authorization Policies** for user-specific data access
- **CSRF Protection** for web routes
- **SQL Injection Prevention** via Eloquent ORM
- **File Upload Security** with validated file types

## 📝 Data Models

### Product
- `name` - Product name
- `sku` - Stock Keeping Unit
- `description` - Product description
- `price` - Decimal price
- `stock` - Integer quantity
- `status` - active/inactive
- `image` - Product image path
- `user_id` - Foreign key to User
- `category_id` - Foreign key to Category

### Category
- `name` - Category name
- `description` - Category description

### User
- Standard Laravel User model with Sanctum tokens

## 🔄 Soft Deletes

Products use Laravel's Soft Deletes feature:
- **DELETE** moves product to trash
- **GET /trash** shows trashed products
- **POST /restore** restores trashed product
- **DELETE /force** permanently deletes

## 📊 Response Format

All API responses follow a consistent format:

```json
{
  "status": true,
  "message": "Operation completed successfully",
  "data": { ... },
  "pagination": { ... } // For paginated responses
}
```

## 🚀 Performance Features

- **Database Query Optimization** with eager loading
- **Pagination** for large datasets
- **Query Scopes** for efficient filtering
- **Image Storage** in separate disk for better performance
- **Caching Ready** structure for future implementation

## 🛠 Development Features

- **API Resources** for consistent responses
- **Form Request Validation** for clean controllers
- **Eloquent Query Scopes** for reusable queries
- **Database Seeders** for development data
- **Composer Scripts** for common tasks
- **PHPUnit Tests** for quality assurance

## 📝 Future Enhancements

- [ ] Rate Limiting
- [ ] API Documentation (Swagger/OpenAPI)
- [ ] Caching Implementation
- [ ] Queue System for image processing
- [ ] Advanced search with Elasticsearch
- [ ] Multi-language support
- [ ] API versioning
- [ ] WebSocket for real-time updates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

For support and questions, please contact:
- **Live API**: [https://product-management-api-main-xoagtm.free.laravel.cloud/](https://product-management-api-main-xoagtm.free.laravel.cloud/)
- Email: mohamadsadaat@example.com
- GitHub: [@mohamadsadaat](https://github.com/mohamadsadaat)

---

**🚀 Live API Ready for Production**
**Built with ❤️ using Laravel 12**
