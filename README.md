# StoreX — Laravel Multi-Vendor E-Commerce Platform

A full-featured multi-vendor e-commerce platform built with Laravel 10. StoreX implements a marketplace-style architecture where multiple stores can list products, customers can browse and purchase items, and deliveries are tracked in real-time using maps and websockets.

## Project Overview

StoreX delivers a scalable backend and storefront for marketplace-style applications. The project emphasizes clean architecture, role-based authorization, API readiness, and real-time delivery tracking with live location updates powered by Leaflet and OpenStreetMap.

## Core Features

### Multi-Vendor Architecture
- Independent stores, each with their own products and categories
- `Store`, `Product`, `Category`, `Tag` models with scoped product queries
- Scoped queries to ensure store data isolation
- Models follow Laravel conventions and clear responsibilities

### Authentication & Authorization
- **Laravel Fortify** for user authentication (registration, login, password reset, 2FA)
- **Role-based authorization** with morphable roles (`HasRoles`, `Role`, `RoleAbility` models)
- Gates defined in `AuthServiceProvider` for fine-grained permissions
- **Laravel Sanctum** for API authentication with scoped token abilities

### Products & Orders
- Product CRUD with RESTful APIs
- Paginated product listings with filters
- Order workflow using `Order`, `OrderItem`, `OrderAddress`, and `Payment` models
- Transactional checkout logic

### Payments
- **Stripe PHP SDK** integrated
- Configuration via environment variables (`STRIPE_KEY`, `STRIPE_SECRET`)
- Ready for webhook expansion with signature verification

### Currency Conversion
- Centralized currency helper (`App\Helpers\Currency`)
- Cached exchange rates and session-based currency switching
- Configurable base currency

### Background Jobs & Scheduling
- **ImportProducts** job for asynchronous product imports using factories
- **DeleteExpiredOrders** scheduled cleanup (runs every 6 hours)
- Queue drivers supported: `sync`, `database`, `redis`

### Real-Time Delivery Tracking
- **Leaflet** with **OpenStreetMap** tiles for map rendering (no API keys required)
- **Pusher** and **Laravel Broadcasting** for live delivery location updates
- `DeliveryLocationUpdated` event broadcasts position changes
- Frontend updates delivery markers in real-time on order details page
- Private channel subscription for secure delivery tracking

## Tech Stack

### Backend
- PHP 8.1+
- Laravel 10
- MySQL
- Laravel Fortify (authentication)
- Laravel Sanctum (API tokens)
- Laravel Broadcasting (real-time events)

### Frontend
- Blade templates
- TailwindCSS
- Alpine.js
- Vite

### APIs & Real-time
- RESTful APIs
- Pusher (pusher-php-server + pusher-js)
- Laravel Echo

### Payments & Maps
- Stripe (stripe-php)
- Leaflet.js
- OpenStreetMap tiles

### Queues & Jobs
- Laravel Queues (sync/database/redis)
- Laravel Scheduler

## Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & npm
- MySQL (or supported database)

### Quick Start

```bash
# Clone repository
git clone https://github.com/nahlaghoniim/multi-vendor-store-laravel.git storex
cd storex

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate
php artisan storage:link

# Configure .env with your settings:
# - DB_* (database credentials)
# - STRIPE_KEY, STRIPE_SECRET
# - PUSHER_APP_ID, PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_CLUSTER
# - BROADCAST_DRIVER=pusher
# - QUEUE_CONNECTION=database (or redis for production)

# Run migrations & seeders
php artisan migrate --seed

# Build frontend assets
npm run dev

# Start development server
php artisan serve

# Optional: Run background services
php artisan queue:work --tries=3
php artisan schedule:work
```

### Production Notes
- Set `QUEUE_CONNECTION=redis` or `database` (avoid `sync` in production)
- Use a process manager like Supervisor to keep `queue:work` running
- Configure cron to run `php artisan schedule:run` every minute
- Replace example Pusher and Stripe keys with production credentials

## Usage

### Frontend
- Browse products and categories via storefront routes (`resources/views/front`)
- Add items to cart and complete checkout
- Track delivery in real-time on order details page (`resources/views/front/orders/show.blade.php`)

### API Usage (Sanctum)

#### Generate Access Token
```bash
curl -X POST https://your-app.test/api/auth/access-tokens \
  -d 'email=admin@example.test' \
  -d 'password=secret' \
  -d 'device_name=cli'
```

