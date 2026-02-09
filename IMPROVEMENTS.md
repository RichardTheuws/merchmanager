# MerchManager – Indispensable Features

This document lists five "forgotten" features that are critical for production readiness. All items below have been addressed in version 1.0.3 unless marked as future work.

## 1. Onboarding Wizard (Implemented in 1.0.3)

**Status:** Implemented

A first-run setup wizard guides bands through:
- Welcome and feature overview
- Creating their first band
- Adding a tour and show (optional)
- Adding merchandise (optional)
- Feature overview and shortcode reference

The wizard runs automatically when a user with `manage_msp` visits a MerchManager admin page for the first time. Users can re-run it from **Merchandise Sales → Setup Wizard** or **Settings → Run setup wizard**.

---

## 2. CSV Import/Export UI (Implemented in 1.0.3)

**Status:** Implemented

- **Sales Reports:** Export to CSV button on the Sales Reports tab.
- **Tours:** Import/Export Shows meta box on tour edit screen (forms and handlers wired).

Backend methods existed; the UI was missing. Handlers now process imports and exports correctly.

---

## 3. Report Export Buttons (Implemented in 1.0.3)

**Status:** Implemented

Sales Reports tab includes an "Export to CSV" button that uses the current filters (band, date range) and downloads a CSV file.

---

## 4. Default Settings + First-Run Setup (Implemented in 1.0.3)

**Status:** Implemented

On first activation, the plugin sets sensible defaults for `msp_settings` (currency EUR, date format, thresholds, role permissions, etc.). The wizard can optionally create the first band to avoid an empty dashboard.

---

## 5. Empty-State Guidance (Implemented in 1.0.3)

**Status:** Implemented

When no bands, tours, or merchandise exist:
- **Dashboard:** Message with "Add Band" and "Run setup wizard" links.
- **Sales:** Message when no bands or no merchandise for the selected band.
- **Reports:** Message with links to add a band or run the wizard.

---

## Future Enhancements (Not in 1.0.3)

- **PDF export** for reports (PRD mention; CSV export implemented).
- **Merchandise CSV import/export** UI (backend methods may exist; UI not yet implemented).
- **Configurable CPT slugs** to reduce risk of conflict with existing pages (e.g. `bands`, `tours`).
