# Merchandise Sales Plugin - API Documentation

## 1. Overview

This document describes the API endpoints and hooks available in the Merchandise Sales Plugin. The plugin provides both REST API endpoints for external integrations and WordPress hooks for internal extensions.

## 2. REST API Endpoints

All REST API endpoints are prefixed with `/wp-json/msp/v1/`.

### 2.1 Authentication

API requests require authentication using one of the following methods:
- WordPress cookie authentication (for browser-based requests)
- OAuth 1.0a authentication
- Application passwords

### 2.2 Bands API

#### 2.2.1 Get All Bands
- **Endpoint**: `GET /bands`
- **Parameters**:
  - `per_page` (optional): Number of items per page (default: 10)
  - `page` (optional): Page number (default: 1)
- **Response**: Array of band objects
- **Example**:
  ```json
  [
    {
      "id": 123,
      "title": "The Rockers",
      "description": "Rock band from Seattle",
      "contact_name": "John Doe",
      "contact_email": "john@example.com",
      "contact_phone": "555-1234",
      "website": "https://example.com",
      "social_media": {
        "facebook": "https://facebook.com/therockers",
        "instagram": "https://instagram.com/therockers"
      }
    }
  ]
  ```

#### 2.2.2 Get Band
- **Endpoint**: `GET /bands/{id}`
- **Parameters**:
  - `id` (required): Band ID
- **Response**: Band object
- **Example**: Same as above

#### 2.2.3 Create Band
- **Endpoint**: `POST /bands`
- **Parameters**:
  - `title` (required): Band name
  - `description` (optional): Band description
  - `contact_name` (optional): Contact person's name
  - `contact_email` (optional): Contact email
  - `contact_phone` (optional): Contact phone
  - `website` (optional): Band website
  - `social_media` (optional): Social media links
- **Response**: Created band object

#### 2.2.4 Update Band
- **Endpoint**: `PUT /bands/{id}`
- **Parameters**: Same as Create Band
- **Response**: Updated band object

#### 2.2.5 Delete Band
- **Endpoint**: `DELETE /bands/{id}`
- **Parameters**:
  - `id` (required): Band ID
- **Response**: Success message

### 2.3 Tours API

#### 2.3.1 Get All Tours
- **Endpoint**: `GET /tours`
- **Parameters**:
  - `per_page` (optional): Number of items per page (default: 10)
  - `page` (optional): Page number (default: 1)
  - `band_id` (optional): Filter by band ID
- **Response**: Array of tour objects

#### 2.3.2 Get Tour
- **Endpoint**: `GET /tours/{id}`
- **Parameters**:
  - `id` (required): Tour ID
- **Response**: Tour object

#### 2.3.3 Create Tour
- **Endpoint**: `POST /tours`
- **Parameters**:
  - `title` (required): Tour name
  - `description` (optional): Tour description
  - `band_id` (required): Associated band ID
  - `start_date` (required): Tour start date
  - `end_date` (required): Tour end date
  - `status` (optional): Tour status
  - `notes` (optional): Additional notes
- **Response**: Created tour object

#### 2.3.4 Update Tour
- **Endpoint**: `PUT /tours/{id}`
- **Parameters**: Same as Create Tour
- **Response**: Updated tour object

#### 2.3.5 Delete Tour
- **Endpoint**: `DELETE /tours/{id}`
- **Parameters**:
  - `id` (required): Tour ID
- **Response**: Success message

### 2.4 Shows API

#### 2.4.1 Get All Shows
- **Endpoint**: `GET /shows`
- **Parameters**:
  - `per_page` (optional): Number of items per page (default: 10)
  - `page` (optional): Page number (default: 1)
  - `tour_id` (optional): Filter by tour ID
- **Response**: Array of show objects

#### 2.4.2 Get Show
- **Endpoint**: `GET /shows/{id}`
- **Parameters**:
  - `id` (required): Show ID
- **Response**: Show object

#### 2.4.3 Create Show
- **Endpoint**: `POST /shows`
- **Parameters**:
  - `title` (required): Show name
  - `tour_id` (required): Associated tour ID
  - `date` (required): Show date and time
  - `venue_name` (required): Venue name
  - `venue_address` (optional): Venue address
  - `venue_city` (optional): Venue city
  - `venue_state` (optional): Venue state/province
  - `venue_country` (optional): Venue country
  - `venue_postal_code` (optional): Venue postal code
  - `venue_contact` (optional): Venue contact information
  - `notes` (optional): Additional notes
- **Response**: Created show object

#### 2.4.4 Update Show
- **Endpoint**: `PUT /shows/{id}`
- **Parameters**: Same as Create Show
- **Response**: Updated show object

