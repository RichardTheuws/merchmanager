# Code Standards & Best Practices

## 📋 Overview

This document outlines the coding standards and best practices for the Merchandise Sales Plugin development.

## 🎯 PHP Standards

### WordPress Coding Standards

We follow the official [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

#### Key Rules:
- Use 4 spaces for indentation (no tabs)
- Use single quotes for strings unless interpolation is needed
- Use braces for all control structures
- Use Yoda conditions for comparisons
- Limit lines to 80 characters

#### Example:
```php
<?php
/**
 * Proper PHP documentation
 *
 * @param int    $band_id Band ID.
 * @param string $name    Band name.
 * @return bool True if successful, false otherwise.
 */
function update_band_name( $band_id, $name ) {
    if ( true === is_valid_band( $band_id ) ) {
        $result = wp_update_post( array(
            'ID'         => $band_id,
            'post_title' => sanitize_text_field( $name ),
        ) );
        
        return ! is_wp_error( $result );
    }
    
    return false;
}
```

### Namespace and Autoloading

- Use PSR-4 autoloading standard
- Namespace: `Merchmanager\\`
- Class names in PascalCase
- File names match class names

#### Structure:
```
includes/
├── class-merchmanager.php          # Merchmanager
├── models/
│   ├── class-band.php              # Merchmanager\\Models\\Band
│   └── class-merchandise.php       # Merchmanager\\Models\\Merchandise
└── services/
    ├── class-sales-service.php     # Merchmanager\\Services\\SalesService
    └── class-report-service.php    # Merchmanager\\Services\\ReportService
```

## 🎨 JavaScript Standards

### Modern JavaScript (ES6+)

- Use modern JavaScript features
- Use const/let instead of var
- Arrow functions for callbacks
- Template literals for strings
- Destructuring and spread operators

#### Example:
```javascript
// Good
const { bandId, merchandiseId } = saleData;
const updateStock = async (itemId, quantity) => {
    try {
        const response = await fetch(`/wp-json/msp/v1/stock/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': msp_ajax.nonce
            },
            body: JSON.stringify({ quantity })
        });
        
        return await response.json();
    } catch (error) {
        console.error('Stock update failed:', error);
        throw error;
    }
};
```

### jQuery Usage

- Use modern JavaScript when possible
- When using jQuery, follow WordPress patterns
- Use `$` instead of `jQuery`
- Cache jQuery selections

## 🎭 CSS/SASS Standards

### BEM Methodology

Use Block-Element-Modifier methodology:

```scss
// Block
.msp-dashboard {
    padding: 20px;
    
    // Element
    &__header {
        background: #f3f4f5;
        padding: 15px;
        
        // Modifier
        &--collapsed {
            padding: 10px;
        }
    }
    
    &__content {
        margin-top: 20px;
    }
}
```

### Responsive Design

- Mobile-first approach
- Use CSS Grid and Flexbox
- Responsive breakpoints:
  - Mobile: < 768px
  - Tablet: 768px - 1024px
  - Desktop: > 1024px

## 🗃️ Database Standards

### WordPress Database API

- Use `$wpdb` for database operations
- Always use prepared statements
- Properly escape all data
- Use appropriate data types

#### Example:
```php
global $wpdb;

$sales = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}msp_sales 
         WHERE band_id = %d AND date >= %s
         ORDER BY date DESC
         LIMIT %d",
        $band_id,
        $start_date,
        $limit
    )
);
```

### Custom Tables

- Use proper indexing
- Follow WordPress table naming conventions
- Include created_at/updated_at timestamps
- Use appropriate data types and sizes

## 🔐 Security Standards

### Input Validation

- Validate all user input
- Use WordPress sanitization functions
- Validate data types and ranges

```php
// Good
$band_id = absint( $_POST['band_id'] );
$price   = floatval( $_POST['price'] );
$name    = sanitize_text_field( $_POST['name'] );
$email   = sanitize_email( $_POST['email'] );
```

### Output Escaping

- Escape all output
- Use appropriate escaping functions
- Context-aware escaping

```php
// Good
echo esc_html( $band_name );
echo esc_url( $band_website );
echo esc_attr( $data_attribute );
printf( __( 'Welcome, %s!', 'merchmanager' ), esc_html( $user_name ) );
```

### Nonce Verification

- Use nonces for all forms and AJAX requests
- Verify nonces before processing data

```php
// Form nonce
wp_nonce_field( 'msp_save_band', 'msp_nonce' );

