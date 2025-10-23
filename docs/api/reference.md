# API Reference

## 📋 Complete Endpoint Reference

### Authentication Endpoints

#### Get Authentication Token
```http
POST /auth/token
Content-Type: application/json

{
  "username": "your_username",
  "password": "your_password"
}
```

**Response:**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600,
  "token_type": "Bearer"
}
```

#### Validate Token
```http
GET /auth/validate
Authorization: Bearer {token}
```

#### Revoke Token
```http
POST /auth/revoke
Authorization: Bearer {token}
```

### Bands Endpoints

#### List Bands
```http
GET /bands
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `page` | integer | No | Page number (default: 1) |
| `per_page` | integer | No | Items per page (1-100, default: 20) |
| `search` | string | No | Search term |
| `orderby` | string | No | Field to order by (name, date, id) |
| `order` | string | No | Order direction (asc, desc) |
| `status` | string | No | Filter by status (active, inactive) |

#### Get Band
```http
GET /bands/{id}
```

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | Band ID |

#### Create Band
```http
POST /bands
Content-Type: application/json

{
  "name": "Band Name",
  "slug": "band-name",
  "contact_email": "band@example.com",
  "contact_phone": "+1234567890",
  "description": "Band description",
  "website": "https://band.com",
  "genres": ["Rock", "Metal"],
  "social_media": {
    "facebook": "https://facebook.com/band",
    "instagram": "https://instagram.com/band",
    "twitter": "https://twitter.com/band"
  },
  "payment_details": {
    "bank_account": "1234567890",
    "bank_name": "Bank Name",
    "paypal": "band@paypal.com"
  },
  "status": "active"
}
```

**Required Fields:** `name`, `contact_email`

#### Update Band
```http
PUT /bands/{id}
Content-Type: application/json

{
  "name": "Updated Band Name",
  "contact_email": "updated@band.com",
  "description": "Updated description"
}
```

#### Delete Band
```http
DELETE /bands/{id}
```

#### Band Statistics
```http
GET /bands/{id}/stats
```

**Response:**
```json
{
  "total_sales": 1250.00,
  "total_items_sold": 45,
  "average_sale": 27.78,
  "current_stock_value": 2500.00,
  "top_selling_items": [
    {
      "item_id": 101,
      "name": "T-Shirt",
      "sales": 500.00,
      "quantity": 20
    }
  ],
  "sales_trend": [
    {
      "month": "2024-01",
      "sales": 250.00,
      "items": 10
    }
  ]
}
```

### Merchandise Endpoints

#### List Merchandise
```http
GET /merchandise
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `band_id` | integer | No | Filter by band |
| `category` | string | No | Filter by category |
| `in_stock` | boolean | No | Only in-stock items |
| `low_stock` | boolean | No | Only low-stock items |
| `search` | string | No | Search term |
| `orderby` | string | No | Field to order by |
| `order` | string | No | Order direction |

#### Get Merchandise Item
```http
GET /merchandise/{id}
```

#### Create Merchandise Item
```http
POST /merchandise
Content-Type: application/json

{
  "name": "Product Name",
  "description": "Product description",
  "price": 25.00,
  "cost_price": 10.00,
  "stock_quantity": 100,
  "low_stock_threshold": 10,
  "band_id": 123,
  "categories": ["Clothing", "T-Shirts"],
  "variations": [
    {
      "size": "M",
      "color": "Black",
      "price_adjustment": 0.00,
      "stock": 25,
      "sku": "TSHIRT-M-BLACK"
    }
  ],
  "images": [
    {
      "url": "https://example.com/image.jpg",
      "alt": "Product image",
      "is_primary": true
    }
  ],
  "attributes": {
    "material": "Cotton",
    "weight": 0.2,
    "dimensions": {
      "length": 70,
      "width": 50,
      "height": 5
    }
  },
  "status": "active"
}
```

**Required Fields:** `name`, `price`, `band_id`

#### Update Merchandise Item
```http
PUT /merchandise/{id}
Content-Type: application/json

