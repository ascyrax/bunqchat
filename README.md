# Chat Application

A full-stack chat application with a PHP Slim backend and a React frontend. The application allows users to register, log in, create groups, join groups, send messages, and view messages in groups. Authentication and authorization are handled using JWT tokens.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
  - [Backend Setup](#backend-setup)
  - [Frontend Setup](#frontend-setup)
- [Usage](#usage)
  - [Running the Application](#running-the-application)
  - [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Environment Variables](#environment-variables)
- [Notes](#notes)
- [License](#license)

---

## Features

- **User Authentication**: Register and log in with username and password.
- **JWT Authentication**: Secure endpoints using JWT tokens.
- **Group Management**: Create and join groups.
- **Messaging**: Send and receive messages within groups.
- **RESTful API**: Clean and well-defined API endpoints.
- **CORS Support**: Cross-origin requests enabled for frontend integration.
- **Unit Testing**: PHPUnit tests for backend endpoints.

## Tech Stack

- **Backend**:
  - PHP 7.x or 8.x
  - Slim Framework
  - SQLite (using PDO)
  - Firebase PHP-JWT
- **Frontend**:
  - React(JSX) with Vite
  - HTTP requests using fetch
  - react-router-dom for Navigation

---

## Prerequisites

- **PHP**: Version 7.2 or higher
- **Composer**: Dependency management for PHP
- **yarn**: For running the frontend
- **SQLite Extension**: Enabled in PHP

---

## Installation

### Backend Setup

1. **Clone the Repository**

   ```bash
   git clone https://github.com/yourusername/chat-app.git
   cd chat-app/backend
   ```

2. **Install Dependencies**

   ```bash
   composer install
   ```

3. **Create Environment Variables**

   Create a `.env` file in the `backend` directory:

   ```dotenv
   # backend/.env

   JWT_SECRET_KEY=your_secret_key
   ```

   Replace `your_secret_key` with a secure secret key for JWT.

4. **Database Setup**

   The application uses SQLite. The database file will be created automatically.

5. **Start the PHP Built-in Server**

   ```bash
   php -S localhost:8080 -t public
   ```

   The backend API will be accessible at `http://localhost:8080`.

### Frontend Setup

1. **Navigate to the Frontend Directory**

   ```bash
   cd ../frontend
   ```

2. **Install Dependencies**

   ```bash
   yarn install
   ```

3. **Create Environment Variables**

   Create a `.env` file in the `frontend` directory:

   ```dotenv
   # frontend/.env

   VITE_API_URL=http://localhost:8080
   ```

4. **Start the Development Server**

   ```bash
   yarn dev
   ```

   The frontend application will be accessible at `http://localhost:5173`.

---

## Usage

### Running the Application

- **Backend**: Ensure the backend server is running on `http://localhost:8080`.
- **Frontend**: Access the frontend at `http://localhost:5173`.

### API Endpoints

#### Authentication

- **POST `/register`**

  Register a new user.

  **Request Body:**

  ```json
  {
    "username": "testuser",
    "password": "testpassword"
  }
  ```

- **POST `/login`**

  Log in an existing user.

  **Request Body:**

  ```json
  {
    "username": "testuser",
    "password": "testpassword"
  }
  ```

  **Response:**

  ```json
  {
    "flag": "success",
    "message": "Login successful.",
    "token": "jwt_token_here"
  }
  ```

#### Groups

- **POST `/groups`**

  Create a new group.

  **Headers:**

  ```
  Authorization: Bearer jwt_token_here
  ```

  **Request Body:**

  ```json
  {
    "name": "Group Name"
  }
  ```

- **POST `/join`**

  Join a group by ID.

  **Headers:**

  ```
  Authorization: Bearer jwt_token_here
  ```

  **Request Body:**

  ```json
  {
    "groupName": "Group Name"
  }
  ```

- **GET `/messages/{groupName}`**

  Get messages from a group.

  **Headers:**

  ```
  Authorization: Bearer jwt_token_here
  ```

#### Messages

- **POST `/messages`**

  Send a message to a group.

  **Headers:**

  ```
  Authorization: Bearer jwt_token_here
  ```

  **Request Body:**

  ```json
  {
    "message": "Hello, World!",
    "groupName": "Group Name",
    "createdBy": "testuser",
    "createdAt": "09:38:50",
  }
  ```

---

## Testing

### Running Backend Tests

1. **Ensure Dependencies are Installed**

   ```bash
   composer install
   ```

2. **Run PHPUnit Tests**

   ```bash
   ./vendor/bin/phpunit
   ```

   The tests will execute and display the results in the terminal.

---

## Environment Variables

### Backend `.env`

```dotenv
JWT_SECRET_KEY=your_secret_key
```

- `JWT_SECRET_KEY`: Secret key used for signing JWT tokens.

### Frontend `.env`

```dotenv
VITE_API_URL=http://localhost:8080
```

- `VITE_API_URL`: URL of the backend API.

---

## Notes

- **CORS Configuration**: The backend includes a CORS middleware to handle cross-origin requests from the frontend. It allows requests from `http://localhost:5173`.

- **JWT Authentication**: The backend uses JWT tokens for authenticating requests. Tokens are issued upon successful login and must be included in the `Authorization` header for protected routes.

- **Middleware Order**: Middleware in Slim is added in reverse order of execution. Ensure that the CORS middleware is added before the routing middleware to handle OPTIONS requests properly.

- **Database**: The application uses SQLite for simplicity. The database schema includes tables for users, groups, messages, and group members.

- **Error Handling**: The backend returns consistent error responses with appropriate HTTP status codes and messages.

---

## License

This project is licensed under the MIT License.

---

## Contact

For any questions or issues, please contact [your_email@example.com](mailto:your_email@example.com).

---

**Enjoy using the Chat Application!**
