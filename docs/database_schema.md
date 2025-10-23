# Merchandise Sales Plugin - Database Schema

## 1. Overview

This document details the database schema for the Merchandise Sales Plugin, including WordPress custom post types, custom database tables, and their relationships.

## 2. WordPress Custom Post Types

### 2.1 Band (msp_band)

#### 2.1.1 Post Type Definition
```php
register_post_type('msp_band', [
    'labels' => [
        'name' => __('Bands', 'merchmanager'),
        'singular_name' => __('Band', 'merchmanager'),
        // Other labels...
    ],
    'public' => true,
    'has_archive' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'menu_icon' => 'dashicons-groups',
    'show_in_rest' => true,
]);
```

#### 2.1.2 Meta Fields
| Meta Key | Description | Type | Default |
|----------|-------------|------|---------|
| `_msp_band_contact_name` | Contact person's name | string | '' |
| `_msp_band_contact_email` | Contact email | string | '' |
| `_msp_band_contact_phone` | Contact phone number | string | '' |
| `_msp_band_website` | Band website URL | string | '' |
| `_msp_band_social_media` | Social media links (serialized) | array | [] |

### 2.2 Tour (msp_tour)

#### 2.2.1 Post Type Definition
```php
register_post_type('msp_tour', [
    'labels' => [
        'name' => __('Tours', 'merchmanager'),
        'singular_name' => __('Tour', 'merchmanager'),
        // Other labels...
    ],
    'public' => true,
    'has_archive' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'menu_icon' => 'dashicons-calendar-alt',
    'show_in_rest' => true,
]);
```

#### 2.2.2 Meta Fields
| Meta Key | Description | Type | Default |
|----------|-------------|------|---------|
| `_msp_tour_band_id` | Associated band ID | integer | 0 |
| `_msp_tour_start_date` | Tour start date | date | '' |
| `_msp_tour_end_date` | Tour end date | date | '' |
| `_msp_tour_status` | Tour status (upcoming, active, completed) | string | 'upcoming' |
| `_msp_tour_notes` | Additional notes | string | '' |

### 2.3 Show (msp_show)

#### 2.3.1 Post Type Definition
```php
register_post_type('msp_show', [
    'labels' => [
        'name' => __('Shows', 'merchmanager'),
        'singular_name' => __('Show', 'merchmanager'),
        // Other labels...
    ],
    'public' => true,
    'has_archive' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'menu_icon' => 'dashicons-tickets-alt',
    'show_in_rest' => true,
]);
```

#### 2.3.2 Meta Fields
| Meta Key | Description | Type | Default |
|----------|-------------|------|---------|
| `_msp_show_tour_id` | Associated tour ID | integer | 0 |
| `_msp_show_date` | Show date and time | datetime | '' |
| `_msp_show_venue_name` | Venue name | string | '' |
| `_msp_show_venue_address` | Venue address | string | '' |
| `_msp_show_venue_city` | Venue city | string | '' |
| `_msp_show_venue_state` | Venue state/province | string | '' |
| `_msp_show_venue_country` | Venue country | string | '' |
| `_msp_show_venue_postal_code` | Venue postal code | string | '' |
| `_msp_show_venue_contact` | Venue contact information | string | '' |
| `_msp_show_notes` | Additional notes | string | '' |

### 2.4 Merchandise (msp_merchandise)

#### 2.4.1 Post Type Definition
```php
register_post_type('msp_merchandise', [
    'labels' => [
        'name' => __('Merchandise', 'merchmanager'),
        'singular_name' => __('Merchandise Item', 'merchmanager'),
        // Other labels...
    ],
    'public' => true,
    'has_archive' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'menu_icon' => 'dashicons-cart',
    'show_in_rest' => true,
]);
```

#### 2.4.2 Meta Fields
| Meta Key | Description | Type | Default |
|----------|-------------|------|---------|
| `_msp_merchandise_band_id` | Associated band ID | integer | 0 |
| `_msp_merchandise_sku` | Stock keeping unit | string | '' |
| `_msp_merchandise_price` | Price | decimal | 0.00 |
| `_msp_merchandise_size` | Size (if applicable) | string | '' |
| `_msp_merchandise_color` | Color (if applicable) | string | '' |
| `_msp_merchandise_stock` | Current stock level | integer | 0 |
| `_msp_merchandise_low_stock_threshold` | Low stock alert threshold | integer | 5 |
| `_msp_merchandise_supplier` | Supplier information | string | '' |
| `_msp_merchandise_cost` | Cost per unit | decimal | 0.00 |
| `_msp_merchandise_category` | Category | string | '' |
| `_msp_merchandise_active` | Whether item is active | boolean | true |

### 2.5 Sales Page (msp_sales_page)