{
  "price": 27.00,
  "description": "Updated description",
  "stock_quantity": 95
}
```

#### Update Stock Level
```http
PATCH /merchandise/{id}/stock
Content-Type: application/json

{
  "quantity": 5,
  "reason": "restock",
  "notes": "Received new shipment",
  "user_id": 1
}
```

**Stock Reasons:** `restock`, `sale`, `adjustment`, `damage`, `return`, `other`

#### Delete Merchandise Item
```http
DELETE /merchandise/{id}
```

#### Merchandise Images
```http
POST /merchandise/{id}/images
Content-Type: multipart/form-data

{
  "image": file,
  "alt": "Image description",
  "is_primary": false
}
```

```http
DELETE /merchandise/{id}/images/{image_id}
```

### Sales Endpoints

#### List Sales
```http
GET /sales
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `band_id` | integer | No | Filter by band |
| `date_from` | string | No | Start date (YYYY-MM-DD) |
| `date_to` | string | No | End date (YYYY-MM-DD) |
| `show_id` | integer | No | Filter by show |
| `sales_page_id` | integer | No | Filter by sales page |
| `payment_type` | string | No | Filter by payment type |
| `min_amount` | number | No | Minimum sale amount |
| `max_amount` | number | No | Maximum sale amount |

#### Get Sale
```http
GET /sales/{id}
```

#### Create Sale
```http
POST /sales
Content-Type: application/json

{
  "band_id": 123,
  "show_id": 456,
  "sales_page_id": 789,
  "date": "2024-01-15T14:30:00Z",
  "items": [
    {
      "merchandise_id": 101,
      "quantity": 2,
      "unit_price": 25.00,
      "discount": 0.00
    },
    {
      "merchandise_id": 102,
      "quantity": 1,
      "unit_price": 15.00,
      "discount": 2.00
    }
  ],
  "payment_type": "card",
  "payment_details": {
    "card_last4": "4242",
    "card_brand": "visa",
    "transaction_id": "ch_123456789"
  },
  "customer_info": {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890"
  },
  "shipping_info": {
    "address": "123 Main St",
    "city": "Anytown",
    "state": "CA",
    "zip": "12345",
    "country": "US"
  },
  "tax_amount": 5.25,
  "discount_amount": 2.00,
  "notes": "Customer requested receipt",
  "status": "completed"
}
```

**Required Fields:** `band_id`, `items`, `payment_type`

#### Update Sale
```http
PUT /sales/{id}
Content-Type: application/json

{
  "notes": "Updated sale notes",
  "status": "refunded"
}
```

#### Process Refund
```http
POST /sales/{id}/refund
Content-Type: application/json

{
  "amount": 25.00,
  "reason": "customer_return",
  "items": [
    {
      "merchandise_id": 101,
      "quantity": 1,
      "refund_amount": 25.00
    }
  ],
  "notes": "Customer returned item",
  "restock_items": true
}
```

**Refund Reasons:** `customer_return`, `damaged`, `wrong_item`, `other`

#### Delete Sale
```http
DELETE /sales/{id}
```

### Reports Endpoints

#### Sales Report
```http
GET /reports/sales
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `band_id` | integer | No | Filter by band |
| `date_from` | string | No | Start date (YYYY-MM-DD) |
| `date_to` | string | No | End date (YYYY-MM-DD) |
| `group_by` | string | No | day, week, month, year |
| `include_details` | boolean | No | Include item details |
| `format` | string | No | json, csv, pdf |

#### Stock Report
```http
GET /reports/stock
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `band_id` | integer | No | Filter by band |
| `category` | string | No | Filter by category |
| `low_stock_only` | boolean | No | Only low stock items |
| `format` | string | No | json, csv, pdf |

#### Financial Report
```http
GET /reports/financial
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `band_id` | integer | No | Filter by band |
| `date_from` | string | No | Start date (YYYY-MM-DD) |
| `date_to` | string | No | End date (YYYY-MM-DD) |
| `include_costs` | boolean | No | Include cost calculations |
| `format` | string | No | json, csv, pdf |

### Webhooks Endpoints

#### List Webhooks
```http
GET /webhooks
```

#### Create Webhook
```http
POST /webhooks
Content-Type: application/json

