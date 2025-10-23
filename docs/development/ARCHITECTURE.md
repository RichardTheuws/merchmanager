# System Architecture

## 🏗️ Overview

This document describes the architecture of the Merchandise Sales Plugin, including design patterns, component structure, and system design decisions.

## 🎯 Architectural Principles

### Core Principles
1. **Modularity**: Components are loosely coupled and highly cohesive
2. **Testability**: All components are designed for easy testing
3. **Extensibility**: Easy to add new features without breaking existing ones
4. **Maintainability**: Clean, well-documented code following standards
5. **Performance**: Optimized for speed and scalability

### Design Patterns
- **MVC-inspired**: Model-View-Controller pattern adaptation
- **Dependency Injection**: For better testability and flexibility
- **Repository Pattern**: For data access abstraction
- **Service Layer**: For business logic separation
- **Observer Pattern**: For event-driven architecture

## 📦 Component Architecture

### High-Level Structure

```
merchmanager/
├── admin/                 # Admin-facing functionality
├── includes/              # Core plugin components
│   ├── models/           # Data models and entities
│   ├── services/         # Business logic services
│   ├── database/         # Database operations
│   └── post-types/       # Custom post type definitions
├── public/               # Public-facing functionality
├── assets/               # Static assets (JS, CSS, images)
├── tests/                # Test suites
└── templates/            # Template files
```

### Core Components

#### 1. Models Layer
**Responsibility**: Data representation and business rules

```php
// Band Model Example
class Band extends BaseModel {
    private $id;
    private $name;
    private $contact_email;
    private $merchandise_items;
    
    public function add_merchandise( Merchandise $item ) {}
    public function get_total_sales() {}
    public function validate() {}
}
```

#### 2. Services Layer
**Responsibility**: Business logic and operations

```php
// Sales Service Example
class SalesService {
    public function record_sale( Sale $sale ) {}
    public function get_sales_report( $band_id, $date_range ) {}
    public function process_refund( $sale_id ) {}
}
```

#### 3. Database Layer
**Responsibility**: Data persistence and retrieval

```php
// Database Repository Example
class SalesRepository {
    public function find_by_band( $band_id ) {}
    public function save( Sale $sale ) {}
    public function get_daily_totals( $band_id ) {}
}
```

#### 4. Controllers Layer
**Responsibility**: Handle requests and coordinate responses

```php
// Admin Controller Example
class AdminController {
    public function dashboard() {
        $sales_data = $this->sales_service->get_totals();
        $view = new DashboardView( $sales_data );
        return $view->render();
    }
}
```

## 🗃️ Database Architecture

### Custom Tables

#### 1. Sales Table (`wp_msp_sales`)
```sql
CREATE TABLE wp_msp_sales (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    date DATETIME NOT NULL,
    merchandise_id BIGINT(20) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    payment_type VARCHAR(50) NOT NULL,
    show_id BIGINT(20) DEFAULT NULL,
    sales_page_id BIGINT(20) DEFAULT NULL,
    band_id BIGINT(20) NOT NULL,
    user_id BIGINT(20) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY merchandise_id (merchandise_id),
    KEY show_id (show_id),
    KEY sales_page_id (sales_page_id),
    KEY band_id (band_id),
    KEY user_id (user_id),
    KEY date (date)
)
```

#### 2. Stock Log Table (`wp_msp_stock_log`)
```sql
CREATE TABLE wp_msp_stock_log (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    merchandise_id BIGINT(20) NOT NULL,
    previous_stock INT(11) NOT NULL,
    new_stock INT(11) NOT NULL,
    change_reason VARCHAR(50) NOT NULL,
    user_id BIGINT(20) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY merchandise_id (merchandise_id),
    KEY user_id (user_id),
    KEY created_at (created_at)
)
```

### WordPress Integration

#### Custom Post Types
- **msp_band**: Bands and artists
- **msp_tour**: Tour schedules
- **msp_show**: Individual shows
- **msp_merchandise**: Merchandise items
- **msp_sales_page**: Generated sales pages

#### Metadata Structure
```php
// Band metadata
update_post_meta( $band_id, '_msp_band_contact_email', $email );
update_post_meta( $band_id, '_msp_band_contact_phone', $phone );

// Merchandise metadata
update_post_meta( $merch_id, '_msp_merchandise_price', $price );
update_post_meta( $merch_id, '_msp_merchandise_stock', $stock );
update_post_meta( $merch_id, '_msp_merchandise_band_id', $band_id );
```

## 🌐 API Architecture

### REST API Endpoints

#### Bands API
```
GET    /wp-json/msp/v1/bands          # List bands
POST   /wp-json/msp/v1/bands          # Create band
GET    /wp-json/msp/v1/bands/{id}     # Get band
PUT    /wp-json/msp/v1/bands/{id}     # Update band
DELETE /wp-json/msp/v1/bands/{id}     # Delete band
```

#### Sales API
```
GET    /wp-json/msp/v1/sales          # List sales
POST   /wp-json/msp/v1/sales          # Record sale
GET    /wp-json/msp/v1/sales/{id}     # Get sale
PUT    /wp-json/msp/v1/sales/{id}     # Update sale
DELETE /wp-json/msp/v1/sales/{id}     # Delete sale
```

### Internal API Structure

```php
class REST_Controller {
    public function register_routes() {
        register_rest_route( 'msp/v1', '/bands', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_bands' ),
            'permission_callback' => array( $this, 'get_bands_permissions_check' ),
            'args'                => $this->get_collection_params(),
        ) );
    }
    
    public function get_bands( $request ) {
        $bands_service = new BandsService();
        $bands = $bands_service->get_all_bands();
        
        return rest_ensure_response( $bands );
    }
}
```

