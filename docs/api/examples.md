# API Examples

## 🚀 Quick Start Examples

### 1. Authentication Example

#### PHP Example
```php
<?php
$api_url = 'https://yoursite.com/wp-json/msp/v1/';
$username = 'your_username';
$password = 'your_password';

// Get authentication token
$response = wp_remote_post($api_url . 'auth/token', [
    'headers' => [
        'Content-Type' => 'application/json',
    ],
    'body' => json_encode([
        'username' => $username,
        'password' => $password
    ])
]);

$data = json_decode(wp_remote_retrieve_body($response), true);
$token = $data['token'];

// Use token for subsequent requests
$bands_response = wp_remote_get($api_url . 'bands', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ]
]);

$bands = json_decode(wp_remote_retrieve_body($bands_response), true);
print_r($bands);
```

#### JavaScript Example
```javascript
const API_BASE = 'https://yoursite.com/wp-json/msp/v1/';

async function authenticate(username, password) {
    const response = await fetch(API_BASE + 'auth/token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password })
    });
    
    const data = await response.json();
    return data.token;
}

async function getBands(token) {
    const response = await fetch(API_BASE + 'bands', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        }
    });
    
    return await response.json();
}

// Usage
const token = await authenticate('username', 'password');
const bands = await getBands(token);
console.log(bands);
```

#### Python Example
```python
import requests
import json

API_BASE = 'https://yoursite.com/wp-json/msp/v1/'

# Authenticate
response = requests.post(API_BASE + 'auth/token', 
    json={'username': 'your_username', 'password': 'your_password'},
    headers={'Content-Type': 'application/json'}
)

token = response.json()['token']

# Get bands
response = requests.get(API_BASE + 'bands',
    headers={
        'Authorization': f'Bearer {token}',
        'Content-Type': 'application/json'
    }
)

bands = response.json()
print(bands)
```

### 2. Band Management Examples

#### Create a New Band
```javascript
async function createBand(token, bandData) {
    const response = await fetch(API_BASE + 'bands', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(bandData)
    });
    
    return await response.json();
}

const newBand = {
    name: 'Metal Titans',
    contact_email: 'metal@titans.com',
    contact_phone: '+1234567890',
    description: 'An awesome metal band',
    genres: ['Metal', 'Rock'],
    social_media: {
        facebook: 'https://facebook.com/metaltitans',
        instagram: 'https://instagram.com/metaltitans'
    }
};

const band = await createBand(token, newBand);
console.log('Created band:', band);
```

#### Update Band Information
```php
$band_id = 123;
$update_data = [
    'name' => 'Updated Band Name',
    'contact_email' => 'updated@band.com',
    'description' => 'New and improved band description'
];

$response = wp_remote_post($api_url . 'bands/' . $band_id, [
    'method' => 'PUT',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ],
    'body' => json_encode($update_data)
]);

$result = json_decode(wp_remote_retrieve_body($response), true);
```

#### Search Bands
```python
# Search for bands containing "metal"
params = {
    'search': 'metal',
    'per_page': 10,
    'orderby': 'name',
    'order': 'asc'
}

response = requests.get(API_BASE + 'bands', 
    headers=headers,
    params=params
)

bands = response.json()
for band in bands['data']:
    print(f"{band['name']} - {band['contact_email']}")
```

### 3. Merchandise Management Examples

#### Add New Merchandise Item
```javascript
async function addMerchandise(token, itemData) {
    const response = await fetch(API_BASE + 'merchandise', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(itemData)
    });
    
    return await response.json();
}

const newItem = {
    name: 'Official Band T-Shirt',
    description: 'High-quality cotton t-shirt with band logo',
    price: 25.00,
    cost_price: 10.00,
    stock_quantity: 100,
    low_stock_threshold: 10,
    band_id: 123,
    categories: ['Clothing', 'T-Shirts'],
    variations: [
        {
            size: 'S',
            color: 'Black',
            stock: 25,
            sku: 'BAND-TS-S-BK'
        },
        {
            size: 'M', 
            color: 'Black',
            stock: 30,
            sku: 'BAND-TS-M-BK'
        }
    ]
};

const item = await addMerchandise(token, newItem);
console.log('Added merchandise:', item);
```

#### Update Stock Levels
```php
$item_id = 456;
$stock_update = [
    'quantity' => 5,
    'reason' => 'restock',
    'notes' => 'Received new shipment from supplier',
    'user_id' => 1
];

$response = wp_remote_post($api_url . 'merchandise/' . $item_id . '/stock', [
    'method' => 'PATCH',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ],
    'body' => json_encode($stock_update)
]);

$result = json_decode(wp_remote_retrieve_body($response), true);
```

