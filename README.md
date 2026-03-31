# SaaS POS System

A modern, multi-tenant SaaS Point of Sale (POS) system built with Laravel 12, Livewire 3, Tailwind CSS, and FilamentPHP v3.

## Features

- **Multi-Tenancy**: Each business has its own isolated data with subdomain support
- **Subscription Management**: 3-day free trial with automatic trial expiry
- **Role-Based Access**: Superadmin, Business Owner, Admin, and Cashier roles
- **Point of Sale**: Fast product search, shopping cart, multiple payment methods
- **Inventory Management**: Track stock, low stock alerts, inventory movements
- **Analytics Dashboard**: Real-time sales reports, top products, revenue metrics
- **Receipt Generation**: PDF receipt support for thermal printers

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire 3, Tailwind CSS, Alpine.js
- **Admin Panel**: FilamentPHP v3
- **Multi-Tenancy**: Stancl/Tenancy
- **Database**: MySQL
- **Authentication**: Laravel Sanctum

## Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer 2.x
- Node.js 18+ (for asset compilation)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd saas-pos
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_pos
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 5. Create Storage Link

```bash
php artisan storage:link
```

### 6. Run the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Default Admin Credentials

- **Email**: admin@saaspos.com
- **Password**: password

## Tenant Registration

Navigate to `/register` to create a new tenant account. You'll receive a 3-day free trial automatically.

## User Roles

| Role | Description |
|------|-------------|
| Superadmin | Global system administration |
| Owner | Full access to tenant business |
| Admin | Manages staff and inventory |
| Cashier | Processes sales only |

## API Endpoints

```
POST /api/v1/auth/token        - Generate API token
GET  /api/v1/products          - List products
GET  /api/v1/products/search   - Search products
POST /api/v1/orders            - Create order
GET  /api/v1/orders/{id}      - Get order details
```

## Multi-Domain Setup

Configure your web server to point subdomains to the application. Each tenant can have a custom subdomain:

```
business1.yourdomain.com
business2.yourdomain.com
```

## Payment Gateways

Configure payment gateways in `.env`:

```env
# Stripe
STRIPE_ENABLED=true
STRIPE_KEY=your_key
STRIPE_SECRET=your_secret

# Paystack
PAYSTACK_ENABLED=true
PAYSTACK_PUBLIC_KEY=your_key
PAYSTACK_SECRET_KEY=your_secret
```

## Testing

```bash
php artisan test
```

## License

This project is licensed under the MIT License.
