# Finale audit – MerchManager Roadmap 1.1.4

**Versie:** 1.0  
**Datum:** 2026-02-08  
**Doel:** Eén overzicht van alle roadmap-items (P0, P1, P2) met bewijs (Playwright of handmatige UAT) en status. De roadmap is **voltooid** wanneer deze audit **100% geslaagd** is.

---

## 1. Definitie “100% geslaagd”

- **Alle** audit-items in onderstaande checklist hebben status **Geslaagd**.
- Geen item mag voor de beoogde release op **Overgeslagen** of **Gefaald** staan, tenzij er een expliciete, gedocumenteerde uitzondering is (bijv. “niet van toepassing op deze omgeving”).
- De testomgeving voldoet aan de [eisen in sectie 2](#2-testomgeving-eisen), zodat alle geautomatiseerde tests **kunnen** draaien (geen skip wegens ontbrekende versie of data).

---

## 2. Testomgeving-eisen

Om de Playwright-suite zonder noodzakelijke skips te laten slagen:

| Eis | Toelichting |
|-----|-------------|
| **MerchManager 1.1.4+** | Plugin geïnstalleerd en actief; bevat P2.1 detail-export, P2.4 tooltips. |
| **Onboarding voltooid** | Setup wizard afgerond; geen redirect naar onboarding vanaf adminpagina’s. |
| **Minimaal één band** | Reports → Sales toont filters en exportknoppen; Reports → Sales toont currency-tooltip; geen “No data yet” empty state. |
| **Optioneel: low-stock items** | Voor P2.3: min. één item met voorraad ≤ drempel (en > 0) om “View for reorder” en Inventory-export te testen. |
| **Optioneel: verkopen** | Voor P2.1 export: datumbereik met sales zorgt voor niet-lege CSV; geen integrity_error als data consistent is. |
| **Admin-account** | Gebruiker met rechten o.a. `manage_msp_sales`, `manage_msp_merchandise`; credentials voor Playwright (bijv. `WP_USER`, `WP_PASSWORD`). |
| **BASE_URL** | Bijv. `https://merchmanager.local`; HTTPS met self-signed cert wordt door Playwright geaccepteerd (`ignoreHTTPSErrors: true`). |

Zie ook [UAT_P2_Roadmap_1.1.4.md](UAT_P2_Roadmap_1.1.4.md) voor handmatige UAT-voorbereiding.

---

## 3. Audit-checklist

### P0 – Failsafe (data-integriteit)

| Id | Item | Acceptatie | Bewijs | Status | Opmerking |
|----|------|------------|--------|--------|-----------|
| P0.1 | Reconciliatie rapporten | Bij afwijking > drempel: geen totaal getoond, duidelijke foutmelding | Code: `Merchmanager_Report_Service`; handmatig: [UAT_P0_Failsafe_1.1.3.md](UAT_P0_Failsafe_1.1.3.md) | Niet getest | Geen Playwright-test voor P0; handmatige UAT beschikbaar |
| P0.2 | Foutmelding en logging | Melding begrijpelijk; beheer kan incidenten in logs terugvinden | Zie P0.1 | Niet getest | Idem |
| P0.3 | Geen stille correcties | Geen code die totalen “repareert” zonder gebruiker te informeren | Code review / CHANGELOG 1.1.3 | Niet getest | Idem |

**P0-bewijs:** Handmatige UAT volgens [UAT_P0_Failsafe_1.1.3.md](UAT_P0_Failsafe_1.1.3.md). Geen geautomatiseerde P0-tests in de huidige suite.

---

### P1 – Betrouwbaarheid (bestaande flows)

| Id | Item | Acceptatie | Bewijs | Status | Opmerking |
|----|------|------------|--------|--------|-----------|
| P1.1 | Edge cases UAT 1.0.4 | Geen wit scherm of onverwacht gedrag in beschreven scenario’s | Handmatig: [UAT_AND_AUDIT_1.0.4_REPORT.md](UAT_AND_AUDIT_1.0.4_REPORT.md) | Niet getest | Geen Playwright voor P1 edge cases |
| P1.2 | Plugin Check (PCP) | Geen critical/blocker; bewuste uitzonderingen gedocumenteerd | [PLUGIN_CHECK_REPORT_2026-02-08.md](PLUGIN_CHECK_REPORT_2026-02-08.md) | Niet getest | Handmatig / CI |
| P1.3 | Validatie en escaping | Input gesanitized/geëscaped; geen nieuwe security-waarschuwingen | Code / PHPCS / Plugin Check | Niet getest | Idem |

**P1-bewijs:** Handmatige UAT en Plugin Check. Geen geautomatiseerde P1-tests in de huidige suite.

---

### P2.1 – Export verkoopcijfers naar Excel (Jim)

| Id | Item | Acceptatie | Bewijs | Status | Opmerking |
|----|------|------------|--------|--------|-----------|
| P2.1.1 | Beide exportknoppen zichtbaar | Reports → Sales toont “Export summary to CSV” en “Export detail (Excel)” | Playwright: `tests/e2e/p2-uat.spec.js` → “Reports → Sales shows both export buttons” | Overgeslagen | Plugin < 1.1.4 of geen bands op test-URL |
| P2.1.2 | Detail-link params en nonce | Href bevat `msp_export_sales_detail=1` en `_wpnonce=` | Playwright: “Export detail link has correct query params and nonce” | Overgeslagen | Idem |
| P2.1.3 | Klik detail-export → CSV-download | Bestand `sales-detail-YYYY-MM-DD.csv` | Playwright: “Clicking Export detail triggers CSV download” | Overgeslagen | Idem |
| P2.1.4 | Klik summary-export → CSV-download | Bestand `sales-report-YYYY-MM-DD.csv` | Playwright: “Clicking Export summary triggers CSV download” | Overgeslagen | Export triggert geen download op deze omgeving |

**P2.1-bewijs:** `tests/e2e/p2-uat.spec.js` (describe “P2.1 – Export detail (Excel)”). Handmatig: [UAT_P2_Roadmap_1.1.4.md](UAT_P2_Roadmap_1.1.4.md) sectie P2.1.

---

### P2.2 – Responsive / mobile-first (Hans & Eric)

| Id | Item | Acceptatie | Bewijs | Status | Opmerking |
|----|------|------------|--------|--------|-----------|
| P2.2.1 | Reports → Sales 375px | Geen horizontale overflow | Playwright: “Reports → Sales at 375px: no horizontal overflow” | Geslaagd | |
| P2.2.2 | Dashboard 375px | Geen horizontale overflow | Playwright: “Dashboard at 375px: no horizontal overflow” | Geslaagd | |
| P2.2.3 | Record Sale 375px | Geen horizontale overflow | Playwright: “Record Sale page at 375px: no horizontal overflow” | Geslaagd | |
| P2.2.4 | Touch targets ≥ 44px | Primaire knoppen min. ~44px hoog | Playwright: “Primary buttons have min touch target (44px)” | Geslaagd | |

**P2.2-bewijs:** `tests/e2e/p2-uat.spec.js` (describe “P2.2 – Responsive / mobile-first”). Handmatig: UAT_P2 sectie P2.2.

---

### P2.3 – Low Stock Alert actionable (Hans & Harry)

| Id | Item | Acceptatie | Bewijs | Status | Opmerking |
|----|------|------------|--------|--------|-----------|
| P2.3.1 | Dashboard: Low Stock stat + Alerts-kaart | “Low Stock Items” en heading “Low Stock Alerts” zichtbaar | Playwright: “Dashboard has Low Stock stat and Alerts card” | Geslaagd | |
| P2.3.2 | View for reorder → Reports → Inventory | Link href bevat `page=msp-reports&tab=inventory` | Playwright: “When low stock: View for reorder links to Reports → Inventory” | Geslaagd | |
| P2.3.3 | Reports → Inventory: sectie + export-knop | Low Stock-sectie en “Export for reorder (CSV)”; download `low-stock-reorder-*.csv` bij items | Playwright: “Reports → Inventory has Low Stock section and export button when items exist” | Geslaagd | |

**P2.3-bewijs:** `tests/e2e/p2-uat.spec.js` (describe “P2.3 – Low Stock Alert actionable”). Handmatig: UAT_P2 sectie P2.3.

---

### P2.4 – Educatieve tooltips (Eric)

| Id | Item | Acceptatie | Bewijs | Status | Opmerking |
|----|------|------------|--------|--------|-----------|
| P2.4.1 | Settings → General: currency-beschrijving | Tekst over EU/US tours en valuta (Euro, USD) zichtbaar | Playwright: “Settings → General: Currency has EU/US description” | Overgeslagen | Onboarding niet voltooid of beschrijving niet gevonden (versie/vertaling) |
| P2.4.2 | Reports → Sales: currency-tooltip | Regel “configured currency” / “Settings → General” zichtbaar | Playwright: “Reports → Sales: currency tooltip text visible” | Overgeslagen | Geen bands of tooltip niet aanwezig |

**P2.4-bewijs:** `tests/e2e/p2-uat.spec.js` (describe “P2.4 – Educatieve tooltips”). Handmatig: UAT_P2 sectie P2.4.

---

## 4. UAT feedback loop (methode naar 100% geslaagd)

Herhaal onderstaande stappen tot alle audit-items status **Geslaagd** hebben.

1. **Voorbereiding**
   - Zorg dat de testomgeving voldoet aan [sectie 2](#2-testomgeving-eisen) (plugin 1.1.4+, onboarding voltooid, min. één band, enz.).
   - Playwright: `BASE_URL=... WP_USER=... WP_PASSWORD=... npm run test:p2`.

2. **Uitvoeren**
   - Playwright-suite draaien; output bewaren (passed/skipped/failed per test).
   - Handmatige UAT voor P0 en P1 volgens [UAT_P0_Failsafe_1.1.3.md](UAT_P0_Failsafe_1.1.3.md) en [UAT_AND_AUDIT_1.0.4_REPORT.md](UAT_AND_AUDIT_1.0.4_REPORT.md).

3. **Rapport**
   - Vul in dit document de **Status** en **Opmerking** per audit-item in op basis van de run.
   - Bij **Overgeslagen** of **Gefaald**: noteer oorzaak (omgeving vs. code).

4. **Acties (feedback)**
   - **Skip/fail door omgeving:** Pas testomgeving aan (versie, onboarding, bands, data) of richt een tweede omgeving in die wél voldoet.
   - **Skip/fail door gedrag:** Bugfix of aanpassing in de plugin; daarna opnieuw testen.

5. **Herhalen**
   - Ga terug naar stap 1 (of stap 2 als alleen code is aangepast) tot alle items **Geslaagd** zijn.

6. **Vastleggen**
   - Bij 100%: zet onderaan dit document datum, omgeving en “Release 1.1.4 audit geslaagd”; vermeld dit eventueel in CHANGELOG of release notes.

Zie voor een korte referentie ook [UAT_FEEDBACK_LOOP.md](UAT_FEEDBACK_LOOP.md).

---

## 5. Resultaat eerste audit-run

**Datum run:** 2026-02-08  
**Omgeving:** `https://merchmanager.local`  
**Playwright-commando:** `BASE_URL=https://merchmanager.local WP_USER=richard WP_PASSWORD='...' npm run test:p2`

**Playwright-resultaat (laatste run):**
- **Passed:** 7
- **Skipped:** 6
- **Failed:** 0
- **Duur:** ~30s

**Per test:**
| Test | Resultaat |
|------|-----------|
| P2.1 – Reports → Sales shows both export buttons | Skipped |
| P2.1 – Export detail link has correct query params and nonce | Skipped |
| P2.1 – Clicking Export detail triggers CSV download | Skipped |
| P2.1 – Clicking Export summary triggers CSV download | Skipped |
| P2.2 – Reports → Sales at 375px: no horizontal overflow | Passed |
| P2.2 – Dashboard at 375px: no horizontal overflow | Passed |
| P2.2 – Record Sale page at 375px: no horizontal overflow | Passed |
| P2.2 – Primary buttons have min touch target (44px) | Passed |
| P2.3 – Dashboard has Low Stock stat and Alerts card | Passed |
| P2.3 – When low stock: View for reorder links to Reports → Inventory | Passed |
| P2.3 – Reports → Inventory has Low Stock section and export button when items exist | Passed |
| P2.4 – Settings → General: Currency has EU/US description | Skipped |
| P2.4 – Reports → Sales: currency tooltip text visible | Skipped |

**Samenvatting:** P2.2 en P2.3 volledig geslaagd; P2.1 en P2.4 overgeslagen wegens ontbrekende detail-export op site, geen download bij summary-export, en ontbrekende currency-tooltip/onboarding. P0 en P1 niet geautomatiseerd; status “Niet getest” tot handmatige UAT is uitgevoerd.

**100% geslaagd:** Nee – meerdere items Overgeslagen of Niet getest. Volg de feedback loop (sectie 4) om de testomgeving en/of code aan te passen en opnieuw te draaien.

---

## 6. Ondertekening bij 100% geslaagd

*(In te vullen wanneer alle audit-items Geslaagd zijn.)*

- **Datum:** …
- **Omgeving:** …
- **Resultaat:** Release 1.1.4 audit 100% geslaagd.
- **Opmerking:** …
