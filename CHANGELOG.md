# Changelog

All notable changes to the Merchandise Sales Plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.5] - 2026-02-08

### Added (documentation)
- **Final Audit**: `docs/FINAL_AUDIT_ROADMAP_1.1.4.md` – Audit checklist for P0, P1, P2 with proof (Playwright test names / manual UAT refs), status fields, definition of 100% passed, and test environment requirements.
- **UAT feedback loop**: `docs/UAT_FEEDBACK_LOOP.md` – Six-step method to run Playwright + manual UAT, fill results in the audit, act on skip/fail (environment or code), and repeat until all items are passed. Feedback loop is also described in the Final Audit document (sectie 4).

### Changed
- **Version**: Plugin 1.1.4 → 1.1.5 (SemVer PATCH: documentation and audit process).

## [1.1.4] - 2026-02-08

### Added (P2.1 – Jim)
- **Reports → Sales**: "Export detail (Excel)" button. Downloads a row-level CSV (one row per sale) with date, band, show, merchandise, quantity, price, payment type, total and related fields for margin analysis in Excel. Uses same filters (band, date range) as the report. Summary export remains available as "Export summary to CSV".

### Added (P2.2 – Hans & Eric)
- **Mobile-first / responsive**: Admin and public CSS updated for critical flows at 375px viewport: touch targets min 44px for buttons and quantity inputs; report filters and sales items stack on small screens; no horizontal scroll on Dashboard, Reports (Sales), and Sales recording.

### Added (P2.3 – Hans & Harry)
- **Low Stock Alert actionable**: Dashboard "Low Stock Items" stat shows warning style when count > 0 and a "View for reorder" button. "Low Stock Alerts" card shows a clear notice ("X items need reordering") and CTA "View low stock for reorder" linking to Reports → Inventory. Reports → Inventory: "Export for reorder (CSV)" button for low stock items (Item, SKU, Current Stock, Threshold, Band) for use with printer/supplier.

### Added (P2.4 – Eric)
- **Educative tooltips**: Settings → General: short description under Currency (EU tours often use €; US use $). Reports → Sales: line stating that all amounts use the configured currency (Settings → General).

### Added (testing)
- **Playwright P2 UAT suite**: `tests/e2e/p2-uat.spec.js` plus `playwright.config.js`. Covers P2.1 (export buttons and CSV downloads), P2.2 (375px viewport, no horizontal scroll, touch targets), P2.3 (Low Stock dashboard and Inventory export), P2.4 (currency tooltip text). Run with `BASE_URL=https://merchmanager.local WP_USER=admin WP_PASSWORD=xxx npm run test:p2`. See `docs/UAT_P2_Roadmap_1.1.4.md`.

## [1.1.3] - 2026-02-08

### Added
- **P0 Failsafe (Roadmap)**: Report data integrity check. Sales report totals are reconciled against a raw aggregation on the sales table; if the values differ, the report is not shown and a clear error message is displayed instead. Prevents displaying incorrect totals. Optional logging to debug.log when WP_DEBUG_LOG is enabled.
- **Sales Service**: `get_sales_totals_raw( $args )` for reconciliation (same filters as `get_sales_summary`, no grouping).

### Fixed
- **Reports**: Sales summary totals were previously taken from the first row of a grouped query (e.g. first day only). Totals are now computed from an explicit ungrouped summary query, so Total Sales, Total Quantity, and Total Revenue are correct for the selected period.

### Changed
- **Reports**: When integrity check fails, the Sales report tab shows an error notice and no totals/tables; CSV export is aborted with the same message.

### Security (P1)
- **Onboarding**: Explicit `current_user_can( 'manage_msp' )` check before processing load_demo, create_band, and complete actions; `$_POST['action']` sanitized with `sanitize_text_field( wp_unslash() )`.

## [1.1.2] - 2026-02-08

### Changed
- **Links**: Plugin URI and GitHub links updated to correct repo (RichardTheuws/merchmanager). Updated in merchmanager.php, README.md, and languages/merchmanager.pot.

## [1.1.1] - 2026-02-08

### Changed
- **WordPress.org**: Plugin display name changed from "Merchandise Sales Plugin" to "MerchManager" to comply with restricted term "plugin" in name/permalink. Updated main plugin file, readme.txt, admin menu/dashboard strings, and language template (.pot).