#### Create Product (Authorized)
```bash
curl -H "Authorization: Bearer <TOKEN>" \
  -X POST https://your-app.test/api/products \
  -d 'name=New Product' \
  -d 'price=99.95' \
  -d 'category_id=1'
```

### Delivery Tracking Flow

1. `Delivery` model is linked to `Order` via `order_id`
2. When delivery location updates, backend fires `DeliveryLocationUpdated` event
3. Event is broadcast via Pusher to channel `private-deliveries.{orderId}`
4. Frontend (Leaflet map) subscribes to the channel using Laravel Echo
5. Map marker position updates in real-time as delivery moves

**Important**: Add channel authorization in `routes/channels.php`:
```php
Broadcast::channel('deliveries.{orderId}', function ($user, $orderId) {
    return $user->orders()->where('id', $orderId)->exists();
});
```

## API Endpoints Overview

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/products` | List all products (paginated) | No |
| GET | `/api/products/{id}` | Get product details | No |
| POST | `/api/products` | Create new product | Yes (Sanctum + ability) |
| POST | `/api/auth/access-tokens` | Generate API token | No (credentials required) |
| DELETE | `/api/auth/access-tokens/{token}` | Revoke API token | Yes (Sanctum) |
| GET | `/api/deliveries/{delivery}` | Get delivery details | Yes |
| PUT | `/api/deliveries/{delivery}` | Update delivery location | Yes |

See `routes/api.php` and `app/Http/Controllers/Api` for full implementation.

## Background Jobs & Queues

### DeleteExpiredOrders
- Runs every 6 hours via Laravel Scheduler
- Removes pending orders older than 7 days
- Configured in `app/Console/Kernel.php`

### ImportProducts
- Queued job that creates products using factories
- Demonstrates batch processing and queue usage
- Useful for bulk imports

### Queue Configuration
```bash
# Development (synchronous)
QUEUE_CONNECTION=sync

# Production (asynchronous)
QUEUE_CONNECTION=database  # or redis

# Start queue worker
php artisan queue:work --tries=3 --timeout=90

# Run scheduler
php artisan schedule:work
```

## Architecture Highlights

- **Multi-store separation** enforced via policies and gates
- **Service and helper layers** for reusable business logic
- **Event-driven design** with broadcasting for real-time updates
- **Background processing** using queues and scheduler for scalability
- **API-first approach** with Sanctum for secure token-based authentication
- **Localization support** via Mcamara Laravel Localization package

## Important Files

| File | Purpose |
|------|---------|
| `app/Models/Store.php` | Store model and relationships |
| `app/Models/Product.php` | Product model with scopes |
| `app/Models/Delivery.php` | Delivery tracking model |
| `app/Helpers/Currency.php` | Currency conversion helper |
| `app/Events/DeliveryLocationUpdated.php` | Real-time delivery broadcast event |
| `app/Jobs/DeleteExpiredOrders.php` | Scheduled cleanup job |
| `app/Jobs/ImportProducts.php` | Queued product import job |
| `routes/api.php` | API route definitions |
| `routes/channels.php` | Broadcast channel authorizations |
| `resources/views/front/orders/show.blade.php` | Real-time delivery tracking page |

## Future Improvements

- Add Stripe webhook handling with signature verification (`STRIPE_WEBHOOK_SECRET`)
- Create admin UI for role and ability management with audit logs
- Add support for additional payment gateways (PayPal, local gateways)
- Implement multi-currency price handling at product/store level
- Add advanced analytics and reporting dashboards
- Expand test coverage for APIs, broadcasting, queues, and scheduled jobs
- Optimize database queries for large-scale deployments
- Enhance frontend with Vue.js or React for richer interactivity

## Credits & Attributions

- **Laravel** — https://laravel.com
- **Leaflet** — https://leafletjs.com (map library)
- **OpenStreetMap** — https://www.openstreetmap.org (map tiles)
- **Stripe** — https://stripe.com (payment processing)
- **Pusher** — https://pusher.com (real-time websockets)
- **TailwindCSS** — https://tailwindcss.com (styling)
- **Alpine.js** — https://alpinejs.dev (JavaScript framework)

## Contributing

This project is actively maintained and open to contributions. Feel free to:
- Submit issues for bugs or feature requests
- Create pull requests with improvements
- Suggest architectural enhancements
- Report security vulnerabilities privately

## License

This project is open-source software licensed under the MIT license.

---

© Project scaffold and implementation by the repository author.

