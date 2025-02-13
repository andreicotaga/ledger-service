# Ledger Service API

## 📌 Overview
The **Ledger Service API** is a high-performance financial transactions system that allows users to manage ledgers, track balances, and process transactions efficiently. Built with **Symfony API Platform**, the service includes rate-limiting, Redis-based messaging, and Doctrine ORM for database management.

---

## 🚀 Getting Started

### 1️⃣ **Clone the Repository**
```sh
git clone https://github.com/andreicotaga/ledger-service
cd ledger-service
```
### 2️⃣ **Install Dependencies**
```sh
composer install
```

### 3️⃣ **Set Up Environment Variables**
```sh
cp .env.example .env
```
#### Modify `.env` to include your database credentials and other environment variables.
```env
APP_ENV=dev
DATABASE_URL=
POSTGRES_DB=
POSTGRES_USER=
POSTGRES_PASSWORD=
LOCK_DSN=redis://symfony_redis
MESSENGER_TRANSPORT_DSN=redis://symfony_redis:6379/messages
REDIS_HOST=symfony_redis
REDIS_PORT=6379
```

### 4️⃣ **Start the Docker Containers**
```sh
docker-compose up -d
```

### 5️⃣ **Run the Migrations**
```sh
docker exec -it symfony_php php bin/console doctrine:migrations:migrate
```

### 6️⃣ **Run the Application**
```sh
http://localhost:8080
```

---

## 📝 Documentation

### 🔥 Swagger UI
Access API documentation via OpenAPI (Swagger) UI:
```sh
http://localhost:8080/api
```

### 📚 API Endpoints
The Ledger Service API provides the following endpoints:
```sh
HTTP    Endpoint	                Description
POST	/api/ledgers	                Create a new ledger
GET	/api/ledgers/{id}	        Get a specific ledger
POST	/api/ledger_balances	        Create a balance entry
PUT	/api/ledger_balances/{id}	Update ledger balance
POST	/api/transactions	        Process a transaction
GET	/api/transactions/{id}	        Get transaction details
```

---
### 🛠️ Example API Requests
1️⃣ **Create a Ledger**
```sh
curl -X POST "http://localhost:8080/api/ledgers" -H "Content-Type: application/json" -d '{
    "name": "Business Account",
    "baseCurrency": "USD"
}'
```

2️⃣ **Create a Transaction**
```sh
curl -X POST "http://localhost:8080/api/transactions" -H "Content-Type: application/json" -d '{
    "ledger": "/api/ledgers/1",
    "amount": "500.00",
    "currency": "USD",
    "transactionType": "credit"
}'
```
---

## 📦 Architecture

#### Technologies Used
- **Symfony API Platform** for RESTful API
- **PostgreSQL** for database storage
- **Redis** for caching and messaging
- **Docker & Docker Compose** for containerized deployment
- **PHPUnit** for unit and integration testing

#### High-Level Design 
- **Rate Limiting**: Transactions are throttled using a Redis-based rate limiter.
- **Async Processing**: Transactions are handled using Symfony Messenger.
- **API Documentation**: OpenAPI (Swagger) provides an interactive API UI.
- **Database Integrity**: Transactions and balances are maintained with strict foreign key constraints.

---

## ✅ Running Tests
```sh
docker exec -it symfony_php php bin/phpunit
```