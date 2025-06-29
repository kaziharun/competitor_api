# Metro Market

## Installation

1. Install dependencies:
   ```bash
   composer install
   ```
2. Configure your `.env` file with database and Redis settings.
3. Run migrations:
   ```bash
   php bin/console doctrine:migrations:migrate
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
  -H "X-API-Key: K4kP9wqX2YbV5nJm8tRv7sA6zQ3fH1gL" \
  -H "Content-Type: application/json"
```

## Fetch All Prices via API

Make a GET request to fetch all prices:
```bash
curl -X GET "http://localhost:8000/api/prices" \
  -H "X-API-Key: K4kP9wqX2YbV5nJm8tRv7sA6zQ3fH1gL" \
  -H "Content-Type: application/json"
```

## Code Structure

This project follows a **Clean Architecture** pattern with the following structure:

### **Domain Layer** (`src/Product/Domain/`)
- **Entities**: `ProductPrice` - Core business objects
- **Value Objects**: `ProductId`, `Price`, `VendorName` - Immutable business values
- **Services**: `PriceAggregationService` - Business logic for price aggregation
- **Repositories**: Interfaces defining data access contracts

### **Application Layer** (`src/Product/Application/`)
- **Use Cases**: Business operations like `FetchCompetitorPricesUseCase`
- **Services**: `CompetitorPriceService`, `ProductPriceApiService` - Application logic
- **Commands/Queries**: `FetchPricesCommandData` - Input/output data structures
- **Message Handlers**: `FetchPricesMessageHandler` - Async message processing

### **Infrastructure Layer** (`src/Product/Infrastructure/`)
- **APIs**: `CompetitorApi1`, `CompetitorApi2`, `CompetitorApi3` - External API integrations
- **Repository**: `ProductPriceRepository` - Database implementation
- **Cache**: `ProductPriceCacheService` - Caching layer
- **External**: `MockApiClient`, `RetryableApiClient` - HTTP client implementations

### **Presentation Layer** (`src/Product/Presentation/`)
- **Controllers**: `ProductPriceController` - HTTP request handling
- **DTOs**: Request/Response data transfer objects

### **Key Features**
- **Async Processing**: Uses Symfony Messenger for background price fetching
- **Multi-Source Aggregation**: Fetches prices from multiple APIs and stores only the lowest
- **Caching**: Redis-based caching for performance
- **Error Handling**: Centralized error handling with proper HTTP status codes
- **Validation**: Input validation at multiple layers 