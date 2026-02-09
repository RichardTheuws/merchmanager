=== MerchManager ===
Contributors: merchandisenl
Tags: merchandise, sales, bands, music, inventory, tours
Requires at least: 6.2
Tested up to: 6.9
Stable tag: 1.1.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Manage merchandise sales for bands during tours and events. Track inventory, record sales, manage tours, and generate reports.

== Description ==

MerchManager is designed to help bands, musicians, tour managers, and merchandising staff manage their merchandise inventory and sales across multiple tours and venues. It offers a complete solution for tracking inventory, recording sales, managing tours, and generating reports, all within a WordPress environment.

= Why MerchManager? =

Richard (Theuws Consulting) ran a European merchandise company for over 20 years and has always helped bands with their merchandise management. This project is his way of giving something back: a free, open tool so more bands can run their merch without extra cost. There is no commercial version and there never will be—everyone else already makes money from bands; this is built with the help of AI and offered as-is. You are welcome to use it, fork it, improve it, or contribute. It exists as a WordPress plugin simply because Richard has been using WordPress since 2006 and is most at home there.

= Key Features =

* **Multi-band support**: Manage merchandise for multiple bands
* **Tour and show management**: Create and manage tours and shows
* **Merchandise inventory management**: Track stock levels and manage inventory
* **Sales recording and tracking**: Record sales and associate them with specific tours and locations
* **Dynamic sales page generation**: Generate temporary sales pages for events with access codes
* **Detailed reporting**: Generate sales and inventory reports
* **CSV import/export functionality**: Import and export tour schedules and merchandise data
* **Stock level monitoring and alerts**: Get notified when stock levels are low
* **Onboarding wizard**: Step-by-step setup for first-time users

== Installation ==

1. Download the plugin ZIP file or install from WordPress.org
2. Log in to your WordPress admin panel
3. Go to Plugins > Add New
4. Click "Upload Plugin" and choose the ZIP file, or search for "MerchManager"
5. Click "Install Now"
6. After installation, click "Activate Plugin"
7. Follow the onboarding wizard to set up your first band, tour, and merchandise

== Frequently Asked Questions ==

= What user roles does the plugin create? =

The plugin creates custom capabilities and integrates with WordPress roles. Users with `manage_msp` capability can access the full admin interface.

= Can I use this for multiple bands? =

Yes, the plugin supports multiple bands. Each band can have its own tours, shows, merchandise, and sales pages.

= How do I create a sales page for an event? =

Create a Band, Tour, Show, and Merchandise items first. Then create a Sales Page from the Band or Show and share the generated access code with your merchandising staff.

== Screenshots ==

1. Dashboard overview
2. Sales recording interface
3. Reports tab with sales summary

== Changelog ==

= 1.0.5 = - 2026-02-08
* Changed: Release ZIP for WordPress.org now excludes vendor, tests, docs, and dev files (stays under 10 MB)
* Added: scripts/build-wp-org-zip.sh for building WordPress.org-ready ZIPs; removes old ZIPs before building
* Documentation: WORDPRESS_ORG_PUBLISHING.md updated with build command and SVN excludes

= 1.0.4 = - 2026-02-08
* Fixed: White screen on edit Shows, Tours, Merchandise – load model classes before meta boxes
* Fixed: Reports Inventory Management and Stock History errors – require Merchandise model
* Fixed: Sales page session "headers already sent" – guard session_start with headers_sent check
* Added: Demo images from Picsum Photos (Unsplash) for band, tour, shows, merchandise on demo load
* Added: Demo merchandise categories (apparel, music, poster, accessory)
* Added: Cursor rule for local installation details

= 1.0.3 = - 2026-02-07
* Added: Onboarding wizard with 5 steps (Welcome, Create Band, Tour/Show, Merchandise, Feature Overview)
* Added: First-run redirect to Setup Wizard for users with manage_msp capability
* Added: Setup Wizard submenu and "Run setup wizard" link in Settings
* Added: Default settings on plugin activation (currency EUR, date format, thresholds, etc.)
* Added: Empty-state messaging on Dashboard, Sales, and Reports when no bands exist
* Added: CSV Export button for Sales Reports tab
* Added: Tour shows Import/Export handlers
* Added: Invalid access code error message on sales page
* Fixed: Uninstall/settings sync for remove_data option
* Fixed: Sales page band/show null-safety and invalid access code feedback
* Changed: Activator sets default msp_settings and msp_remove_data_on_uninstall on first activation

= 1.0.2 = - 2026-02-07
* Added: Admin Sales page for recording sales
* Added: Band selector and band dashboard public partials for shortcode
* Added: Settings API registration for msp_settings
* Added: Dashboard wired to ReportService and StockService for real data
* Added: Sales Reports tab with summary, top selling items, payment type breakdown
* Fixed: Settings now save correctly via WordPress Settings API

= 1.0.1 = - 2025-09-06
* Added: Improved user interface for Sales Management
* Added: Styled dropdown for band selection
* Added: sales_page_id column to sales database table
* Added: Reset functionality for generated sales pages
* Fixed: Bug fixes for sales page generation
* Fixed: Improved error handling and logging

= 1.0.0 = - 2025-01-01
* Initial release with core functionality
* Custom post types (Band, Tour, Show, Merchandise, Sales Page)
* User roles and permissions system
* Basic sales recording functionality
* Stock management features
* CSV import/export capabilities
* Basic reporting system

== Upgrade Notice ==

= 1.0.4 =
Fixes white screen on edit pages, Reports errors, and Sales page session issues. Adds demo images. Recommended update.

= 1.0.3 =
Security fixes, onboarding wizard, and improved empty-state handling. Recommended update.
