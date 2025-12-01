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
- Vue 3 (Composition API)
- TailwindCSS
- Vite

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

## 🐳 Running With Docker

1. Copy environment file:
```bash
   cp .env.example .env
```
2. Build & start containers:
```bash
   docker-compose up --build -d
```
3. Generate application key:
```bash
   docker exec -it php-fpm php artisan key:generate
```
4. Run database migrations:
```bash
   docker exec -it php-fpm php artisan migrate
```
5. Import CSV data:
```bash
   docker exec -it php-fpm php artisan app:import-products
```
6. Build frontend assets:
```bash
   docker exec -it php-fpm npm install
   docker exec -it php-fpm npm run build
```

Application will be available at:

http://localhost:55000/catalog


### **🖥 Live Demo (Render)**

👉 https://laravel-product-catalog-api.onrender.com/catalog

(The API is also available via /api/products)


### **🌐 REST API**

GET /api/products — returns filtered products

Examples:

🔍 Full-text search
```bash
  GET /api/products?search=solar
```

🧩 Filter by manufacturer
```bash
    GET /api/products?manufacturer=SunVolt
```

💰 Price range
```bash
    GET /api/products?min_price=100&max_price=500
```

🔋 Batteries by capacity range
```bash
    GET /api/products?category=battery&capacity_min=1000&capacity_max=3000
```

☀ Panels by power output
```bash
    GET /api/products?category=panel&power_min=300&power_max=600
```


🔌 Connectors by type
```bash
    GET /api/products?category=connector&connector_type=MC4```
```

### **🎨 Frontend**

Interactive filtering UI is available at:

👉 http://localhost:55000/catalog

Supports:
- **Search=**
- **Category filters=**
- **Manufacturer filter=**
- **Price range=**
- **Dynamic attribute filters=**
- **Live updates=**

 
  No page reloads required.

### **📁 Project structure (short)**
```bash

app/
├── Http/Controllers/ProductController.php
└── Services/ProductFilterService.php
resources/
├── js/components/ProductList.vue
└── views/products.blade.php
database/
├── migrations/
└── seed CSV importer
docker/
└── php-fpm + render deployment
```
✔ Completed Requirements

✔ Filter by category, manufacturer and price range

✔ Search by name, manufacturer, description

✔ Filter category-specific attributes

✔ Simple frontend (Vue 3 + Tailwind)

✔ API + Docker deployment

✔ Live demo online

### **🧑‍💻 Author**

Illia — Full Stack Developer

poseva41@gmail.com

https://github.com/pithatak
