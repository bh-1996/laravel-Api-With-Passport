
### Author : MBK Bhandari ( https://www.linkedin.com/in/mbk-9852/ )

# Laravel-Api-With-Passport

Laravel API with Passport Authentication This repository contains a sample API built with Laravel, showcasing how to implement OAuth2 authentication using Laravel Passport. The project includes user registration, login, and secured endpoints accessible only with valid tokens.Also add event listen to user created and post created.

## Features

- **Authentication**: Secure authentication using Laravel Passport..
- **CRUD Operations**: Create, read, update, and delete posts.
- **Image Handling**: Upload and delete images associated with posts.
- **Validation**: Server-side validation for incoming requests.
- **Testing**: Includes API testing for endpoints.

## Prerequisites

- PHP 8.x
- Composer
- Laravel 10.x
- MySQL or any other supported database
- XAMPP or similar local development environment

## Installation

1. Clone the repository:
   ```bash
   git clone git@github.com:bh-1996/laravel-Api-With-Passport.git

2. Navigate to the project directory:
    ```bash
    cd laravel-Api-With-Passport

3. Install dependencies:
    ```bash
    composer install


4. Copy .env.example to .env and configure your environment variables:
    ```bash
    cp .env.example .env

5. Generate application key:
    ```bash
    php artisan key:generate

6. Run database migrations:
    ```bash
   php artisan migrate

7. Serve the application:
    ```bash
    php artisan passport:install

8. Serve the application:
    ```bash
    php artisan serve



## API Endpoints
- POST /api/register - Register a new user
- POST /api/login - Login and receive a token
- POST /api/logout - Logout the current user
- GET /api/posts - Retrieve all posts
- POST /api/posts - Create a new post
- GET /api/posts/{id} - Retrieve a specific post
- PUT/PATCH /api/posts/{id} - Update a specific post
- DELETE /api/posts/{id} - Delete a specific post

**User**
- POST /api/register - Register a new user
    ```bash
    curl -X POST http://localhost:8000/api/register -d "name=John Doe&email=john@example.com&password=password&password_confirmation=password"
- POST /api/login - Login and receive a token
    ```bash
    curl -X POST http://localhost:8000/api/login -d "email=john@example.com&password=password"

- POST /api/logout - Logout the current user
    ```bash
    curl -H "Authorization: Bearer {token}" -X POST http://localhost:8000/api/logout
**Posts**
- GET /api/posts - Retrieve all posts
    ```bash
    curl -H "Authorization: Bearer {token}" -X GET http://localhost:8000/api/posts

- POST /api/posts - Create a new post
    ```bash
    curl -H "Authorization: Bearer {token}" -X POST http://localhost:8000/api/posts -F "title=New Post" -F "description=Post description" -F "image=@path/to/image.jpg"
- GET /api/posts/{id} - Retrieve a specific post
    ```bash
    curl -H "Authorization: Bearer {token}" -X GET http://localhost:8000/api/posts/{id}

- PUT/PATCH /api/posts/{id} - Update a specific post
    ```bash
    curl -H "Authorization: Bearer {token}" -X PUT http://localhost:8000/api/posts/{id} -F "title=Updated Title" -F "description=Updated description" -F "image=@path/to/image.jpg"

- DELETE /api/posts/{id} - Delete a specific post
    ```bash
   curl -H "Authorization: Bearer {token}" -X DELETE http://localhost:8000/api/posts/{id}


**Testing**
- To run tests, use the following command:
    ```bash
    php artisan test

## Contributing
- Feel free to open issues or submit pull requests. Any contributions are welcome!

