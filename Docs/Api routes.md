# Orders-be: Api routes

## Introduction

This documentation provides a description of the API methods needed to interact with the orders and products backend.

# Authorization

Access to the api methods is available via tokens given to an user once it's registered. 

The token must be provided in the request headers.

```bash
curl -X GET "<endpoint>" \
     -H "Authorization: Bearer <token>" \
     -H "Content-Type: application/json"
```

## Token roles

- "user" tokens cannot create or modify products.
- "admin" tokens have full access to every endpoint.


### Response 

**401 UNAUTHORIZED** (invalid or missing token)

```json
{
    "message": "Unauthenticated."
}
```

**403 FORBIDDEN** (invalid token role for the action)

```json
{
    "message": "This action is unauthorized."
}
```


## Getting a token

**`POST /api/users`**

### Request Body

| name                 | type              | description                                    |
| -------------------- | ----------------- | ---------------------------------------------- |
| name                 | string (max 255)  | username                                       |
| role                 | `admin` \| `user` | user role. Admins have all permissions granted |
| name                 | string            | return orders with corresponding name          |
| credentials.email    | string (email)    | user's email                                   |
| credentials.password | string (min )     | user's password                                |

**Body example:**

```json
{
  "name": "test_user",
  "role": "user",
  "credentials": {
    "email": "test_user@test.com",
    "password": "password"
  }
}
```

### Response

**200 OK**

| name  | type              | description      |
| ----- | ----------------- | ---------------- |
| token | string            | Api access token |
| role  | `admin` \| `user` | user role        |

```json
{
  "token": "5|xZTqpEIqhwzmLuC1XICoT9CWsSTWiU4uLgtF6xbLce037946",
  "role": "user"
}
```

**400 BAD REQUEST**

```json
{
  "error": "user already exists",
  "status": 400
}
```

**422 UNPROCESSABLE CONTENT**

```json
{
  "message": "The name field must be a string.",
  "errors": {
    "name": ["The name field must be a string."]
  }
}
```

# Orders

## View all the user's orders

**`GET /api/orders`**

### Query Parameters (optionals)

| name        | type                   | description                                     | example                |
| ----------- | ---------------------- | ----------------------------------------------- | ---------------------- |
| date_start  | ISO 8601 UTC timestamp | return orders created after this point in time  | `2025-01-26T18:20:14Z` |
| date_end    | ISO 8601 UTC timestamp | return orders created before this point in time | `2025-01-26T18:20:14Z` |
| name        | string (max 255)       | return orders with corresponding name           | `test order`           |
| description | string                 | return orders with corresponding description    | `test description`     |
| page        | integer                | return orders from this page                    | `0`                    |

### Response

**200 - OK**

| name  | type   | description            |
| ----- | ------ | ---------------------- |
| data  | array  | list of fetched orders |
| links | object | navigation links       |
| meta  | object | pagination data        |

```json
{
    "data": [
        {
            "ID": 1,
            "name": "test order",
            "description": "this is a test order"
        }
    ],
    "links": {
        "first": "http://localhost/api/orders?page=1",
        "last": "http://localhost/api/orders?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost/api/orders?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://localhost/api/orders",
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
```

**422 UNPROCESSABLE CONTENT**
```json
{
    "message": "The description field must be a string.",
    "errors": {
        "description": [
            "The description field must be a string."
        ]
    }
}
```

## View a order details

**`GET /api/orders/:orderId`**

### Response

**200 - OK**

| name        | type    | description               |
| ----------- | ------- | ------------------------- |
| ID          | integer | order id                  |
| name        | string  | order name                |
| description | string  | order description         |
| products    | array   | order products            |

```json
{
    "data": {
        "ID": 1,
        "name": "test order",
        "description": "this is a test order",
        "products": [
            {
                "ID": 1,
                "name": "cake",
                "price": 100,
                "quantity": 3
            }
        ]
    }
}
```

