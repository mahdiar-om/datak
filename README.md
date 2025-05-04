# Project Overview


## Setup & Local Run Instructions

### Prerequisites
- **Docker and Docker Compose**: Ensure Docker and Docker Compose are installed. [Install Docker](https://docs.docker.com/get-docker/) if needed.
- **Git**

### Steps to Run Locally
1. **Clone the Repository**
   ```bash
   git clone https://github.com/mahdiar-om/datak.git
   cd datak
   ```

2. **Create Environment File**
   ```bash
   cp .env.example laravel/.env
   ```
   * Open `.env` and add the Elasticsearch hostname:
   ```env
   ELASTICSEARCH_HOST=elastic
   ```
   * * You can alternatively run this command:
    ```bash
    echo "ELASTICSEARCH_HOST=elastic" >> laravel/.env
    ```
   
   
3. **Generate Application Key**
   ```bash
   docker compose run --rm app php artisan key:generate
   ```
   This adds a unique `APP_KEY` to your `.env` file.

4. **Start Docker Containers**
   In the `datak` directory, run:
   ```bash
   docker compose up -d
   ```
   This starts two services in detached mode:
   - **Laravel Application**: Uses the `bitnami/laravel` image, mounts the `./laravel` directory to `/app`, installs dependencies with `composer install`, and runs `php artisan serve --host 0.0.0.0 --port 8000`. Exposed on port 8000.
   - **Elasticsearch**: Uses `docker.elastic.co/elasticsearch/elasticsearch:8.12.0`, configured as a single node with security disabled. Persists data in the `es_data` volume and is exposed on port 9200.


5. **Run Tests**
   Verify the application with the test suite:
   ```bash
   docker compose exec app php artisan test
   ```
   - Now API endpoints are available at `http://localhost:8000/api/*` (e.g., `/api/posts`, `/api/comments`).   
   
### Stopping the Application
To stop and remove the containers:
```bash
docker compose down
```


## System Design Explanation

### Data Flow
API requests are routed through `routes/api.php` to `PostController` and `CommentController`, where `ElasticsearchService` manages Elasticsearch interactions for storage in `posts` and `comments` indices, and `NotificationService` handles notifications after entity creation.

### API Endpoints
The application exposes RESTful endpoints under `/api`, including `POST /api/posts`, `GET /api/posts/{id}`, `PUT /api/posts/{id}`, `DELETE /api/posts/{id}`, and `GET /api/posts` for posts, as well as `POST /api/comments`, `GET /api/comments/{id}`, `PUT /api/comments/{id}`, and `DELETE /api/comments/{id}` for comments.

### Elasticsearch Integration
Elasticsearch stores data in `posts` and `comments` indices with text fields like `caption` and `text` for search, managed by `ElasticsearchService` using the `elasticsearch/elasticsearch` PHP client for CRUD operations.

### Notification System
The `NotificationService@checkAndNotify` method, called after entity creation, checks `caption` or `text` for keywords (`'post'`, `'comment'`) and, if matched, logs the event via `Log::info` and echoes to the console.

### Docker Setup
The application runs on a `bitnami/laravel` image serving on port 8000, alongside Elasticsearch 8.12.0 on port 9200 with an `es_data` volume for persistence, connected via a bridge network (`app-network`) for seamless communication.

## Tech Stack and Package Reasons

- **PHP + Laravel**: Framework for API development; PHP 8.1, Laravel 10.x (via `bitnami/laravel`).
- **Elasticsearch**: Sole data store; version 8.12.0, with `elasticsearch/elasticsearch` client for PHP integration.
- **Docker + Docker Compose**: Consistent local setup; uses `bitnami/laravel` for simplicity.
- **Laravel Scout**: simplifies full-text search by offering a Eloquent-based API that integrates easily with Elasticsearch
