# Inventory Management System API

A Laravel-based RESTful API for managing inventory items, user borrowing, and returns with notifications.

## Features

- **User Authentication**: Register, login, logout, profile management with Sanctum
- **Role-Based Access Control**: Admin and User roles with specific permissions
- **Category Management**: CRUD operations for item categories
- **Item Management**: CRUD operations with image upload functionality
- **Transaction System**: Borrow and return items with due date tracking
- **Notification System**: Automatic notifications for due dates and overdue items
- **Scheduler**: Daily checks for due dates and sending notifications

## Setup Instructions

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL or compatible database
- Laravel requirements

### Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/inventory-backend.git
   cd inventory-backend
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Set up environment:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventory
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations and seeders:
   ```
   php artisan migrate
   php artisan db:seed
   ```

6. Start the server:
   ```
   php artisan serve
   ```

7. Set up scheduler (for notifications):
   ```
   # Add to crontab for production
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## API Documentation

### Authentication Endpoints

- **POST /api/register**: Register a new user
  - Required: name, email, password, password_confirmation
  - Optional: profile_picture

- **POST /api/login**: Login and get token
  - Required: email, password

- **POST /api/logout**: Logout (requires authentication)

- **GET /api/user**: Get authenticated user details

- **PUT /api/user/profile**: Update user profile
  - Optional: name, email, profile_picture

- **PUT /api/user/password**: Change password
  - Required: current_password, password, password_confirmation

### Category Endpoints

- **GET /api/user/categories**: List all categories
- **GET /api/user/categories/{id}**: Get category details

- **POST /api/admin/categories**: Create category (admin only)
  - Required: name
  - Optional: description

- **PUT /api/admin/categories/{id}**: Update category (admin only)
  - Optional: name, description

- **DELETE /api/admin/categories/{id}**: Delete category (admin only)

### Item Endpoints

- **GET /api/user/items**: List all items
- **GET /api/user/items/{id}**: Get item details

- **POST /api/admin/items**: Create item (admin only)
  - Required: name, category_id, quantity
  - Optional: description, image, status

- **PUT /api/admin/items/{id}**: Update item (admin only)
  - Optional: name, description, category_id, quantity, image, status

- **DELETE /api/admin/items/{id}**: Delete item (admin only)

### Transaction Endpoints

- **GET /api/user/transactions**: List user transactions
- **POST /api/user/transactions/borrow**: Borrow an item
  - Required: item_id, due_date

- **PUT /api/user/transactions/{id}/return**: Return an item

- **GET /api/admin/transactions**: List all transactions (admin only)
- **PUT /api/admin/transactions/{id}/cancel**: Cancel a transaction (admin only)

### Notification Endpoints

- **GET /api/user/notifications**: List user notifications
- **GET /api/user/notifications/unread-count**: Get unread notification count
- **PUT /api/user/notifications/{id}/read**: Mark notification as read
- **PUT /api/user/notifications/mark-all-read**: Mark all notifications as read

## License

This project is licensed under the MIT License.

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