#### Get Low Stock Items
```python
# Get items with low stock
params = {
    'low_stock': 'true',
    'per_page': 50
}

response = requests.get(API_BASE + 'merchandise', 
    headers=headers,
    params=params
)

low_stock_items = response.json()

for item in low_stock_items['data']:
    print(f"{item['name']}: {item['stock_quantity']} left (threshold: {item['low_stock_threshold']})")
```

### 4. Sales Management Examples

#### Record a Sale
```javascript
async function recordSale(token, saleData) {
    const response = await fetch(API_BASE + 'sales', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(saleData)
    });
    
    return await response.json();
}

const newSale = {
    band_id: 123,
    items: [
        {
            merchandise_id: 456,
            quantity: 2,
            unit_price: 25.00
        },
        {
            merchandise_id: 789,
            quantity: 1,
            unit_price: 15.00
        }
    ],
    payment_type: 'card',
    payment_details: {
        card_last4: '4242',
        card_brand: 'visa',
        transaction_id: 'ch_123456789'
    },
    customer_info: {
        name: 'John Doe',
        email: 'john@example.com'
    },
    tax_amount: 8.25,
    notes: 'Sold at concert venue'
};

const sale = await recordSale(token, newSale);
console.log('Recorded sale:', sale);
```

#### Get Sales Report
```php
$report_params = [
    'band_id' => 123,
    'date_from' => '2024-01-01',
    'date_to' => '2024-01-31',
    'group_by' => 'day'
];

$response = wp_remote_get($api_url . 'reports/sales?' . http_build_query($report_params), [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ]
]);

$report = json_decode(wp_remote_retrieve_body($response), true);

// Process report data
echo "Total Sales: $" . $report['data']['total_sales'] . "\n";
echo "Total Items: " . $report['data']['total_items'] . "\n";

foreach ($report['data']['by_date'] as $daily_sales) {
    echo $daily_sales['date'] . ": $" . $daily_sales['sales'] . "\n";
}
```

#### Process a Refund
```python
refund_data = {
    'amount': 25.00,
    'reason': 'customer_return',
    'items': [
        {
            'merchandise_id': 456,
            'quantity': 1,
            'refund_amount': 25.00
        }
    ],
    'notes': 'Customer returned item due to wrong size',
    'restock_items': True
}

response = requests.post(API_BASE + f'sales/{sale_id}/refund',
    headers=headers,
    json=refund_data
)

refund = response.json()
print(f'Processed refund: {refund}')
```

### 5. Webhook Examples

#### Create a Webhook
```javascript
async function createWebhook(token, webhookData) {
    const response = await fetch(API_BASE + 'webhooks', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(webhookData)
    });
    
    return await response.json();
}

const webhookConfig = {
    url: 'https://your-app.com/webhooks/sales',
    events: ['sale.created', 'sale.updated', 'stock.low'],
    secret: 'your-webhook-secret-key',
    description: 'Sales and stock notifications',
    status: 'active'
};

const webhook = await createWebhook(token, webhookConfig);
console.log('Created webhook:', webhook);
```

#### Webhook Receiver Example (Node.js)
```javascript
const express = require('express');
const crypto = require('crypto');

const app = express();
app.use(express.json());

const WEBHOOK_SECRET = 'your-webhook-secret-key';

app.post('/webhooks/sales', (req, res) => {
    const signature = req.headers['x-msp-signature'];
    const payload = JSON.stringify(req.body);
    
    // Verify signature
    const expectedSignature = crypto
        .createHmac('sha256', WEBHOOK_SECRET)
        .update(payload)
        .digest('hex');
    
    if (signature !== expectedSignature) {
        return res.status(401).send('Invalid signature');
    }
    
    const event = req.body.event;
    const data = req.body.data;
    
    switch (event) {
        case 'sale.created':
            console.log('New sale:', data);
            // Process new sale
            break;
        case 'stock.low':
            console.log('Low stock alert:', data);
            // Send notification
            break;
    }
    
    res.status(200).send('Webhook received');
});

app.listen(3000, () => {
    console.log('Webhook server listening on port 3000');
});
```

### 6. Error Handling Examples

#### Comprehensive Error Handling
```javascript
async function apiCall(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new ApiError(
                response.status,
                errorData.error?.message || response.statusText,
                errorData.error?.code,
                errorData.error?.errors
            );
        }
        
        return await response.json();
    } catch (error) {
        if (error instanceof ApiError) {
            throw error;
        }
        throw new ApiError(0, 'Network error', 'network_error');
    }
}

class ApiError extends Error {
    constructor(status, message, code, errors = null) {
        super(message);
        this.status = status;
        this.code = code;
        this.errors = errors;
        this.name = 'ApiError';
    }
}

// Usage with error handling
try {
    const bands = await apiCall(API_BASE + 'bands');
    console.log('Bands:', bands);
} catch (error) {
    if (error instanceof ApiError) {
        console.error(`API Error (${error.status}):`, error.message);
        if (error.errors) {
            console.error('Validation errors:', error.errors);
        }
    } else {
        console.error('Unexpected error:', error);
    }
}
```