#### 2.4.5 Delete Show
- **Endpoint**: `DELETE /shows/{id}`
- **Parameters**:
  - `id` (required): Show ID
- **Response**: Success message

### 2.5 Merchandise API

#### 2.5.1 Get All Merchandise
- **Endpoint**: `GET /merchandise`
- **Parameters**:
  - `per_page` (optional): Number of items per page (default: 10)
  - `page` (optional): Page number (default: 1)
  - `band_id` (optional): Filter by band ID
- **Response**: Array of merchandise objects

#### 2.5.2 Get Merchandise Item
- **Endpoint**: `GET /merchandise/{id}`
- **Parameters**:
  - `id` (required): Merchandise ID
- **Response**: Merchandise object

#### 2.5.3 Create Merchandise Item
- **Endpoint**: `POST /merchandise`
- **Parameters**:
  - `title` (required): Item name
  - `description` (optional): Item description
  - `band_id` (required): Associated band ID
  - `sku` (optional): Stock keeping unit
  - `price` (required): Price
  - `size` (optional): Size
  - `color` (optional): Color
  - `stock` (required): Current stock level
  - `low_stock_threshold` (optional): Low stock alert threshold
  - `supplier` (optional): Supplier information
  - `cost` (optional): Cost per unit
  - `category` (optional): Category
  - `active` (optional): Whether item is active
- **Response**: Created merchandise object

#### 2.5.4 Update Merchandise Item
- **Endpoint**: `PUT /merchandise/{id}`
- **Parameters**: Same as Create Merchandise Item
- **Response**: Updated merchandise object

#### 2.5.5 Delete Merchandise Item
- **Endpoint**: `DELETE /merchandise/{id}`
- **Parameters**:
  - `id` (required): Merchandise ID
- **Response**: Success message

### 2.6 Sales API

#### 2.6.1 Get All Sales
- **Endpoint**: `GET /sales`
- **Parameters**:
  - `per_page` (optional): Number of items per page (default: 10)
  - `page` (optional): Page number (default: 1)
  - `band_id` (optional): Filter by band ID
  - `show_id` (optional): Filter by show ID
  - `sales_page_id` (optional): Filter by sales page ID
  - `start_date` (optional): Filter by start date
  - `end_date` (optional): Filter by end date
- **Response**: Array of sale objects

#### 2.6.2 Get Sale
- **Endpoint**: `GET /sales/{id}`
- **Parameters**:
  - `id` (required): Sale ID
- **Response**: Sale object

#### 2.6.3 Create Sale
- **Endpoint**: `POST /sales`
- **Parameters**:
  - `merchandise_id` (required): Merchandise ID
  - `quantity` (required): Quantity sold
  - `price` (optional): Override price (if different from merchandise price)
  - `payment_type` (required): Payment method
  - `show_id` (optional): Associated show ID
  - `sales_page_id` (optional): Associated sales page ID
  - `band_id` (required): Associated band ID
  - `notes` (optional): Additional notes
- **Response**: Created sale object

#### 2.6.4 Update Sale
- **Endpoint**: `PUT /sales/{id}`
- **Parameters**: Same as Create Sale
- **Response**: Updated sale object

#### 2.6.5 Delete Sale
- **Endpoint**: `DELETE /sales/{id}`
- **Parameters**:
  - `id` (required): Sale ID
- **Response**: Success message

### 2.7 Sales Pages API

#### 2.7.1 Get All Sales Pages
- **Endpoint**: `GET /sales-pages`
- **Parameters**:
  - `per_page` (optional): Number of items per page (default: 10)
  - `page` (optional): Page number (default: 1)
  - `band_id` (optional): Filter by band ID
  - `show_id` (optional): Filter by show ID
  - `status` (optional): Filter by status
- **Response**: Array of sales page objects

#### 2.7.2 Get Sales Page
- **Endpoint**: `GET /sales-pages/{id}`
- **Parameters**:
  - `id` (required): Sales page ID
- **Response**: Sales page object

#### 2.7.3 Create Sales Page
- **Endpoint**: `POST /sales-pages`
- **Parameters**:
  - `title` (required): Page title
  - `show_id` (required): Associated show ID
  - `band_id` (required): Associated band ID
  - `access_code` (optional): Access code
  - `status` (optional): Status
  - `expiry_date` (optional): Expiry date
  - `merchandise` (optional): Associated merchandise IDs
- **Response**: Created sales page object

#### 2.7.4 Update Sales Page
- **Endpoint**: `PUT /sales-pages/{id}`
- **Parameters**: Same as Create Sales Page
- **Response**: Updated sales page object