{
  "url": "https://your-app.com/webhooks",
  "events": ["sale.created", "sale.updated"],
  "secret": "your-secret-key",
  "status": "active",
  "description": "Sales webhook"
}
```

#### Get Webhook
```http
GET /webhooks/{id}
```

#### Update Webhook
```http
PUT /webhooks/{id}
Content-Type: application/json

{
  "events": ["sale.created", "stock.updated"],
  "status": "inactive"
}
```

#### Delete Webhook
```http
DELETE /webhooks/{id}
```

#### Test Webhook
```http
POST /webhooks/{id}/test
Content-Type: application/json

{
  "event": "sale.created",
  "payload": {
    "sale_id": 123,
    "amount": 100.00
  }
}
```

### Utility Endpoints

#### Health Check
```http
GET /health
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2024-01-15T14:30:00Z",
  "version": "1.0.0",
  "database": "connected",
  "cache": "enabled"
}
```

#### System Status
```http
GET /status
```

**Response:**
```json
{
  "system": {
    "php_version": "8.1.0",
    "wordpress_version": "6.3.0",
    "plugin_version": "1.0.0",
    "memory_limit": "256M",
    "max_execution_time": 30
  },
  "database": {
    "version": "8.0.0",
    "tables": {
      "sales": 125,
      "bands": 10,
      "merchandise": 45
    }
  },
  "performance": {
    "response_time": "125ms",
    "memory_usage": "45MB",
    "cache_hit_rate": "92%"
  }
}
```

#### Clear Cache
```http
POST /cache/clear
Authorization: Bearer {token}
```

## 📊 Response Formats

### Pagination
All list endpoints support pagination:

```json
{
  "data": [],
  "meta": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 20
  },
  "links": {
    "first": "/endpoint?page=1",
    "last": "/endpoint?page=5",
    "prev": null,
    "next": "/endpoint?page=2"
  }
}
```

### Error Responses

#### Validation Error
```json
{
  "error": {
    "code": "validation_error",
    "message": "Invalid input data",
    "errors": {
      "email": ["The email must be a valid email address."],
      "price": ["The price must be a number."]
    },
    "data": {
      "status": 422
    }
  }
}
```

#### Authentication Error
```json
{
  "error": {
    "code": "rest_forbidden",
    "message": "Sorry, you are not allowed to do that.",
    "data": {
      "status": 403
    }
  }
}
```

#### Not Found Error
```json
{
  "error": {
    "code": "rest_not_found",
    "message": "Resource not found",
    "data": {
      "status": 404
    }
  }
}
```

## 🔐 Permission Matrix

| Endpoint | User Role | Required Capability |
|----------|-----------|---------------------|
| `GET /bands` | All | `read` |
| `POST /bands` | Editor+ | `edit_posts` |
| `PUT/DELETE /bands` | Admin | `manage_options` |
| `GET /sales` | Contributor+ | `read` |
| `POST /sales` | Author+ | `edit_posts` |
| `PUT/DELETE /sales` | Editor+ | `delete_posts` |
| `GET /reports` | Editor+ | `edit_posts` |
| Financial endpoints | Admin | `manage_options` |

## ⚡ Performance Tips

1. **Use field selection** to reduce response size:
   ```http
   GET /bands?fields=id,name,contact_email
   ```

2. **Use pagination** for large datasets
3. **Cache responses** when appropriate
4. **Use compression** for large responses
5. **Batch operations** when possible

## 🚦 Rate Limits

- **General**: 100 requests/minute
- **Burst**: 20 requests/second
- **Authentication**: 10 failed attempts/hour

Headers:
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1627842000
Retry-After: 60
```

---

*This reference is automatically generated from the OpenAPI specification. For the most up-to-date information, always refer to the live API documentation.*