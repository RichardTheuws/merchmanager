# API Overview

## 🌐 REST API Architecture

The Merchandise Sales Plugin provides a comprehensive REST API for programmatic access to all functionality.

### Base URL
```
https://yoursite.com/wp-json/msp/v1/
```

### Authentication

#### WordPress Authentication
```http
GET /wp-json/msp/v1/bands
Authorization: Basic base64(username:password)
```

#### Application Passwords
```http
GET /wp-json/msp/v1/bands
Authorization: Basic base64(username:application_password)
```

#### OAuth 2.0 (Optional)
```http
GET /wp-json/msp/v1/bands
Authorization: Bearer {access_token}
```

### Response Format

All responses follow this structure:
```json
{
  "data": [
    // Array of resources
  ],
  "meta": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 20
  },
  "links": {
    "first": "/wp-json/msp/v1/bands?page=1",
    "last": "/wp-json/msp/v1/bands?page=5",
    "prev": null,
    "next": "/wp-json/msp/v1/bands?page=2"
  }
}
```

### Error Responses

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

### Common HTTP Status Codes

- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid parameters
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error

## 📋 API Endpoints

### Bands API

#### List Bands
```http
GET /bands
```

**Parameters:**
- `page` (integer): Page number
- `per_page` (integer): Items per page (max 100)
- `search` (string): Search term
- `orderby` (string): Field to order by
- `order` (string): asc/desc

**Response:**
```json
{
  "data": [
    {
      "id": 123,
      "name": "Band Name",
      "slug": "band-name",
      "contact_email": "band@example.com",
      "contact_phone": "+1234567890",
      "description": "Band description...",
      "website": "https://band.com",
      "genres": ["Rock", "Metal"],
      "social_media": {
        "facebook": "https://facebook.com/band",
        "instagram": "https://instagram.com/band"
      },
      "payment_details": {
        "bank_account": "1234567890",
        "paypal": "band@paypal.com"
      },
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ]
}
```

#### Get Band
```http
GET /bands/{id}
```

#### Create Band
```http
POST /bands
Content-Type: application/json

{
  "name": "New Band",
  "contact_email": "new@band.com",
  "description": "Band description"
}
```

#### Update Band
```http
PUT /bands/{id}
Content-Type: application/json

{
  "name": "Updated Band Name",
  "contact_email": "updated@band.com"
}
```

#### Delete Band
```http
DELETE /bands/{id}
```

### Merchandise API

#### List Merchandise
```http
GET /merchandise
```

**Parameters:**
- `band_id` (integer): Filter by band
- `category` (string): Filter by category
- `in_stock` (boolean): Only in-stock items
- `low_stock` (boolean): Only low-stock items

**Response:**
```json
{
  "data": [
    {
      "id": 456,
      "name": "T-Shirt",
      "description": "Band t-shirt description",
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
          "stock": 25
        }
      ],
      "images": [
        {
          "url": "https://example.com/image.jpg",
          "alt": "T-Shirt Front"
        }
      ],
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ]
}
```

#### Get Merchandise Item
```http
GET /merchandise/{id}
```

#### Create Merchandise Item
```http
POST /merchandise
Content-Type: application/json

{
  "name": "New Item",
  "price": 20.00,
  "band_id": 123,
  "stock_quantity": 50
}
```

#### Update Merchandise Item
```http
PUT /merchandise/{id}
Content-Type: application/json

{
  "price": 22.00,
  "stock_quantity": 45
}
```

#### Update Stock
```http
PATCH /merchandise/{id}/stock
Content-Type: application/json

{
  "quantity": 5,
  "reason": "restock",
  "notes": "Received new shipment"
}
```

#### Delete Merchandise Item
```http
DELETE /merchandise/{id}
```

### Sales API

#### List Sales
```http
GET /sales
```

**Parameters:**
- `band_id` (integer): Filter by band
- `date_from` (string): Start date (YYYY-MM-DD)
- `date_to` (string): End date (YYYY-MM-DD)
- `show_id` (integer): Filter by show

