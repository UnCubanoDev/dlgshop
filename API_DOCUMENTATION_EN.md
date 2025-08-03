# API Documentation - Mobile App Integration

## Base URL

```
http://localhost/api/v1
```

## Authentication

The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

## Endpoints

### Authentication

-   `POST /register` - Register new user
-   `POST /login` - Login user
-   `POST /logout` - Logout user (requires auth)
-   `GET /user` - Get user profile (requires auth)

### Products

-   `GET /products` - Get all products with filters
-   `GET /products/featured` - Get featured products
-   `GET /products/{slug}` - Get product by slug
-   `GET /collections/{slug}/products` - Get products by collection

### Collections

-   `GET /collections` - Get all collections
-   `GET /collections/{slug}` - Get collection by slug

### Cart (requires auth)

-   `GET /cart` - Get current cart
-   `POST /cart/add` - Add item to cart
-   `PUT /cart/{lineId}` - Update cart item quantity
-   `DELETE /cart/{lineId}` - Remove item from cart
-   `DELETE /cart` - Clear cart

### Orders (requires auth)

-   `GET /orders` - Get user orders
-   `GET /orders/{orderId}` - Get order details
-   `POST /orders` - Create new order

## Example Usage

### Register User

```bash
curl -X POST http://localhost/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login

```bash
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Products

```bash
curl -X GET "http://localhost/api/v1/products?per_page=10&search=laptop"
```

### Add to Cart

```bash
curl -X POST http://localhost/api/v1/cart/add \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "variant_id": 1,
    "quantity": 2
  }'
```

### Create Order

```bash
curl -X POST http://localhost/api/v1/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "+1234567890",
    "address": "123 Main St",
    "city": "New York",
    "postcode": "10001",
    "country": "US"
  }'
```

````

### 10. Configuración adicional en `app/Http/Kernel.php`

```php:app/Http/Kernel.php
// ... existing code ...

protected $middlewareGroups = [
    'web' => [
        // ... existing middleware ...
    ],

    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

// ... existing code ...
````

## Características de la API:

1. **Autenticación completa** con Laravel Sanctum
2. **Gestión de productos** con filtros y búsqueda
3. **Gestión de colecciones**
4. **Carrito de compras** completo
5. **Sistema de órdenes**
6. **Respuestas estandarizadas** con formato JSON consistente
7. **Validación de datos** en todos los endpoints
8. **Manejo de errores** robusto
9. **Documentación completa** para desarrolladores móviles

Esta API está lista para ser consumida por cualquier app móvil (iOS, Android, React Native, Flutter, etc.) y proporciona todas las funcionalidades básicas de e-commerce necesarias para una tienda móvil completa.
