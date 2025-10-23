# Merchandise Sales Plugin

A comprehensive WordPress plugin for bands and music artists to manage merchandise sales during tours and events.

## Description

The Merchandise Sales Plugin is designed to help bands, musicians, tour managers, and merchandising staff manage their merchandise inventory and sales across multiple tours and venues. The plugin offers a complete solution for tracking inventory, recording sales, managing tours, and generating reports, all within a WordPress environment.

### Key Features

- **Multi-band support**: Manage merchandise for multiple bands
- **Tour and show management**: Create and manage tours and shows
- **Merchandise inventory management**: Track stock levels and manage inventory
- **Sales recording and tracking**: Record sales and associate them with specific tours and locations
- **Dynamic sales page generation**: Generate temporary sales pages for events
- **Detailed reporting**: Generate sales and inventory reports
- **CSV import/export functionality**: Import and export tour schedules and merchandise data
- **Stock level monitoring and alerts**: Get notified when stock levels are low

## Installation

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Go to Plugins > Add New
4. Click "Upload Plugin"
5. Choose the ZIP file and click "Install Now"
6. After installation, click "Activate Plugin"

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Modern web browsers (Chrome, Firefox, Safari, Edge)

## Documentation

Comprehensive documentation is available in the `docs` folder:

- [Product Requirements Document](docs/prd.md)
- [Technical Specification](docs/technical_specification.md)
- [Development Roadmap](docs/development_roadmap.md)
- [Database Schema](docs/database_schema.md)
- [API Documentation](docs/api_documentation.md)
- [Testing Plan](docs/testing_plan.md)
- [User Guide](docs/user_guide.md)
- [Project Architecture](docs/project_architecture.md)

## Development

### Getting Started

1. Clone the repository
2. Install dependencies with Composer: `composer install`
3. Set up a local WordPress development environment
4. Activate the plugin in WordPress

### Directory Structure

```
merchmanager/
├── admin/                  # Admin-specific functionality
│   ├── css/                # Admin stylesheets
│   ├── js/                 # Admin JavaScript
│   ├── partials/           # Admin view templates
│   └── class-admin.php     # Admin class
├── includes/               # Core plugin functionality
│   ├── class-activator.php # Plugin activation
│   ├── class-deactivator.php # Plugin deactivation
│   ├── class-i18n.php      # Internationalization
│   ├── class-loader.php    # Hook and filter loader
│   ├── class-merchmanager.php # Main plugin class
│   ├── post-types/         # Custom post type definitions
│   ├── taxonomies/         # Custom taxonomy definitions
│   ├── models/             # Data models
│   └── services/           # Business logic services
├── public/                 # Public-facing functionality
│   ├── css/                # Public stylesheets
│   ├── js/                 # Public JavaScript
│   ├── partials/           # Public view templates
│   └── class-public.php    # Public class
├── languages/              # Translation files
├── templates/              # Template overrides
├── assets/                 # Static assets
├── docs/                   # Documentation
├── merchmanager.php        # Plugin bootstrap file
├── uninstall.php           # Cleanup on uninstall
└── README.md               # This file
```

### Coding Standards

This project follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

### Testing

The plugin includes a comprehensive testing suite:

- Unit tests with PHPUnit
- Integration tests with WP-Browser
- End-to-end tests with Cypress

To run the tests:

```
# Run unit tests
phpunit

# Run integration tests
vendor/bin/codecept run wpunit

# Run end-to-end tests
npx cypress run
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/my-new-feature`
5. Submit a pull request

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## Support

For support, please:

1. Check the [User Guide](docs/user_guide.md)
2. Visit the support forum
3. Contact the plugin developer

## Changelog

### 1.0.1
- Improved user interface for Sales Management
- Bug fixes for sales page generation
- Improved error handling and logging
- Addition of 'sales_page_id' column to the sales database table
- Implementation of reset functionality for generated sales pages

### 1.0.0
- Initial release