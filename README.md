# Multi-Vendor Store (StoreX)

A full-featured multi-vendor e-commerce platform built with Laravel 10. This repository implements a marketplace-style architecture where multiple stores can list products, customers can browse and purchase items, and deliveries are tracked in real-time using maps and websockets.

## Key Features

- Multi-vendor architecture: `Store`, `Product`, `Category`, `Tag` models with scoped product queries and store association.
- Authentication & Authorization:
  - Laravel Fortify for user registration, login, profile, 2FA, and password reset.
  - Role-based abilities via `HasRoles` (morphable roles), `Role` and `RoleAbility` models, and Gates defined in `AuthServiceProvider`.
  - API tokens via Laravel Sanctum with scoped abilities (`AccessTokensController`).
- Products API: `routes/api.php` exposes `products` resource with paginated listing and guarded create/update/delete actions.
- Checkout & Orders:
  StoreX — Laravel Multi-Vendor E-Commerce Platform

  StoreX is a multi-vendor e-commerce platform built with Laravel 10. It provides a marketplace architecture where independent stores can manage products and categories, customers can place orders, payments are processed securely, and delivery locations are tracked in real time.

  Project Overview

  StoreX delivers a scalable backend and storefront for marketplace-style applications. The project emphasizes clean architecture, role-based authorization, API readiness, and real-time delivery tracking.

  Core Features

  Multi-Vendor Architecture

  - Independent stores, each with their own products and categories.
  - Scoped queries to ensure store data isolation.
  - Models follow Laravel conventions and clear responsibilities.

  Authentication & Authorization

  - Laravel Fortify for user authentication.
  - Registration, login, password reset, and two-factor authentication (2FA).
  - Role-based authorization with morphable roles (`Role`, `RoleAbility`).
  - Gates defined in `AuthServiceProvider`.
  - API authentication via Laravel Sanctum with scoped token abilities.

  Products, Checkout & Orders

  - Product CRUD with RESTful APIs.
  - Order workflow using `Order`, `OrderItem`, `OrderAddress`, and `Payment` models.
  - Transactional checkout logic and Stripe integration.

  Payments

  - Stripe PHP SDK integrated.
  - Configuration via environment variables.
  - Ready for webhook expansion.

  Currency Conversion

  - Centralized currency helper (`App/Helpers/Currency`).
  - Cached exchange rates and session-based currency switching.

  Background Jobs & Scheduling

  - `ImportProducts` job for asynchronous product imports.
  - `DeleteExpiredOrders` scheduled cleanup (every 6 hours).
  - Queue drivers supported: `sync`, `database`, `redis`.

  Real-Time Delivery Tracking

  - Leaflet with OpenStreetMap tiles for map rendering (no API keys required).
  - Pusher and Laravel Broadcasting for live updates.
  - Delivery marker updates on the order details page.

  Architecture Highlights

  - Multi-store separation enforced via policies and gates.
  - Service and helper layers for reusable business logic.
  - Event-driven design with broadcasting for delivery updates.
  - Background processing using queues and scheduler for scalability.

  Tech Stack

  Backend

  - PHP 8.1+
  - Laravel 10
  - MySQL

  Frontend

  - Blade templates
  - TailwindCSS
  - Alpine.js
  - Vite

  APIs & Realtime

  - Laravel Sanctum
  - Laravel Broadcasting
  - Pusher

  Payments & Maps

  - Stripe (`stripe-php`)
  - Leaflet + OpenStreetMap

  Installation & Setup

  Prerequisites

  - PHP 8.1+
  - Composer
  - Node.js & npm
  - MySQL

  Quick Start

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

  # Configure .env
  # DB_*
  # STRIPE_KEY, STRIPE_SECRET
  # PUSHER_*
  # QUEUE_CONNECTION=database|redis

  # Run migrations & seeders
  php artisan migrate --seed

  # Build frontend assets
  npm run dev

  # Optional: background services
  php artisan queue:work
  php artisan schedule:work
  ```

  Usage

  Frontend

  - Browse products and categories.
  - Place orders through checkout.
  - Track delivery in real time using `resources/views/front/orders/show.blade.php`.

  API Usage (Sanctum)

  - Generate token: `POST /api/auth/access-tokens`.
  - Create product (authorized): `POST /api/products` with `Authorization: Bearer <TOKEN>`.

  Delivery Tracking Flow

  - Delivery updates location in backend.
  - `DeliveryLocationUpdated` event is fired and broadcast via Pusher.
  - Frontend listens on `private-deliveries.{orderId}` and updates the Leaflet marker in real time.

  API Endpoints Overview

  - `GET /api/products`
  - `GET /api/products/{id}`
  - `POST /api/products` (requires ability)
  - `POST /api/auth/access-tokens`
  - `GET|PUT /api/deliveries/{delivery}`

  Important Files

  - `app/Models/Store.php`
  - `app/Models/Product.php`
  - `app/Helpers/Currency.php`
  - `app/Events/DeliveryLocationUpdated.php`
  - `routes/api.php`
  - `resources/views/front/orders/show.blade.php`

  Future Improvements

  - Stripe webhook handling and signature verification.
  - Admin UI for role and ability management.
  - Additional payment gateways.
  - Advanced analytics and reporting.
  - Expanded test coverage for APIs, queues, and broadcasting.

  Credits & Attributions

  - Laravel — https://laravel.com
  - Leaflet — https://leafletjs.com
  - OpenStreetMap — https://www.openstreetmap.org
  - Stripe — https://stripe.com
  - Pusher — https://pusher.com

- **Job Example**: `ImportProducts` job creates products in the background using factories.
- **Queue Driver**: Configured to use Redis for queue management.
- **Scheduler**: The `DeleteExpiredOrders` job runs every six hours to clean up expired orders.

## API & Sanctum Usage
- **Endpoints**:
  - `GET /api/products`: Fetch all products.
  - `POST /api/auth/access-tokens`: Generate an access token.
  - `DELETE /api/auth/access-tokens/{token}`: Revoke an access token.
- **Middleware**: Sanctum middleware secures API routes.

## Installation & Setup
1. Clone the repository:
   ```bash
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```bash
   cd store
   ```
3. Install PHP dependencies:
   ```bash
   composer install
   ```
4. Install JavaScript dependencies:
   ```bash
   npm install
   ```
5. Copy the `.env` file:
   ```bash
   cp .env.example .env
   ```
6. Generate the application key:
   ```bash
   php artisan key:generate
   ```
7. Configure the `.env` file with your database and other environment settings.
8. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
9. Start the development server:
   ```bash
   php artisan serve
   ```
10. Build frontend assets:
    ```bash
    npm run dev
    ```

## Environment Variables
Key environment variables include:
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `QUEUE_CONNECTION` (e.g., `sync`, `redis`)
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`

## Queue & Scheduler Setup Commands
- Start the queue worker:
  ```bash
  php artisan queue:work
  ```
- Run the scheduler:
  ```bash
  php artisan schedule:work
  ```

## Notes for Future Improvements
- Implement advanced reporting and analytics for store performance.
- Add support for payment gateways like PayPal and Stripe.
- Enhance the frontend with a modern JavaScript framework like Vue.js or React.
- Optimize database queries for large-scale data handling.
- Improve test coverage for critical features.

---

This project is actively maintained and open to contributions. Feel free to submit issues or pull requests to improve the functionality.