## 🔌 Plugin Hooks System

### Actions
```php
// After band is saved
do_action( 'msp_after_band_save', $band_id, $band_data );

// After sale is recorded
do_action( 'msp_after_sale_recorded', $sale_id, $sale_data );

// Low stock alert
do_action( 'msp_low_stock_alert', $merchandise_id, $stock_level );
```

### Filters
```php
// Filter band data before saving
$band_data = apply_filters( 'msp_band_data', $band_data );

// Filter sale data before recording
$sale_data = apply_filters( 'msp_sale_data', $sale_data );

// Filter report data before display
$report_data = apply_filters( 'msp_report_data', $report_data );
```

## 🎨 Frontend Architecture

### JavaScript Architecture

#### Module Structure
```javascript
// ES6 Modules
import { SalesManager } from './modules/sales-manager';
import { StockManager } from './modules/stock-manager';
import { ReportGenerator } from './modules/report-generator';

// Main application
class MSPApp {
    constructor() {
        this.salesManager = new SalesManager();
        this.stockManager = new StockManager();
        this.reportGenerator = new ReportGenerator();
    }
    
    init() {
        this.salesManager.init();
        this.stockManager.init();
    }
}

// Initialize app
document.addEventListener( 'DOMContentLoaded', () => {
    window.mspApp = new MSPApp();
    window.mspApp.init();
} );
```

### CSS Architecture

#### SMACSS Methodology
```scss
// Base styles
@import 'base/variables';
@import 'base/mixins';
@import 'base/reset';

// Layout styles
@import 'layout/header';
@import 'layout/footer';
@import 'layout/grid';

// Module styles
@import 'modules/dashboard';
@import 'modules/sales-form';
@import 'modules/reports';

// State styles
@import 'states/loading';
@import 'states/error';
@import 'states/success';

// Theme styles
@import 'themes/light';
@import 'themes/dark';
```

## 🔄 Data Flow Architecture

### Sales Recording Flow

1. **User Interaction**: Sales form submission
2. **Validation**: Client-side and server-side validation
3. **Processing**: Sales service processes the sale
4. **Persistence**: Data saved to database
5. **Stock Update**: Stock levels updated
6. **Notification**: Events triggered for hooks
7. **Response**: Success/error response to user

### Report Generation Flow

1. **Request**: User requests report with parameters
2. **Data Retrieval**: Repository fetches data from database
3. **Processing**: Service processes and formats data
4. **Formatting**: Data formatted for display/export
5. **Delivery**: Report returned to user

## 🚀 Performance Architecture

### Caching Strategy

#### Transient Caching
```php
// Cache report data for 1 hour
$report_data = get_transient( 'msp_sales_report_' . $report_key );

if ( false === $report_data ) {
    $report_data = $this->generate_report_data();
    set_transient( 'msp_sales_report_' . $report_key, $report_data, HOUR_IN_SECONDS );
}
```

#### Object Caching
```php
// Use WordPress object cache
wp_cache_set( 'band_data_' . $band_id, $band_data, 'merchmanager', 3600 );
$band_data = wp_cache_get( 'band_data_' . $band_id, 'merchmanager' );
```

### Database Optimization

#### Indexing Strategy
- Index all foreign keys
- Index frequently queried columns
- Composite indexes for common query patterns
- Regular index maintenance

#### Query Optimization
- Use EXPLAIN to analyze queries
- Limit result sets
- Avoid N+1 query problems
- Use appropriate JOIN types

## 🔒 Security Architecture

### Authentication & Authorization

```php
// Role-based access control
class AccessControl {
    public static function can_manage_band( $user_id, $band_id ) {
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }
        
        $user_bands = get_user_meta( $user_id, '_msp_associated_bands', true );
        return in_array( $band_id, (array) $user_bands );
    }
}
```

### Data Protection

- **Encryption**: Sensitive data encryption at rest
- **Sanitization**: Input validation and sanitization
- **Escaping**: Output escaping for all contexts
- **Nonces**: CSRF protection for all forms

## 📊 Monitoring Architecture

### Logging System

```php
class Logger {
    public static function info( $message, $context = array() ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'MSP INFO: ' . $message . ' ' . json_encode( $context ) );
        }
    }
    
    public static function error( $message, $context = array() ) {
        error_log( 'MSP ERROR: ' . $message . ' ' . json_encode( $context ) );
    }
}
```

### Performance Monitoring

- Query execution time logging
- Memory usage monitoring
- API response time tracking
- Error rate monitoring

## 🧪 Testing Architecture

### Test Pyramid

```
        /
       / \
      /   \    E2E Tests (10%)
     /-----\
    /       \  Integration Tests (20%)
   /---------\
  /           \ Unit Tests (70%)
 /_____________\
```

### Test Structure

```
tests/
├── unit/                 # Unit tests (70%)
│   ├── models/          # Model tests
│   ├── services/        # Service tests
│   └── database/        # Database tests
├── integration/         # Integration tests (20%)
│   ├── api/             # API integration tests
│   ├── wordpress/       # WordPress integration
│   └── database/        # Database integration
└── acceptance/          # E2E tests (10%)
    ├── admin/           # Admin area tests
    ├── public/          # Public area tests
    └── api/             # API acceptance tests
```

---

*This architecture document provides a comprehensive overview of the system design. Refer to specific component documentation for detailed implementation details.*