#### PHP Error Handling
```php
function make_api_request($url, $args = []) {
    $defaults = [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ]
    ];
    
    $args = wp_parse_args($args, $defaults);
    $response = wp_remote_request($url, $args);
    
    if (is_wp_error($response)) {
        throw new Exception('API request failed: ' . $response->get_error_message());
    }
    
    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if ($status_code >= 400) {
        $error_message = $data['error']['message'] ?? 'Unknown error';
        $error_code = $data['error']['code'] ?? 'unknown_error';
        throw new ApiException($error_message, $status_code, $error_code);
    }
    
    return $data;
}

class ApiException extends Exception {
    public $status_code;
    public $error_code;
    
    public function __construct($message, $status_code, $error_code) {
        parent::__construct($message);
        $this->status_code = $status_code;
        $this->error_code = $error_code;
    }
}

// Usage
try {
    $bands = make_api_request($api_url . 'bands');
    print_r($bands);
} catch (ApiException $e) {
    echo "API Error ({$e->status_code}): {$e->getMessage()}\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
```

### 7. Batch Operations Example

#### Batch Stock Update
```javascript
async function batchUpdateStock(token, updates) {
    const responses = await Promise.all(
        updates.map(update => 
            fetch(API_BASE + `merchandise/${update.item_id}/stock`, {
                method: 'PATCH',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    quantity: update.quantity,
                    reason: update.reason,
                    notes: update.notes
                })
            })
        )
    );
    
    return await Promise.all(responses.map(r => r.json()));
}

const stockUpdates = [
    { item_id: 456, quantity: 10, reason: 'restock', notes: 'New shipment' },
    { item_id: 789, quantity: -2, reason: 'adjustment', notes: 'Found damaged' },
    { item_id: 101, quantity: 5, reason: 'restock', notes: 'Supplier delivery' }
];

const results = await batchUpdateStock(token, stockUpdates);
console.log('Batch update results:', results);
```

### 8. Real-time Dashboard Example

#### Live Sales Dashboard
```javascript
class SalesDashboard {
    constructor(token) {
        this.token = token;
        this.cache = new Map();
    }
    
    async getLiveData() {
        const [sales, stock, bands] = await Promise.all([
            this.getSalesData(),
            this.getStockData(),
            this.getBandsData()
        ]);
        
        return { sales, stock, bands };
    }
    
    async getSalesData() {
        const today = new Date().toISOString().split('T')[0];
        const response = await fetch(
            `${API_BASE}reports/sales?date_from=${today}&group_by=hour`,
            {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json'
                }
            }
        );
        
        return await response.json();
    }
    
    async getStockData() {
        const response = await fetch(
            `${API_BASE}reports/stock?low_stock_only=true`,
            {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json'
                }
            }
        );
        
        return await response.json();
    }
    
    async getBandsData() {
        const response = await fetch(API_BASE + 'bands?per_page=100', {
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'Content-Type': 'application/json'
            }
        });
        
        return await response.json();
    }
    
    startAutoRefresh(interval = 30000) {
        this.intervalId = setInterval(async () => {
            try {
                const data = await this.getLiveData();
                this.updateDashboard(data);
            } catch (error) {
                console.error('Auto-refresh error:', error);
            }
        }, interval);
    }
    
    stopAutoRefresh() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
    }
    
    updateDashboard(data) {
        // Update your UI with the new data
        console.log('Dashboard updated:', data);
    }
}

// Usage
const dashboard = new SalesDashboard(token);
dashboard.startAutoRefresh();

// Later...
// dashboard.stopAutoRefresh();
```

## 🎯 Best Practices

1. **Always handle errors** - Use try/catch blocks and proper error handling
2. **Implement retry logic** for transient failures
3. **Use pagination** for large datasets
4. **Cache responses** when appropriate to reduce API calls
5. **Validate input data** before sending to API
6. **Use appropriate timeouts** for network requests
7. **Implement rate limiting** on your side to respect API limits
8. **Keep tokens secure** and refresh them when needed
9. **Use webhooks** for real-time updates instead of polling
10. **Monitor API usage** and performance

---

*These examples demonstrate common use cases. For complete API documentation, refer to the [API Reference](../reference.md) and always test your implementation thoroughly.*