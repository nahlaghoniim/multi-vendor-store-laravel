# Laravel Multi-Vendor E-Commerce Dashboard

## Project Overview
This project is a Laravel-based multi-vendor e-commerce dashboard designed to manage products, categories, orders, and users. It provides a robust backend for multi-store management, including authentication, authorization, and background job processing.

## Tech Stack
- **Backend Framework**: Laravel 10
- **Programming Language**: PHP 8.1
- **Frontend Tools**: Vite, TailwindCSS, Alpine.js
- **Database**: MySQL
- **Authentication**: Laravel Fortify, Laravel Sanctum
- **Queues**: Laravel Queues with Redis
- **APIs**: RESTful APIs with Sanctum for token-based authentication
- **Localization**: Mcamara Laravel Localization
- **Notifications**: Laravel Notifications

## Key Features
- Multi-store management with separate roles and permissions
- Product and category CRUD operations
- User and admin management
- Order processing and notifications
- Background jobs for importing products and cleaning expired orders
- API endpoints for product management and authentication
- Localization support for multiple languages

## Architecture Highlights
- **Multi-Store Separation**: Policies and gates ensure proper access control for different stores.
- **Service Layer**: Encapsulates business logic in dedicated service classes.
- **Event-Driven Design**: Events and listeners handle asynchronous tasks like notifications.
- **Middleware**: Custom middleware for user activity tracking and notification handling.

## Authentication & Authorization Flow
- **Authentication**: Laravel Fortify handles user authentication, including two-factor authentication.
- **Authorization**: Policies and gates are used to enforce permissions at the model level.
- **API Authentication**: Sanctum provides token-based authentication for API endpoints.

## Background Jobs & Queues
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