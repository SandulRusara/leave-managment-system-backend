# Leave Management System - Backend

A comprehensive Leave Management System built with Laravel 10, featuring REST API endpoints for both Employee and Admin users with secure authentication using Laravel Passport.

## 🚀 Features

### User Roles
- **Employee**: Register, login, apply for leave, view leave status
- **Admin**: Login, view all leave requests, approve/reject leaves, view user statistics

### API Endpoints
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `GET /api/user` - Get authenticated user details
- `POST /api/logout` - User logout
- `GET /api/leaves` - Get leaves (filtered by user role)
- `POST /api/leaves` - Create new leave request
- `GET /api/leaves/{id}` - Get specific leave details
- `PUT /api/leaves/{id}` - Update leave status (Admin only)
- `DELETE /api/leaves/{id}` - Delete leave request
- `GET /api/leaves/statistics/overview` - Get leave statistics (Admin only)
- `GET /api/users` - Get all users (Admin only)
- `GET /api/users/{id}` - Get user details with leave statistics

## 🛠️ Tech Stack

- **Backend**: Laravel 10+
- **Database**: MySQL
- **Authentication**: Laravel Passport (OAuth2)
- **Validation**: Laravel Form Requests
- **Architecture**: RESTful API with role-based access control

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or 8.0+
- Node.js & NPM (for frontend assets)

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone <repository-url>
cd leave-management-backend
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
cp .env.example .env
```

Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leave_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE leave_management;
exit

# Run migrations
php artisan migrate

# Install Passport
php artisan passport:install

# Seed database with sample data
php artisan db:seed
```

### 6. Start Development Server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 👥 Default Credentials

### Admin Account
- **Email**: admin@example.com
- **Password**: password

### Employee Account
- **Email**: employee1@example.com
- **Password**: password

Additional test accounts:
- jane.smith@example.com (Employee)
- mike.johnson@example.com (Employee)
- sarah.wilson@example.com (Employee)

## 📖 API Documentation

### Authentication
All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Request/Response Examples

#### 1. User Registration
```bash
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "employee_id": "EMP005",
    "department": "IT",
    "joining_date": "2024-01-15"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 6,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "employee",
            "department": "IT",
            "employee_id": "EMP005",
            "joining_date": "2024-01-15"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJS..."
    }
}
```

#### 2. User Login
```bash
POST /api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "System Administrator",
            "email": "admin@example.com",
            "role": "admin",
            "department": "IT Administration",
            "employee_id": "ADMIN001"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJS..."
    }
}
```

#### 3. Create Leave Request
```bash
POST /api/leaves
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "leave_type": "annual",
    "start_date": "2024-02-15",
    "end_date": "2024-02-17",
    "reason": "Family vacation"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Leave request submitted successfully",
    "data": {
        "leave": {
            "id": 10,
            "user_id": 2,
            "leave_type": "annual",
            "start_date": "2024-02-15",
            "end_date": "2024-02-17",
            "total_days": 3,
            "reason": "Family vacation",
            "status": "pending",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "user": {
                "id": 2,
                "name": "John Doe",
                "email": "employee1@example.com"
            }
        }
    }
}
```

#### 4. Update Leave Status (Admin Only)
```bash
PUT /api/leaves/10
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
    "status": "approved",
    "admin_comments": "Approved for family vacation"
}
```

#### 5. Get Leave Statistics (Admin Only)
```bash
GET /api/leaves/statistics/overview
Authorization: Bearer ADMIN_TOKEN
```

**Response:**
```json
{
    "success": true,
    "data": {
        "overview": {
            "total_leaves": 25,
            "pending_leaves": 8,
            "approved_leaves": 12,
            "rejected_leaves": 5,
            "total_employees": 4
        },
        "monthly_leaves": [2, 3, 5, 4, 6, 3, 2, 0, 0, 0, 0, 0],
        "leave_type_stats": {
            "annual": 10,
            "sick": 8,
            "personal": 4,
            "emergency": 3
        }
    }
}
```

## 🔒 Security Features

- **JWT Authentication** with Laravel Passport
- **Role-based Access Control** (Admin/Employee)
- **Form Request Validation** with custom error messages
- **CORS Configuration** for cross-origin requests
- **Rate Limiting** on API endpoints
- **Password Hashing** with bcrypt
- **Input Sanitization** and validation

## 🏗️ Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── LeaveController.php
│   │   └── UserController.php
│   ├── Requests/
│   │   ├── LoginRequest.php
│   │   ├── RegisterRequest.php
│   │   ├── LeaveRequest.php
│   │   └── UpdateLeaveStatusRequest.php
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── EmployeeMiddleware.php
├── Models/
│   ├── User.php
│   └── Leave.php
└── Providers/
    └── AuthServiceProvider.php

database/
├── migrations/
│   ├── create_users_table.php
│   └── create_leaves_table.php
└── seeders/
    ├── UserSeeder.php
    └── LeaveSeeder.php

routes/
└── api.php
```

## 📊 Database Schema

### Users Table
- id, name, email, password
- role (admin/employee)
- department, employee_id, joining_date
- timestamps

### Leaves Table
- id, user_id (foreign key)
- leave_type, start_date, end_date, total_days
- reason, status, admin_comments
- approved_by (foreign key), approved_at
- timestamps

## 🧪 Testing

### Test API Endpoints
You can test the API using tools like Postman, Insomnia, or curl:

```bash
# Test login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Test protected endpoint
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔧 Configuration

### Passport Configuration
The system uses Laravel Passport with the following token expiration settings:
- Access tokens: 15 days
- Refresh tokens: 30 days
- Personal access tokens: 6 months

### CORS Configuration
CORS is configured to allow cross-origin requests from your frontend application.

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure your production database
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Set up proper file permissions
7. Configure your web server (Apache/Nginx)

### Environment Variables
Key environment variables for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [API Documentation](#api-documentation)
2. Review the error messages in the response
3. Check Laravel logs in `storage/logs/laravel.log`
4. Verify your database connection and migrations

## 🔄 API Response Format

All API responses follow a consistent format:

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error message"
}
```

### Validation Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field_name": ["Error message for this field"]
    }
}
```