#### 2.5.1 Post Type Definition
```php
register_post_type('msp_sales_page', [
    'labels' => [
        'name' => __('Sales Pages', 'merchmanager'),
        'singular_name' => __('Sales Page', 'merchmanager'),
        // Other labels...
    ],
    'public' => true,
    'has_archive' => false,
    'supports' => ['title'],
    'menu_icon' => 'dashicons-store',
    'show_in_rest' => true,
]);
```

#### 2.5.2 Meta Fields
| Meta Key | Description | Type | Default |
|----------|-------------|------|---------|
| `_msp_sales_page_show_id` | Associated show ID | integer | 0 |
| `_msp_sales_page_band_id` | Associated band ID | integer | 0 |
| `_msp_sales_page_access_code` | Access code for the page | string | '' |
| `_msp_sales_page_status` | Status (active, inactive) | string | 'active' |
| `_msp_sales_page_expiry_date` | Expiry date | date | '' |
| `_msp_sales_page_merchandise` | Associated merchandise IDs (serialized) | array | [] |

## 3. Custom Database Tables

### 3.1 Sales Table (wp_msp_sales)

#### 3.1.1 Table Schema
```sql
CREATE TABLE `wp_msp_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `merchandise_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_type` varchar(50) NOT NULL DEFAULT 'cash',
  `show_id` bigint(20) unsigned DEFAULT NULL,
  `sales_page_id` bigint(20) unsigned DEFAULT NULL,
  `band_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `merchandise_id` (`merchandise_id`),
  KEY `show_id` (`show_id`),
  KEY `sales_page_id` (`sales_page_id`),
  KEY `band_id` (`band_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 3.1.2 Fields Description
| Field | Description | Type | Constraints |
|-------|-------------|------|------------|
| `id` | Primary key | bigint(20) | NOT NULL, AUTO_INCREMENT |
| `date` | Sale date and time | datetime | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `merchandise_id` | ID of sold merchandise | bigint(20) | NOT NULL |
| `quantity` | Quantity sold | int(11) | NOT NULL, DEFAULT 1 |
| `price` | Sale price per unit | decimal(10,2) | NOT NULL, DEFAULT 0.00 |
| `payment_type` | Payment method (cash, card, etc.) | varchar(50) | NOT NULL, DEFAULT 'cash' |
| `show_id` | Associated show ID | bigint(20) | DEFAULT NULL |
| `sales_page_id` | Associated sales page ID | bigint(20) | DEFAULT NULL |
| `band_id` | Associated band ID | bigint(20) | NOT NULL |
| `user_id` | User who recorded the sale | bigint(20) | DEFAULT NULL |
| `notes` | Additional notes | text | DEFAULT NULL |
| `created_at` | Record creation timestamp | datetime | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `updated_at` | Record update timestamp | datetime | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP |

### 3.2 Stock Alerts Table (wp_msp_stock_alerts)