// AJAX nonce
wp_localize_script( 'msp-admin', 'msp_ajax', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'nonce'    => wp_create_nonce( 'msp_ajax_nonce' )
) );

// Verify nonce
if ( ! wp_verify_nonce( $_POST['msp_nonce'], 'msp_save_band' ) ) {
    wp_die( 'Security check failed' );
}
```

## 🧪 Testing Standards

### Unit Tests

- Test all public methods
- Mock dependencies
- Test edge cases
- Achieve 80%+ coverage

#### Example:
```php
class BandTest extends TestCase {
    
    public function test_can_create_band() {
        $band = new Band();
        $band->set_name( 'Test Band' );
        $band->set_contact_email( 'test@example.com' );
        
        $this->assertTrue( $band->save() );
        $this->assertIsInt( $band->get_id() );
    }
    
    public function test_cannot_create_band_without_name() {
        $this->expectException( InvalidArgumentException::class );
        
        $band = new Band();
        $band->set_contact_email( 'test@example.com' );
        $band->save();
    }
}
```

### Integration Tests

- Test WordPress hooks and filters
- Test database interactions
- Test user permissions

### E2E Tests

- Test user workflows
- Test UI interactions
- Cross-browser testing

## 📝 Documentation Standards

### PHPDoc Comments

```php
/**
 * Update merchandise stock level.
 *
 * @since 1.0.0
 *
 * @param int $merchandise_id Merchandise ID.
 * @param int $new_stock      New stock quantity.
 * @param int $user_id        User ID making the change (optional).
 * @param string $reason      Reason for stock change (optional).
 * @return bool True on success, false on failure.
 * @throws InvalidArgumentException If merchandise ID is invalid.
 */
public function update_stock( $merchandise_id, $new_stock, $user_id = null, $reason = 'update' ) {
    // Method implementation
}
```

### Inline Comments

- Explain why, not what
- Use clear, concise language
- Avoid obvious comments

```php
// Good - explains the purpose
// Convert price to cents to avoid floating point precision issues
$price_in_cents = (int) ( $price * 100 );

// Bad - states the obvious
// Multiply price by 100
$price_in_cents = $price * 100;
```

## 🚀 Performance Standards

### Database Optimization

- Use proper indexing
- Limit query results
- Use caching where appropriate
- Avoid N+1 query problems

### Asset Optimization

- Minify CSS and JavaScript
- Use appropriate image formats
- Implement lazy loading
- Use CDN for static assets

### Memory Management

- Use generators for large datasets
- Free memory when done with large variables
- Use appropriate data structures

## 🔄 Version Control Standards

### Commit Messages

Follow Conventional Commits format:

```
feat: add band management functionality
fix: resolve sales calculation error
docs: update installation guide
chore: update dependencies
style: fix code formatting
refactor: improve database class structure
test: add unit tests for sales service
```

### Branch Naming

- `feature/band-management`
- `fix/sales-calculation`
- `docs/installation-guide`
- `release/v1.1.0`

## 🎯 Code Review Checklist

### Before Submission
- [ ] Code follows coding standards
- [ ] Tests are written and passing
- [ ] Documentation is updated
- [ ] No console errors
- [ ] Responsive design works
- [ ] Accessibility standards met
- [ ] Performance optimized
- [ ] Security measures implemented

### Review Points
- Code clarity and readability
- Architecture and design patterns
- Error handling and validation
- Test coverage and quality
- Performance considerations
- Security implications

---

*These standards are living documents and will be updated as needed. All contributors are expected to follow these guidelines.*