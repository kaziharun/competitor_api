# Competitor API

## Installation

1. Install dependencies:
   ```bash
   composer install
   ```
2. Configure your `.env` file with database and Redis settings.
3. Run migrations:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. **Start services (optional):**
```bash
# Start containers
docker compose up -d

# Install Composer dependencies
docker compose exec php composer install

# Create database
docker compose exec php bin/console doctrine:database:create

# Run migrations
docker compose exec php bin/console doctrine:migrations:migrate -n
```

## Fetch Data for a Product

Run the fetch command (replace `123` with your product ID):
```bash
php bin/console app:fetch-prices-async 123
```

## Fetch Data for All Products

Run the fetch command without a product ID:
```bash
php bin/console app:fetch-prices-async
```

## Run the Queue Worker

Start the messenger worker (if using async transport):
```bash
php bin/console messenger:consume async -vv
```

## Fetch Price via API

Make a GET request (replace `123` with your product ID):
```bash
curl -X GET "http://localhost:8000/api/prices/123" \
  -H "X-API-Key: K4kP9wqX2YbV5nJm8tRv7sA6zQ3fH1gL" 
```

## Fetch All Prices via API

Make a GET request to fetch all prices:
```bash
curl -X GET "http://localhost:8000/api/prices" \
  -H "X-API-Key: K4kP9wqX2YbV5nJm8tRv7sA6zQ3fH1gL" 
```

## Run integration test
composer test:integration

## Run unit test
composer test:unit

## Code Structure

This project follows a **Clean Architecture** pattern with the following structure:

## **Core Architecture Layers**

### **Domain Layer** (`src/Product/Domain/`)
- **Entities**: `ProductPrice` - Core business objects representing product price data
- **Value Objects**: 
  - `ProductId`, `Price`, `VendorName` - Core business values
  - `FetchedAt`, `RequestId`, `PriceData` - Additional business value objects
- **Services**: 
  - `ExternalApiInterface` - Domain contract for external API interactions
  - `ProductFetchServiceInterface` - Domain contract for product fetching
  - `DefaultProductIdsService` - Default product ID management
- **Repository**: Interface defining data access contracts
- **Validation**: Domain-level validation rules

### **Application Layer** (`src/Product/Application/`)
- **Use Cases**: 
  - `FetchCompetitorPricesUseCase` - Fetch prices from competitor APIs
  - `GetProductPriceByIdUseCase` - Retrieve specific product price
  - `GetAllProductPricesUseCase` - Retrieve all product prices
- **Services**: 
  - `ProductPriceService` - Main application service for price operations
  - `FetchResult` - Result wrapper for fetch operations
- **Message Handlers**: Async message processing for background operations
- **Commands/Queries**: Command and query data structures
- **DTOs**: Data transfer objects for application layer
- **Exceptions**: Application-specific exceptions

### **Infrastructure Layer** (`src/Product/Infrastructure/`)
- **API Clients**: 
  - `CompetitorApi1`, `CompetitorApi2`, `CompetitorApi3` - External competitor API integrations
- **API Factory**: 
  - `CompetitorApiFactory` - Factory for creating API client instances
- **Services**: 
  - `CompetitorPriceService` - Service for aggregating competitor prices
  - `AsyncProductFetchService` - Asynchronous product fetching service
- **Repository**: 
  - `ProductPriceRepository` - Doctrine-based database implementation
- **Cache**: 
  - `ProductPriceCacheService` - Redis-based caching layer for performance

### **Presentation Layer** (`src/Product/Presentation/`)
- **Controllers**: 
  - `ProductPriceController` - HTTP request handling for price endpoints
- **Validation**: Presentation-level validation rules

## **Shared Components** (`src/Shared/`)

### **Shared Domain** (`src/Shared/Domain/`)
- **Value Objects**: 
  - `Identifier` - Base identifier value object used across domains
- **Entities**: Shared entity base classes

### **Shared Infrastructure** (`src/Shared/Infrastructure/`)
- Common infrastructure components used across domains

### **Shared Presentation** (`src/Shared/Presentation/`)
- Common presentation components and utilities

## **Key Features**
- **Clean Architecture**: Strict separation of concerns with dependency inversion
- **Domain-Driven Design**: Rich domain model with value objects and entities
- **Async Processing**: Symfony Messenger integration for background operations
- **Multi-Source Aggregation**: Fetches prices from multiple APIs and stores optimal prices
- **Caching Strategy**: Redis-based caching for improved performance
- **Error Handling**: Centralized exception handling with proper HTTP status codes
- **Validation**: Multi-layer validation (Domain, Application, Presentation)
- **Testing**: Comprehensive unit and integration test coverage 