#### 3.2.1 Table Schema
```sql
CREATE TABLE `wp_msp_stock_alerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `merchandise_id` bigint(20) unsigned NOT NULL,
  `threshold` int(11) NOT NULL DEFAULT '5',
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `merchandise_id` (`merchandise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 3.2.2 Fields Description
| Field | Description | Type | Constraints |
|-------|-------------|------|------------|
| `id` | Primary key | bigint(20) | NOT NULL, AUTO_INCREMENT |
| `merchandise_id` | Associated merchandise ID | bigint(20) | NOT NULL |
| `threshold` | Stock level threshold | int(11) | NOT NULL, DEFAULT 5 |
| `status` | Alert status (active, dismissed) | varchar(50) | NOT NULL, DEFAULT 'active' |
| `created_at` | Record creation timestamp | datetime | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `updated_at` | Record update timestamp | datetime | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP |

### 3.3 Stock Log Table (wp_msp_stock_log)

#### 3.3.1 Table Schema
```sql
CREATE TABLE `wp_msp_stock_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `merchandise_id` bigint(20) unsigned NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `change_reason` varchar(100) NOT NULL DEFAULT 'manual',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `merchandise_id` (`merchandise_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 3.3.2 Fields Description
| Field | Description | Type | Constraints |
|-------|-------------|------|------------|
| `id` | Primary key | bigint(20) | NOT NULL, AUTO_INCREMENT |
| `merchandise_id` | Associated merchandise ID | bigint(20) | NOT NULL |
| `previous_stock` | Previous stock level | int(11) | NOT NULL |
| `new_stock` | New stock level | int(11) | NOT NULL |
| `change_reason` | Reason for stock change | varchar(100) | NOT NULL, DEFAULT 'manual' |
| `user_id` | User who made the change | bigint(20) | DEFAULT NULL |
| `notes` | Additional notes | text | DEFAULT NULL |
| `created_at` | Record creation timestamp | datetime | NOT NULL, DEFAULT CURRENT_TIMESTAMP |

## 4. Entity Relationships

### 4.1 Band Relationships
- A Band has many Tours
- A Band has many Merchandise Items
- A Band has many Sales Pages
- A Band has many Sales (through Merchandise)

### 4.2 Tour Relationships
- A Tour belongs to a Band
- A Tour has many Shows

### 4.3 Show Relationships
- A Show belongs to a Tour
- A Show has one Sales Page
- A Show has many Sales

### 4.4 Merchandise Relationships
- A Merchandise Item belongs to a Band
- A Merchandise Item has many Sales
- A Merchandise Item has many Stock Alerts
- A Merchandise Item has many Stock Logs

### 4.5 Sales Page Relationships
- A Sales Page belongs to a Show
- A Sales Page belongs to a Band
- A Sales Page has many Sales

## 5. Database Diagram

```
+-------------+       +-------------+       +-------------+
|   msp_band  |------>|  msp_tour   |------>|  msp_show   |
+-------------+       +-------------+       +-------------+
      |                                           |
      |                                           |
      v                                           v
+-------------+                           +----------------+
| msp_merch-  |                           | msp_sales_page |
| andise      |                           +----------------+
+-------------+                                   |
      |                                           |
      |                                           |
      v                                           v
+-------------+                           +-------------+
| wp_msp_stock|<--------------------------|  wp_msp_    |
| _alerts     |                           |  sales      |
+-------------+                           +-------------+
      ^
      |
      |
+-------------+
| wp_msp_stock|
| _log        |
+-------------+
```

## 6. Database Initialization

The plugin will create these custom tables during activation using the following approach:

```php
function msp_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Sales table
    $table_name = $wpdb->prefix . 'msp_sales';
    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        merchandise_id bigint(20) unsigned NOT NULL,
        quantity int(11) NOT NULL DEFAULT '1',
        price decimal(10,2) NOT NULL DEFAULT '0.00',
        payment_type varchar(50) NOT NULL DEFAULT 'cash',
        show_id bigint(20) unsigned DEFAULT NULL,
        sales_page_id bigint(20) unsigned DEFAULT NULL,
        band_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        notes text,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY merchandise_id (merchandise_id),
        KEY show_id (show_id),
        KEY sales_page_id (sales_page_id),
        KEY band_id (band_id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    // Stock alerts table
    $table_name = $wpdb->prefix . 'msp_stock_alerts';
    $sql .= "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        merchandise_id bigint(20) unsigned NOT NULL,
        threshold int(11) NOT NULL DEFAULT '5',
        status varchar(50) NOT NULL DEFAULT 'active',
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY merchandise_id (merchandise_id)
    ) $charset_collate;";
    
    // Stock log table
    $table_name = $wpdb->prefix . 'msp_stock_log';
    $sql .= "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        merchandise_id bigint(20) unsigned NOT NULL,
        previous_stock int(11) NOT NULL,
        new_stock int(11) NOT NULL,
        change_reason varchar(100) NOT NULL DEFAULT 'manual',
        user_id bigint(20) unsigned DEFAULT NULL,
        notes text,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY merchandise_id (merchandise_id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
```

## 7. Database Upgrade Process

The plugin will handle database upgrades using a version-based approach:

```php
function msp_check_version() {
    $current_version = get_option('msp_db_version', '0');
    
    if (version_compare($current_version, '1.0', '<')) {
        msp_create_tables();
        update_option('msp_db_version', '1.0');
    }
    
    if (version_compare($current_version, '1.1', '<')) {
        msp_upgrade_to_1_1();
        update_option('msp_db_version', '1.1');
    }
    
    // Add more version checks as needed
}

function msp_upgrade_to_1_1() {
    global $wpdb;
    
    // Example: Add a new column to the sales table
    $table_name = $wpdb->prefix . 'msp_sales';
    $wpdb->query("ALTER TABLE $table_name ADD COLUMN discount decimal(10,2) NOT NULL DEFAULT '0.00' AFTER price");
}
```

## 8. Data Migration Considerations

When upgrading from earlier versions or migrating data, consider the following:

1. **Backup**: Always backup the database before migration
2. **Data Validation**: Validate data during migration
3. **Error Handling**: Implement proper error handling
4. **Rollback Plan**: Have a plan to rollback changes if needed
5. **User Communication**: Inform users about the migration process

## 9. Performance Considerations

To ensure optimal database performance:

1. **Indexing**: Use appropriate indexes on frequently queried columns
2. **Query Optimization**: Optimize database queries
3. **Data Archiving**: Implement a strategy for archiving old data
4. **Caching**: Use caching for frequently accessed data
5. **Batch Processing**: Use batch processing for large operations

## 10. Conclusion

This database schema provides a comprehensive structure for the Merchandise Sales Plugin, supporting all the required functionality while maintaining good performance and scalability. The schema is designed to be extensible, allowing for future enhancements and additions as the plugin evolves.