**Response:**
```json
{
  "data": [
    {
      "id": 789,
      "date": "2024-01-15T14:30:00Z",
      "band_id": 123,
      "show_id": 456,
      "sales_page_id": 789,
      "total_amount": 125.00,
      "tax_amount": 10.00,
      "payment_type": "cash",
      "items": [
        {
          "merchandise_id": 101,
          "name": "T-Shirt",
          "quantity": 2,
          "unit_price": 25.00,
          "total_price": 50.00
        },
        {
          "merchandise_id": 102,
          "name": "CD",
          "quantity": 3,
          "unit_price": 15.00,
          "total_price": 45.00
        }
      ],
      "customer_info": {
        "name": "John Doe",
        "email": "john@example.com"
      },
      "notes": "Customer requested receipt",
      "created_at": "2024-01-15T14:30:00Z"
    }
  ]
}
```

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
  "items": [
    {
      "merchandise_id": 101,
      "quantity": 2
    }
  ],
  "payment_type": "card",
  "customer_email": "customer@example.com"
}
```

#### Update Sale
```http
PUT /sales/{id}
Content-Type: application/json

{
  "notes": "Updated sale notes"
}
```

#### Process Refund
```http
POST /sales/{id}/refund
Content-Type: application/json

{
  "amount": 25.00,
  "reason": "Customer return",
  "items": [
    {
      "merchandise_id": 101,
      "quantity": 1
    }
  ]
}
```

#### Delete Sale
```http
DELETE /sales/{id}
```

### Reports API

#### Sales Report
```http
GET /reports/sales
```

**Parameters:**
- `band_id` (integer): Filter by band
- `date_from` (string): Start date (YYYY-MM-DD)
- `date_to` (string): End date (YYYY-MM-DD)
- `group_by` (string): day/week/month

**Response:**
```json
{
  "data": {
    "total_sales": 1250.00,
    "total_items": 45,
    "average_sale": 27.78,
    "by_date": [
      {
        "date": "2024-01-15",
        "sales": 250.00,
        "items": 10
      }
    ],
    "by_product": [
      {
        "product_id": 101,
        "name": "T-Shirt",
        "sales": 500.00,
        "items": 20
      }
    ],
    "by_payment": [
      {
        "type": "cash",
        "sales": 750.00,
        "count": 25
      }
    ]
  }
}
```

#### Stock Report
```http
GET /reports/stock
```

#### Financial Report
```http
GET /reports/financial
```

### Webhooks

#### Available Webhooks
- `sale.created`: When a new sale is recorded
- `sale.updated`: When a sale is updated
- `sale.refunded`: When a refund is processed
- `stock.low`: When stock falls below threshold
- `stock.updated`: When stock levels change
- `band.created`: When a new band is created

#### Webhook Payload
```json
{
  "event": "sale.created",
  "data": {
    "sale_id": 789,
    "band_id": 123,
    "total_amount": 125.00,
    "date": "2024-01-15T14:30:00Z"
  },
  "timestamp": "2024-01-15T14:30:05Z"
}
```

#### Register Webhook
```http
POST /webhooks
Content-Type: application/json

{
  "url": "https://your-app.com/webhooks/sales",
  "events": ["sale.created", "sale.updated"],
  "secret": "your-webhook-secret"
}
```

## 🔒 Rate Limiting

- **General Limit**: 100 requests per minute
- **Burst Limit**: 20 requests per second
- **Authentication**: 10 failed attempts per hour

Headers included in responses:
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1627842000
```

## 🛡️ Security

### Required Headers
```http
Content-Type: application/json
X-WP-Nonce: {nonce}  # For AJAX requests
```

### Permission Requirements
- **Read operations**: `read` capability
- **Write operations**: `edit_posts` capability
- **Delete operations**: `delete_posts` capability
- **Financial data**: `manage_options` capability

### Data Validation
- Input sanitization
- Type validation
- Range checking
- SQL injection prevention
- XSS protection

## 🧪 Testing

### Sandbox Environment
Use test mode for development:
```http
POST /sales
Content-Type: application/json
X-MSP-Test-Mode: true

{
  "band_id": 123,
  "items": [/* ... */]
}
```

### Error Simulation
Test error handling:
```http
GET /sales/invalid_id
```

## 📚 SDKs & Libraries

### Official Libraries
- **PHP SDK**: `composer require merchmanager/sdk`
- **JavaScript SDK**: `npm install @merchmanager/sdk`
- **Python SDK**: `pip install merchmanager`

### Community Libraries
- **Ruby Gem**: `gem install merchmanager`
- **Go Package**: `go get github.com/merchmanager/sdk`
- **.NET NuGet**: `Install-Package MerchManager.Sdk`

---

*For complete API documentation, see the [API Reference](../api/reference.md) and [Examples](../api/examples.md).*