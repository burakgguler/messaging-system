# Messaging System

This project is an automatic message sending platform built with Laravel.
It sends messages stored in the database to an external webhook using a queue-based
architecture with rate limiting and Redis caching.

---

## Project Overview

- Messages are stored in the database with recipient phone numbers and content.
- Pending messages are sent asynchronously using Laravel Queues.
- The system sends **2 messages every 5 seconds** using delayed queue jobs.
- After sending, messages are marked as sent and the returned `messageId` is stored.
- Sent message metadata is cached in Redis.
- A REST API is provided to retrieve sent messages with pagination.

---

## Architecture Overview

The application follows a layered architecture:

- **Command Layer**
    - Triggers the message sending process via Artisan.
- **Service Layer**
    - Contains business logic such as batching, rate limiting, and validation.
- **Repository Layer**
    - Handles all database operations.
- **Job / Queue Layer**
    - Sends messages asynchronously to the webhook.
- **Cache Layer (Redis)**
    - Caches `messageId` and `sent_at` values after sending.

This design ensures clean separation of concerns, testability, and compliance with
Laravel best practices.

---

## Message Sending Flow

1. `php artisan messages:send` command is executed.
2. The command calls `MessageService`.
3. All pending messages are retrieved from the database.
4. Messages are chunked into batches of **2 messages**.
5. Each batch is dispatched with a **5-second delay** between batches.
6. `SendMessageJob` sends the message to the webhook.
7. Returned `messageId` and `sent_at` are:
    - Stored in the database
    - Cached in Redis

---

## API Documentation

### Get Sent Messages

- **Endpoint:** `GET /api/messages/sent`
- **Base URL:** `http://localhost:8080`
- **Features:**
    - Pagination support
    - Returns content, phone number, messageId and sent timestamp
    - Sorted by sent date in descending order.

#### Example Request

```http
GET http://localhost:8080/api/messages/sent?per_page=10&page=1
```

#### Example Response

```json
{
  "meta": {
    "total": 42,
    "per_page": 10,
    "current_page": 1,
    "last_page": 5
  },
  "data": [
    {
      "id": 1,
      "phone_number": "+905551112233",
      "content": "Hello World",
      "message_id": "67f2f8a8-ea58-4ed0-a6f9-ff217df4d849",
      "sent_at": "2026-01-04 12:30:00"
    }
  ]
}
```

## Swagger / OpenAPI

Swagger UI is available at:

```http
http://localhost:8080/api/documentation
```

## Installation (Docker)

Clone the repository and build the containers:

```shell
git clone git@github.com:burakgguler/messaging-system.git
```

```shell
docker compose up -d --build
```

Install dependencies and prepare the application:

```shell
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

## Running the Message Sender

Dispatch pending messages:

```shell
docker compose exec app php artisan messages:send
```

Start the queue worker:
```shell
docker compose exec app php artisan queue:work
```

## Redis

Redis is used to cache sent message metadata.

Cached key format:

```
message:{messageId}
```

Example value:

```json
{
  "sent_at": "2026-01-04 12:30:00"
}
```

## Environment Variables
The application uses environment variables for configuration.
A `.env.example` file is provided as a reference.

Before running the application, create your `.env` file:

```shell
cp .env.example .env
```

#### Key environment variables include:

- Application settings (APP_KEY, APP_ENV)

- Database connection

- Queue driver configuration

- Redis connection

- External webhook URL

## Running Tests

The project includes both unit and integration tests.

Run all tests:

```shell
docker compose exec app php artisan test
```

## Notes

- Laravel 11+

- PHP 8+

- Queue driver: database

- Cache driver: Redis

- All services are fully dockerized