#### 2.7.5 Delete Sales Page
- **Endpoint**: `DELETE /sales-pages/{id}`
- **Parameters**:
  - `id` (required): Sales page ID
- **Response**: Success message

### 2.8 Reports API

#### 2.8.1 Get Sales Report
- **Endpoint**: `GET /reports/sales`
- **Parameters**:
  - `band_id` (optional): Filter by band ID
  - `tour_id` (optional): Filter by tour ID
  - `show_id` (optional): Filter by show ID
  - `start_date` (optional): Filter by start date
  - `end_date` (optional): Filter by end date
  - `group_by` (optional): Group by (day, week, month, merchandise)
- **Response**: Sales report data

#### 2.8.2 Get Inventory Report
- **Endpoint**: `GET /reports/inventory`
- **Parameters**:
  - `band_id` (optional): Filter by band ID
  - `low_stock_only` (optional): Show only low stock items
- **Response**: Inventory report data

## 3. WordPress Hooks

### 3.1 Actions

#### 3.1.1 Band Actions
- `msp_after_band_save`: Triggered after a band is saved
  - Parameters: `$band_id`, `$band_data`
- `msp_before_band_delete`: Triggered before a band is deleted
  - Parameters: `$band_id`
- `msp_after_band_delete`: Triggered after a band is deleted
  - Parameters: `$band_id`

#### 3.1.2 Tour Actions
- `msp_after_tour_save`: Triggered after a tour is saved
  - Parameters: `$tour_id`, `$tour_data`
- `msp_before_tour_delete`: Triggered before a tour is deleted
  - Parameters: `$tour_id`
- `msp_after_tour_delete`: Triggered after a tour is deleted
  - Parameters: `$tour_id`

#### 3.1.3 Show Actions
- `msp_after_show_save`: Triggered after a show is saved
  - Parameters: `$show_id`, `$show_data`
- `msp_before_show_delete`: Triggered before a show is deleted
  - Parameters: `$show_id`
- `msp_after_show_delete`: Triggered after a show is deleted
  - Parameters: `$show_id`

#### 3.1.4 Merchandise Actions
- `msp_after_merchandise_save`: Triggered after merchandise is saved
  - Parameters: `$merchandise_id`, `$merchandise_data`
- `msp_before_merchandise_delete`: Triggered before merchandise is deleted
  - Parameters: `$merchandise_id`
- `msp_after_merchandise_delete`: Triggered after merchandise is deleted
  - Parameters: `$merchandise_id`
- `msp_stock_updated`: Triggered when stock is updated
  - Parameters: `$merchandise_id`, `$old_stock`, `$new_stock`, `$reason`
- `msp_low_stock_alert`: Triggered when stock falls below threshold
  - Parameters: `$merchandise_id`, `$current_stock`, `$threshold`

#### 3.1.5 Sale Actions
- `msp_before_sale_recorded`: Triggered before a sale is recorded
  - Parameters: `$sale_data`
- `msp_after_sale_recorded`: Triggered after a sale is recorded
  - Parameters: `$sale_id`, `$sale_data`
- `msp_before_sale_delete`: Triggered before a sale is deleted
  - Parameters: `$sale_id`
- `msp_after_sale_delete`: Triggered after a sale is deleted
  - Parameters: `$sale_id`

#### 3.1.6 Sales Page Actions
- `msp_after_sales_page_save`: Triggered after a sales page is saved
  - Parameters: `$sales_page_id`, `$sales_page_data`
- `msp_before_sales_page_delete`: Triggered before a sales page is deleted
  - Parameters: `$sales_page_id`
- `msp_after_sales_page_delete`: Triggered after a sales page is deleted
  - Parameters: `$sales_page_id`

### 3.2 Filters

#### 3.2.1 Band Filters
- `msp_band_data`: Filter band data before saving
  - Parameters: `$band_data`
- `msp_band_query_args`: Filter arguments for band queries
  - Parameters: `$query_args`
- `msp_band_display`: Filter band display data
  - Parameters: `$display_data`, `$band_id`

#### 3.2.2 Tour Filters
- `msp_tour_data`: Filter tour data before saving
  - Parameters: `$tour_data`
- `msp_tour_query_args`: Filter arguments for tour queries
  - Parameters: `$query_args`
- `msp_tour_display`: Filter tour display data
  - Parameters: `$display_data`, `$tour_id`

#### 3.2.3 Show Filters
- `msp_show_data`: Filter show data before saving
  - Parameters: `$show_data`
- `msp_show_query_args`: Filter arguments for show queries
  - Parameters: `$query_args`