**401 UNAUTHORIZED** ( Trying to acces a different user's order )
```json
{
    "Message": "unauthorized"
}
```

**404 NOT FOUND**
```json
{
    "Message": "order not found"
}
```

## Create a new order

**`POST /api/orders`**

### Request Body

| name                 | type              | description                                    |
| -------------------- | ----------------- | ---------------------------------------------- |
| name                 | string (max 255)  | order name                                     |
| description          | string            | (optional) order description                   |
| products             | array             | list of products to add to the order           |
| products.*.ID        | integer           | product id                                     |
| products.*.quantity  | integer           | product quantity                               |

```json
{
    "name": "test order",
    "description": "this is a test order",
    "products": [
        {
            "ID": 1,
            "quantity": 3
        }
    ]
}
```

### Response

**200 - OK**
| name                 | type              | description                                    |
| -------------------- | ----------------- | ---------------------------------------------- |
| data                 | object            | created order details                          |

```json
{
    "data": {
        "ID": 2,
        "name": "test order",
        "description": "this is a test order",
        "products": [
            {
                "ID": 1,
                "name": "cake",
                "price": 100,
                "quantity": 3
            }
        ]
    }
}
```

**400 BAD REQUEST**

```json
{
    "error": "Insufficient stock for product ID: 1",
    "status": 400
}
```

**422 UNPROCESSABLE CONTENT**

```json
{
    "message": "The products.0.quantity field must be an integer.",
    "errors": {
        "products.0.quantity": [
            "The products.0.quantity field must be an integer."
        ]
    }
}
```

## Delete an order

**`DELETE /api/orders/:orderId`**

### Response

**200 OK**

```json
{
    "message": "OK"
}
```

**401 unauthorized** ( Trying to acces a different user's order )

```json
{
    "error": "unauthorized",
    "status": 401
}
```

**404 NOT FOUND**

```json
{
    "error": "order not found",
    "status": 404
}
```

## Update an order

**`PUT /api/orders/:orderId`**

### Request body

| name                 | type              | description                                    |
| -------------------- | ----------------- | ---------------------------------------------- |
| name                 | string (max 255)  | new order name                                 |
| description          | string            | new order description                          |

**Body example:**

```json
{
  "name": "new test Order",
  "role": "new test Description",
}
```

### Response

**200 OK**

```json
{
    "message": "OK"
}
```

**401 unauthorized** ( Trying to acces a different user's order )

```json
{
    "error": "unauthorized",
    "status": 401
}
```

**404 NOT FOUND**

```json
{
    "error": "order not found",
    "status": 404
}
```

## Add products to an existing order

**`POST /api/orders/:orderId/products`**

### Request body

| name                 | type              | description                                    |
| -------------------- | ----------------- | ---------------------------------------------- |
| products             | array             | list of products to add to the order           |
| products.*.ID        | integer           | product id                                     |
| products.*.quantity  | integer           | product quantity                               |

**Body example:**

```json
{
    "products": [
        {
            "ID": 1,
            "quantity": 3
        }
    ]
}
```

### Response

**200 - OK**
| name                 | type              | description                                    |
| -------------------- | ----------------- | ---------------------------------------------- |
| data                 | object            | order details                                  |

```json
{
    "data": {
        "ID": 2,
        "name": "test order",
        "description": "this is a test order",
        "products": [
            {
                "ID": 1,
                "name": "cake",
                "price": 100,
                "quantity": 3
            }
        ]
    }
}
```

**400 BAD REQUEST**

```json
{
    "error": "Insufficient stock for product ID: 1",
    "status": 400
}
```

**422 UNPROCESSABLE CONTENT**

```json
{
    "message": "The products.0.quantity field must be an integer.",
    "errors": {
        "products.0.quantity": [
            "The products.0.quantity field must be an integer."
        ]
    }
}
```

## Remove a prodct from an existing order

**`DELETE /api/orders/:orderId/products/:productId`**

**200 OK**

```json
{
    "message": "OK"
}
```

**400 BAD REQUEST**

```json
{
    "error": "Product not found in the order",
    "status": 400
}
```

**401 unauthorized** ( Trying to acces a different user's order )
 
```json
{
    "error": "unauthorized",
    "status": 401
}
```

**404 NOT FOUND**

```json
{
    "error": "order not found",
    "status": 404
}
```

# Products

## View all products and remaining stock

**`GET api/products`**

### Response

**200 - OK**

| name  | type   | description              |
| ----- | ------ | ------------------------ |
| data  | array  | list of fetched products |
| links | object | navigation links         |
| meta  | object | pagination data          |

```json
{
    "data": [
        {
            "ID": 1,
            "name": "cake",
            "price": 100,
            "availableQuantity": 18
        },
        {
            "ID": 2,
            "name": "bread",
            "price": 10,
            "availableQuantity": 200
        }
    ],
    "links": {
        "first": "http://localhost/api/products?page=1",
        "last": "http://localhost/api/products?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost/api/products?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://localhost/api/products",
        "per_page": 15,
        "to": 2,
        "total": 2
    }
}
```

## View a product details

**`GET api/products/:productID`**

### Response

**200 - OK**

| name              | type    | description               |
| ----------------- | ------- | ------------------------- |
| ID                | integer | product id                |
| name              | string  | product name              |
| price             | float   | product price             |
| availableQuantity | integer | product remaining stock   |

```json
{
    "data": {
        "ID": 1,
        "name": "cake",
        "price": 100,
        "availableQuantity": 18
    }
}
```

**404 NOT FOUND**

```json
{
    "error": "product not found",
    "status": 404
}
```

## Register new products

**`POST /api/products`**

### Request body

| name                 | type              | description                                     |
| -------------------- | ----------------- | ------------------------------------------------| 
| products             | array             | list of products to register                    |
| products.*.name      | string (max 255)  | product name                                    |
| products.*.price     | numeric (min 0)   | product price                                   |
| products.*.quantity  | integer (min 1)   | (optional) product stock quantity. Default is 0 |

**Body example:**

```json
{
    "products": [
        {
            "name": "pastry",
            "price": 10,
            "quantity": 200
        }
    ]
}
```

### Response

**200 - OK**

| name              | type    | description                 |
| ----------------- | ------- | --------------------------- |
| data              | array   | list of registered products |

```json
{
    "data": [
        {
            "ID": 5,
            "name": "pastry",
            "price": 10,
            "availableQuantity": 200
        }
    ]
}
```