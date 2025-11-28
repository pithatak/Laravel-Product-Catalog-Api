# Product Catalog API — Laravel Test Assignment

This application implements a product catalog API for photovoltaic products  
(solar panels, batteries and connectors).  
The goal of the task was to:

- import product data from CSV files
- store normalized product and attribute data in a relational database
- provide filtering and search functionality
- expose a clean JSON REST API
- deploy the project in a reproducible Docker environment

---

## 🚀 Stack

- PHP 8.2 (FPM)
- Laravel 10
- MySQL 8
- Nginx (Alpine)
- Docker & Docker Compose

---

## 📦 Features Implemented

### **1. CSV Import**
A custom Laravel command imports three CSV files into the database:
- **storage/app/data/batteries.csv**
- **storage/app/data/solar_panels.csv**
- **storage/app/data/connectors.csv**

Command:

```bash
  php artisan app:import-products
```
For each file:
- **A product record is created**
- **A category-specific attribute is inserted (capacity, power_output, connector_type)**
- **Data is normalized using two tables: products and product_attributes**

### **2. Database Schema**
   products

| Column       | Type    |
| ------------ | ------- |
| id           | bigint  |
| name         | string  |
| manufacturer | string  |
| price        | decimal |
| category     | string  |
| description  | text    |
| timestamps   | —       |

product_attributes

| Column     | Type      |
| ---------- | --------- |
| id         | bigint    |
| product_id | bigint FK |
| key        | string    |
| value      | string    |
| timestamps | —         |

This structure supports any number of dynamic attributes per product.

## 🔍 Filtering and Search

The API supports search by:
- **category=**
- **manufacturer==**
- **min_price=, max_price===**
- **min_price=, max_price===**
Full-text-like search:
- **search= on name, manufacturer, description**
Category-specific filters:
- **battery: capacity_min, capacity_max**
- **panel: power_min, power_max**
- **connector: connector_type=**

Implemented in App\Services\ProductFilterService.

## 🌐 REST API Endpoints
GET /api/products

Returns filtered list of products.
Examples:
```bash

    /api/products
    /api/products?category=battery
    /api/products?search=solar
    /api/products?min_price=100&max_price=500
    /api/products?category=panel&power_min=300
    /api/products?category=connector&connector_type=MC4
```
Returns product with all attributes.
```bash
    /api/products/{id}
```
## 🐳 Running With Docker

1. Copy environment file
   cp .env.example .env

2. Build & start containers
   docker-compose up --build -d

3. Generate application key
   docker exec -it php-fpm php artisan key:generate

4. Run database migrations
   docker exec -it php-fpm php artisan migrate

5. Import CSV data
   docker exec -it php-fpm php artisan app:import-products



Application will be available at:

http://localhost:55000

🧪 Testing the API
All products:
GET /api/products

Batteries with capacity between 1000 and 3000:
GET /api/products?category=battery&capacity_min=1000&capacity_max=3000

Search:
GET /api/products?search=panel