- `msp_show_display`: Filter show display data
  - Parameters: `$display_data`, `$show_id`

#### 3.2.4 Merchandise Filters
- `msp_merchandise_data`: Filter merchandise data before saving
  - Parameters: `$merchandise_data`
- `msp_merchandise_query_args`: Filter arguments for merchandise queries
  - Parameters: `$query_args`
- `msp_merchandise_display`: Filter merchandise display data
  - Parameters: `$display_data`, `$merchandise_id`
- `msp_low_stock_threshold`: Filter low stock threshold
  - Parameters: `$threshold`, `$merchandise_id`

#### 3.2.5 Sale Filters
- `msp_sale_data`: Filter sale data before saving
  - Parameters: `$sale_data`
- `msp_sale_query_args`: Filter arguments for sale queries
  - Parameters: `$query_args`
- `msp_sale_display`: Filter sale display data
  - Parameters: `$display_data`, `$sale_id`

#### 3.2.6 Sales Page Filters
- `msp_sales_page_data`: Filter sales page data before saving
  - Parameters: `$sales_page_data`
- `msp_sales_page_query_args`: Filter arguments for sales page queries
  - Parameters: `$query_args`
- `msp_sales_page_display`: Filter sales page display data
  - Parameters: `$display_data`, `$sales_page_id`

#### 3.2.7 Report Filters
- `msp_sales_report_data`: Filter sales report data
  - Parameters: `$report_data`, `$args`
- `msp_inventory_report_data`: Filter inventory report data
  - Parameters: `$report_data`, `$args`

## 4. Usage Examples

### 4.1 REST API Example

```javascript
// Example: Get all bands
fetch('/wp-json/msp/v1/bands', {
  method: 'GET',
  headers: {
    'X-WP-Nonce': wpApiSettings.nonce,
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));

// Example: Create a new merchandise item
fetch('/wp-json/msp/v1/merchandise', {
  method: 'POST',
  headers: {
    'X-WP-Nonce': wpApiSettings.nonce,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: 'T-Shirt',
    description: 'Band logo t-shirt',
    band_id: 123,
    price: 25.00,
    stock: 100,
    size: 'M',
    color: 'Black'
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

### 4.2 WordPress Hooks Example

```php
// Example: Add a custom field to merchandise
add_filter('msp_merchandise_data', function($merchandise_data) {
    // Add a custom field
    if (isset($_POST['custom_field'])) {
        $merchandise_data['custom_field'] = sanitize_text_field($_POST['custom_field']);
    }
    return $merchandise_data;
});

// Example: Log sales
add_action('msp_after_sale_recorded', function($sale_id, $sale_data) {
    // Log the sale
    error_log("Sale recorded: " . $sale_id);
    error_log("Merchandise: " . $sale_data['merchandise_id']);
    error_log("Quantity: " . $sale_data['quantity']);
    error_log("Price: " . $sale_data['price']);
}, 10, 2);

// Example: Modify low stock threshold
add_filter('msp_low_stock_threshold', function($threshold, $merchandise_id) {
    // Get merchandise category
    $category = get_post_meta($merchandise_id, '_msp_merchandise_category', true);
    
    // Adjust threshold based on category
    if ($category === 'T-Shirts') {
        return 10; // Higher threshold for t-shirts
    } elseif ($category === 'Posters') {
        return 3; // Lower threshold for posters
    }
    
    return $threshold;
}, 10, 2);
```

## 5. Error Handling

All API endpoints return standard HTTP status codes:

- 200: Success
- 400: Bad request (invalid parameters)
- 401: Unauthorized (authentication required)
- 403: Forbidden (insufficient permissions)
- 404: Not found
- 500: Server error

Error responses include a JSON object with an error message:

```json
{
  "code": "error_code",
  "message": "Human-readable error message",
  "data": {
    "status": 400
  }
}
```

## 6. Rate Limiting

API requests are rate-limited to prevent abuse:

- 100 requests per minute per IP address
- 1000 requests per hour per IP address

Rate limit headers are included in API responses:

- `X-RateLimit-Limit`: Maximum number of requests allowed
- `X-RateLimit-Remaining`: Number of requests remaining
- `X-RateLimit-Reset`: Time when the rate limit resets (Unix timestamp)

## 7. Versioning

The API uses versioning in the URL path (`/wp-json/msp/v1/`). Future versions will use incremented version numbers (`v2`, `v3`, etc.).

## 8. Conclusion

This API documentation provides a comprehensive guide to the endpoints and hooks available in the Merchandise Sales Plugin. Developers can use these APIs to extend the plugin's functionality or integrate it with other systems.