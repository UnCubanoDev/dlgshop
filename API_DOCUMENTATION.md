# Documentación de la API - Integración para App Móvil

## URL Base

```
http://localhost/api/v1
```

## Autenticación

La API utiliza Laravel Sanctum para la autenticación. Incluye el token Bearer en el encabezado Authorization:

```
Authorization: Bearer {token}
```

## Endpoints

### Autenticación

-   `POST /register` - Registrar nuevo usuario
-   `POST /login` - Iniciar sesión
-   `POST /logout` - Cerrar sesión (requiere autenticación)
-   `GET /user` - Obtener perfil del usuario (requiere autenticación)

### Productos

-   `GET /products` - Obtener todos los productos con filtros
-   `GET /products/featured` - Obtener productos destacados
-   `GET /products/{slug}` - Obtener producto por slug
-   `GET /collections/{slug}/products` - Obtener productos por colección

### Colecciones

-   `GET /collections` - Obtener todas las colecciones
-   `GET /collections/{slug}` - Obtener colección por slug

### Carrito de Compras (requiere autenticación)

-   `GET /cart` - Obtener carrito actual
-   `POST /cart/add` - Agregar item al carrito
-   `PUT /cart/{lineId}` - Actualizar cantidad del item del carrito
-   `DELETE /cart/{lineId}` - Eliminar item del carrito
-   `DELETE /cart` - Vaciar carrito

### Órdenes (requiere autenticación)

-   `GET /orders` - Obtener órdenes del usuario
-   `GET /orders/{orderId}` - Obtener detalles de la orden
-   `POST /orders` - Crear nueva orden

## Ejemplos de Uso

### Registrar Usuario

```bash
curl -X POST http://localhost/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Iniciar Sesión

```bash
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "juan@ejemplo.com",
    "password": "password123"
  }'
```

### Obtener Productos

```bash
curl -X GET "http://localhost/api/v1/products?per_page=10&search=laptop"
```

### Agregar al Carrito

```bash
curl -X POST http://localhost/api/v1/cart/add \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "variant_id": 1,
    "quantity": 2
  }'
```

### Crear Orden

```bash
curl -X POST http://localhost/api/v1/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "juan@ejemplo.com",
    "first_name": "Juan",
    "last_name": "Pérez",
    "phone": "+1234567890",
    "address": "Calle Principal 123",
    "city": "Ciudad de México",
    "postcode": "12345",
    "country": "MX"
  }'
```

## Parámetros de Consulta

### Productos

| Parámetro    | Tipo    | Descripción                                  |
| ------------ | ------- | -------------------------------------------- |
| `per_page`   | integer | Número de productos por página (default: 12) |
| `search`     | string  | Buscar productos por nombre o descripción    |
| `collection` | integer | Filtrar por ID de colección                  |
| `brand`      | integer | Filtrar por ID de marca                      |
| `min_price`  | float   | Precio mínimo                                |
| `max_price`  | float   | Precio máximo                                |

### Carrito

| Parámetro    | Tipo    | Descripción                    |
| ------------ | ------- | ------------------------------ |
| `variant_id` | integer | ID de la variante del producto |
| `quantity`   | integer | Cantidad a agregar (mínimo: 1) |

### Órdenes

| Parámetro    | Tipo   | Descripción          |
| ------------ | ------ | -------------------- |
| `email`      | string | Email del cliente    |
| `first_name` | string | Nombre del cliente   |
| `last_name`  | string | Apellido del cliente |
| `phone`      | string | Teléfono del cliente |
| `address`    | string | Dirección de envío   |
| `city`       | string | Ciudad               |
| `postcode`   | string | Código postal        |
| `country`    | string | País                 |

## Formato de Respuesta

Todas las respuestas de la API siguen este formato:

```json
{
    "success": true,
    "message": "Operación exitosa",
    "data": {
        // Datos de respuesta aquí
    }
}
```

## Formato de Respuesta de Error

```json
{
    "success": false,
    "message": "Descripción del error",
    "data": null
}
```

## Códigos de Estado HTTP

| Código | Descripción                                |
| ------ | ------------------------------------------ |
| 200    | OK - Solicitud exitosa                     |
| 201    | Created - Recurso creado exitosamente      |
| 400    | Bad Request - Datos de entrada inválidos   |
| 401    | Unauthorized - No autenticado              |
| 403    | Forbidden - No autorizado                  |
| 404    | Not Found - Recurso no encontrado          |
| 422    | Unprocessable Entity - Error de validación |
| 500    | Internal Server Error - Error del servidor |

## Ejemplos de Respuestas

### Respuesta de Productos

```json
{
    "success": true,
    "message": "Productos obtenidos exitosamente",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Laptop Gaming",
                "description": "Laptop para gaming de alto rendimiento",
                "thumbnail": {
                    "url": "https://ejemplo.com/imagen.jpg"
                },
                "variants": [
                    {
                        "id": 1,
                        "name": "Laptop Gaming - 16GB RAM",
                        "prices": [
                            {
                                "price": 2500000,
                                "currency": "MXN"
                            }
                        ]
                    }
                ]
            }
        ],
        "total": 50,
        "per_page": 12
    }
}
```

### Respuesta del Carrito

```json
{
    "success": true,
    "message": "Carrito obtenido exitosamente",
    "data": {
        "id": 1,
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "variant_id": 1,
                "name": "Laptop Gaming",
                "variant_name": "Laptop Gaming - 16GB RAM",
                "quantity": 2,
                "unit_price": 2500000,
                "total_price": 5000000,
                "thumbnail": {
                    "url": "https://ejemplo.com/imagen.jpg"
                }
            }
        ],
        "total": 5000000,
        "sub_total": 5000000,
        "tax_total": 800000,
        "shipping_total": 150000,
        "discount_total": 0
    }
}
```

## Notas Importantes

1. **Autenticación**: Todos los endpoints protegidos requieren el token Bearer en el encabezado Authorization.

2. **Precios**: Los precios se manejan en centavos para mayor precisión.

3. **Paginación**: Los endpoints que devuelven listas incluyen paginación automática.

4. **Validación**: Todos los endpoints incluyen validación de datos de entrada.

5. **CORS**: La API está configurada para permitir peticiones desde aplicaciones móviles.

## Soporte

Para soporte técnico o preguntas sobre la API, contacta al equipo de desarrollo.

---

**Versión**: 1.0  
**Última actualización**: 2024
