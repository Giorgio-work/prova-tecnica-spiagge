# Prova Tecnica Spiagge - Task Management Application

A modern task management application built with Laravel 12, Vue.js 3, and Inertia.js. This project demonstrates a
full-stack implementation with authentication, CRUD operations, API endpoints, and comprehensive testing.

## 🚀 Technologies Used

### Backend

- **Laravel 12** - PHP framework with modern features
- **PHP 8.4** - Latest PHP version with performance improvements
- **MySQL 8.0** - Relational database
- **Inertia.js v2** - Modern monolith approach bridging backend and frontend
- **Laravel Sanctum** - API authentication

### Frontend

- **Vue.js 3.5** - Progressive JavaScript framework
- **TypeScript** - Type-safe JavaScript
- **TailwindCSS v4** - Utility-first CSS framework
- **Vite** - Fast build tool and development server
- **Reka UI** - Vue component library
- **Lucide Vue** - Beautiful icons

### Development & DevOps

- **Docker & Docker Compose** - Containerized development environment
- **Nginx** - Web server
- **Redis** - Caching and session storage
- **PHPUnit** - Testing framework
- **ESLint & Prettier** - Code quality and formatting
- **GitHub Actions** - CI/CD pipeline

## 📋 Features

- **User Authentication** - Registration, login, logout with session management
- **Task Management** - Complete CRUD operations for tasks
- **Task Status Management** - Change task status (pending, in_progress, completed)
- **Task Filtering** - Filter tasks by status, priority, and due date
- **API Endpoints** - RESTful API for all operations
- **Responsive Design** - Mobile-first responsive interface
- **Real-time Validation** - Client and server-side validation
- **Service Layer Architecture** - Clean separation of concerns
- **Comprehensive Testing** - Feature and unit tests

## 🛠️ Installation & Setup

### Prerequisites

- Docker Engine 28.1+
- Docker Compose
- ~~Git~~

### 1. Install Docker + Docker Compose

   ```bash
    # Ubuntu/Linux Mint:
    sudo apt update
    sudo apt install docker.io docker-compose
   ```

### Add user to a docker group

   ```bash
    sudo usermod -aG docker $USER
    # Logout e login per applicare i permessi
   ```

### 2. Verify installation

   ```bash
    docker --version
    docker-compose --version
   ```

### Quick Start with Docker (Recommended)

1. **Run the setup script**

```bash
 ./first-time-setup.sh
```

2. **Access the application**
    - Application: http://localhost:8000
    - MailHog: http://localhost:8025

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
 ./run-tests.sh
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/         # Web and API controllers
│   ├──  Middleware/         # Custom middleware
│   └──  Requests/           # Form request validation
├── Models/                  # Eloquent models
├── Policies/                # Authorization policies
├── Providers/               # Service providers
└── Services/                # Business logic services

resources/
├── css/
├── js/
└── views/

tests/
├── Feature/               # Feature tests
└── Unit/                  # Unit tests
```

### Docker Services

- **app**: Laravel application (PHP 8.4-FPM)
- **nginx**: Web server
- **mysql**: Database server
- **redis**: Cache and session storage
- **mailhog**: Email testing
- **phpmyadmin**: Database administration

## 🏗️ Architecture & Design Patterns

### Service Layer Pattern

Business logic is extracted into service classes (`TaskService`) for better separation of concerns and reusability
between web and API controllers.

### Repository Pattern

Eloquent models serve as repositories with custom query scopes for complex filtering.

### Policy-Based Authorization

Laravel policies handle authorization logic for task operations.

### API Design

- RESTful endpoints following Laravel conventions
- Consistent JSON responses
- Proper HTTP status codes
- Request validation using Form Requests

### Frontend Architecture

- Component-based Vue.js architecture
- TypeScript for type safety
- Composables for reusable logic
- Inertia.js for seamless SPA experience

### Design Choices

For a small project, the monolithic architecture with Vue + Laravel + Inertia.js offers significant advantages over two
separate applications. This combination creates what is called a "modern monolith" that maintains the benefits of a
modern SPA by eliminating the complexity of separate architectures, the main advantages are:

#### Reduction of Complexity:

Inertia.js eliminates the need to manage separate frontend and backend base codes. You won’t have to create and maintain
REST APIs, manage API documentation, or synchronize data models between frontend and backend. This approach can reduce
more than half the frontend code compared to a separate API architecture.

#### Speed of Development:

For small projects like this, the monolithic architecture is more practical and quick to implement, drastically reducing
the development time required.

#### Unified routing and navigation:

The routing is handled entirely by Laravel server side, while Inertia intercepts navigation to create a SPA experience
without page reloads. This eliminates the need to implement routing in two separate locations.

Monolithic architecture is therefore particularly suitable when:

- The project is small and does not anticipate significant growth

- You have small development teams

- You need a fast deployment without an immediate scalability

## 📚 API Documentation

### Authentication

All API endpoints require authentication via Laravel Sanctum.

### Endpoints

#### Tasks:

- `GET /api/tasks` - List tasks with filtering
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get specific task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task
- `PATCH /api/tasks/{id}/change-status` - Change task status

#### Auth:

- `POST /api/register` - Register a new user
- `POST /api/login` - Login
- `POST /api/logout` - Logout