## [1.1.0] - 2026-02-08

### Fixed
- **Plugin Check (security WARNINGs)**: Session data in sales-recording-service documented with phpcs:ignore (plugin-controlled). Admin settings and dashboard redirect params (settings-updated, demo_loaded) documented with NonceVerification ignore. Reports: all `$_GET` inputs now use `wp_unslash()` before sanitization (tab, category, status, start_date, end_date, change_reason).

## [1.0.9] - 2026-02-08

### Fixed
- **Plugin Check (DB)**: Use `%i` and single `prepare()` for table identifiers in database (DROP TABLE), merchandise (stock alert check), stock-service (get_alert, get_stock_alerts, get_stock_log_entries), and merchandise meta-box (sales/stock_log queries). Refactored get_stock_alerts and get_stock_log_entries to avoid concatenated query variables; added phpcs:ignore for stock-service dynamic get_stock_log_entries.

## [1.0.8] - 2026-02-08

### Fixed
- **Plugin Check**: Added phpcs:ignore for PreparedSQL.NotPrepared and DirectDB.UnescapedDBParameter on dynamic get_sales/get_sales_summary queries (query built from whitelisted fragments and placeholders only; known Plugin Check false positive).

## [1.0.7] - 2026-02-08

### Fixed
- **Plugin Check (sales-service)**: Refactored `get_sales()` and `get_sales_summary()` so the full SQL and values are built and passed in a single `$wpdb->prepare( $sql, ...$values )` call before `get_results()`, satisfying `WordPress.DB.PreparedSQL.NotPrepared` and `PluginCheck.Security.DirectDB.UnescapedDBParameter`. Order/orderby and group_by are now whitelisted instead of interpolated. `get_sale()` (delete_sale) now uses `%i` for the table name.

## [1.0.6] - 2026-02-08

### Fixed
- **Plugin Check (WP repo)**: All DB queries that use the sales table now use `$wpdb->prepare( ..., %i, ... )` for the table name (WP 6.2+ identifier placeholder), satisfying `WordPress.DB.PreparedSQL.NotPrepared` and `PluginCheck.Security.DirectDB.UnescapedDBParameter`. Applied in `Merchmanager_Sales_Page`, `Merchmanager_Show`, and `Merchmanager_Sales_Service` (get_sales and get_sales_summary). Report-service fclose in default branch wrapped with phpcs:ignore.

### Changed
- **Requires at least**: WordPress 5.0 → 6.2 (required for `%i` identifier in `$wpdb->prepare()`).

## [1.0.5] - 2026-02-08

### Fixed
- **Plugin Check / WordPress.org**: Escaping (all `_e()` → `esc_html_e()` or `esc_attr_e()`; `__()` in echo → `esc_html( __() )`; `wp_die( __() )` → `esc_html( __() )`); date format preview in Settings → `date_i18n()` + `esc_html()`; all `date()` in logic → `gmdate()` where appropriate; `rand()` → `wp_rand()`; `load_plugin_textdomain()` removed for repo (WordPress.org loads translations); DB `$query` in get_var/get_results covered by phpcs:ignore where table name is from `$wpdb->prefix`; fopen/fclose/readfile/unlink for CSV and temp downloads documented with phpcs:ignore; direct file access: added `if ( ! defined( 'ABSPATH' ) ) exit;` to admin partials, meta-boxes, public partials, and includes (loader, main class); readme.txt “Tested up to” set to 6.9.

### Changed
- **Release ZIP for WordPress.org**: Release package now excludes `vendor/`, `tests/`, `docs/`, `.cursor/`, `.github/`, and other dev-only files so the ZIP stays under the 10 MB limit. Plugin has no runtime Composer dependencies; `vendor/` is dev-only.
- **Build script**: Added `scripts/build-wp-org-zip.sh` to produce WordPress.org-ready ZIPs; script removes existing `merchmanager-*.zip` files in project root before building.

### Documentation
- `docs/WORDPRESS_ORG_PUBLISHING.md`: Documented 10 MB limit, use of `./scripts/build-wp-org-zip.sh <version>`, and SVN rsync excludes aligned with release ZIP.

## [1.0.4] - 2026-02-08

