# Laravel E-Commerce Store

## Project Description
This is a fully functional e-commerce platform built using the Laravel framework. It provides a seamless shopping experience for users, allowing them to browse products, add items to their cart, and complete purchases. The platform is designed to be scalable, secure, and easy to maintain.

## Features
- User authentication and authorization (registration, login, password reset).
- Product management (CRUD operations for products, categories, and tags).
- Shopping cart functionality with real-time updates.
- Order management, including order creation, tracking, and notifications.
- Admin dashboard for managing the store.
- Multi-language support (English, Arabic, Spanish).
- Responsive design for mobile and desktop users.
- Integration with payment gateways.
- Notifications for order updates.

## Tech Stack
- **Backend Framework**: Laravel (PHP)
- **Frontend**: Blade templates, Tailwind CSS
- **Database**: MySQL
- **Task Scheduling**: Laravel Scheduler
- **Authentication**: Laravel Fortify
- **Other Tools**: Composer, NPM, Vite

## Installation
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
5. Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```
6. Generate the application key:
    ```bash
    php artisan key:generate
    ```
7. Configure the `.env` file with your database credentials.
8. Run database migrations:
    ```bash
    php artisan migrate
    ```
9. Seed the database (optional):
    ```bash
    php artisan db:seed
    ```
10. Build frontend assets:
    ```bash
    npm run build
    ```
11. Start the development server:
    ```bash
    php artisan serve
    ```

## Usage
1. Access the application in your browser:
    ```
    http://localhost:8000
    ```
2. Log in or register to start using the platform.
3. Explore the admin dashboard (if you have admin privileges) to manage products, categories, and orders.

## License
This project is open-source and available under the [MIT License](LICENSE).