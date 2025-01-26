# Orders-be: Api routes

## Order Viewing Page

**`GET /api/orders`**

### Query Parameters (optionals)
| name        | type              | description                                     | example                                     |
|-------------|-------------------|-------------------------------------------------|---------------------------------------------|
| date_start  | string(date-time) | return orders created after this point in time  | `2025-01-26T18:20:14Z`                      |
| date_end    | string(date-time) | return orders created before this point in time | `2025-01-26T18:20:14Z`                      |
| name        | string            | return orders with corresponding name           | `Susana's Birthday`                         |
| description | string            | return orders with corresponding description    | `I got this presents for Susana's birthday` |
| index       | integer           | return orders from this index                   | `0`                                         |

### Response

**200 - OK**

| name   | type    | description               |
|--------|---------|---------------------------|
| index  | integer | returned starting index   |
| size   | integer | returned orders number    | 
| orders | array   | a list of the user orders |

*example response*
```json
{
    "index": 0,
    "size": 2,
    "orders": [
        {
            "ID": 12345,
            "name": "Susana's Birthday",
            "description" : "I got this presents for Susana's birthday",
        },
        {
            "ID": 22222,
            "name": "Football scarf",
            "description" : "",
        }
    ]
}
```

## Detailed Order View

**`GET /api/orders/:ID`**

### Response

**200 - OK**

| name        | type    | description               |
|-------------|---------|---------------------------|
| ID          | integer | returned starting index   |
| name        | string  | order name                | 
| description | string  | order description         |
| products    | array   | returned order's products |

*example response*
```json
{
    "ID": 12345,
    "name": "Susana's Birthday",
    "description" : "I got this presents for Susana's birthday",
    "products" : [
        {
            "name": "Toy puzzle",
            "price" : "30$",
            "quantity": 1,
        }
    ]
}
```

## Order Management

**`POST /api/orders`**

### Body

*example request*
```json
{
    "name": "New Order",
    "description" : "order description",
    "products" : [
        {
            "id": 12345,
            "quantity": 1
        }
    ]
}
```

### Response

**200 - OK**

*example response*
```json
{
    "message" : "Order created"
}
```
**500 - Error**
```json
{
    "message" : "Product(s) out of stock"
}
```

**`PUT /api/orders/:ID`**

### Body

*example request*
```json
{
    "name": "Changed order",
    "description" : "order description",
    "products" : [
        {
            "id": 12345,
            "quantity": 2
        }
    ]
}
```

### Response

**200 - OK**

*example response*
```json
{
    "message" : "Order modified"
}
```
**500 - Error**
```json
{
    "message" : "Product(s) out of stock"
}
```

**`DELETE /api/orders/:ID`**

### Response

**200 - OK**

*example response*
```json
{
    "message" : "Order deleted"
}
```