### Fixed
- **White screen on edit**: Load Band, Tour, Show, Merchandise, Sales Page models in meta-box-loader before meta boxes render
- **Reports Inventory/Stock History errors**: Require Merchmanager_Merchandise model before stock service and reports; add class_exists guard in Stock Service
- **Sales page session**: Only call session_start when headers not sent to avoid "headers already sent" errors

### Added
- Demo images: Band, Tour, Shows, Merchandise get royalty-free images from Picsum Photos (Unsplash) on demo load
- Demo merchandise categories: apparel, music, poster, accessory
- Cursor rule `.cursor/rules/merchmanager-local.mdc` with Local installation details

### Changed
- Session start in sales page shortcode guarded with `headers_sent()` check

## [1.0.3] - 2026-02-07

### Added
- Demo data loader in Setup Wizard Step 1: one-click creation of band, tour, shows, merchandise, sales page, and 15–25 sample sales
- Unified page layout (msp-page-header, msp-page-content) for all admin pages
- Menu reorder: Dashboard, Bands, Tours, Shows, Merchandise, Sales Pages, Sales, Reports, Settings, Setup Wizard
- Onboarding wizard with 5 steps (Welcome, Create Band, Tour/Show, Merchandise, Feature Overview)
- First-run redirect to Setup Wizard for users with `manage_msp` capability
- Setup Wizard submenu and "Run setup wizard" link in Settings
- Default settings on plugin activation (currency EUR, date format, thresholds, etc.)
- Empty-state messaging on Dashboard, Sales, and Reports when no bands exist
- CSV Export button for Sales Reports tab
- Tour shows Import/Export handlers (meta box forms already present; handlers now wired)
- Invalid access code error message on sales page
- Safe checks for band/show in sales page partial

### Fixed
- Sales Service: require Merchmanager_Merchandise model before use in get_sales_summary (Reports page fatal error)
- Uninstall/settings sync: `remove_data` in `msp_settings` now syncs to `msp_remove_data_on_uninstall`; uninstall.php falls back to `msp_settings['remove_data']` for backwards compatibility
- Sales page band/show null-safety and invalid access code feedback

### Changed
- Empty states: clearer copy and CTAs on Dashboard, Sales, Reports
- Onboarding inline CSS moved to merchmanager-admin.css
- Activator sets default `msp_settings` and `msp_remove_data_on_uninstall` on first activation
- Uninstall removes `merchmanager_onboarding_complete` and `merchmanager_version` options when removing data

### Documentation
- IMPROVEMENTS.md added with 5 indispensable "forgotten" features list

## [1.0.2] - 2026-02-07

### Added
- Admin Sales page (`merchmanager-admin-sales.php`) for recording sales
- Band selector and band dashboard public partials for shortcode
- Admin and public JavaScript stub files
- Settings API registration for `msp_settings`
- Dashboard wired to ReportService and StockService for real data
- Sales Reports tab with summary, top selling items, payment type breakdown
- Theuws Admin Dashboard Design Guide implementation (gray palette, cards, badges)
- Admin footer with Theuws logo and developer credit
- Assets folder with Theuws logo for plugin branding
- Session start for sales page access code verification

### Fixed
- Settings now save correctly via WordPress Settings API
- `get_option('msp_settings')` safe access in sales page partial
- Band dashboard shortcode reads `band_id` from query string

### Changed
- Admin CSS updated to Theuws brand style (gray-200 borders, rounded-lg, etc.)
- Sanitize callback for settings preserves checkbox values

### Removed
- Duplicate brand-style-guide folder (logo moved to assets/)

### Documentation
- Consolidated docs/user/ to point to main user_guide.md
- Archived PROJECT-PICKUP.md to docs/PROJECT-PICKUP-ARCHIVED.md

## [1.0.1] - 2025-09-06

### Added
- Improved user interface for Sales Management
- Styled dropdown for band selection
- 'sales_page_id' column to sales database table
- Reset functionality for generated sales pages

### Fixed
- Bug fixes for sales page generation
- Improved error handling and logging

## [1.0.0] - 2025-01-01

### Added
- Initial release with core functionality
- Custom post types (Band, Tour, Show, Merchandise, Sales Page)
- User roles and permissions system
- Basic sales recording functionality
- Stock management features
- CSV import/export capabilities
- Basic reporting system