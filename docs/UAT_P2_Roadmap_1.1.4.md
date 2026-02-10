# UAT P2 – Roadmap 1.1.4 (Export, responsive, Low Stock, tooltips)

**Versie:** 1.1.4  
**Datum:** 2026-02-08  
**Doel:** Handmatige acceptatietest voor P2-items op een lokale WordPress-installatie (bijv. merchmanager.local).

---

## Playwright testsuite (geautomatiseerd)

Er is een Playwright-testsuite die dezelfde P2-scenario’s automatisch afdekt.

**Eerste keer:** installeren en browsers installeren:

```bash
npm install
npx playwright install chromium
```

**Tegen merchmanager.local draaien:**

```bash
BASE_URL=https://merchmanager.local WP_USER=admin WP_PASSWORD=jouw_wachtwoord npm run test:p2
```

Of met een `.env` (zie `.env.example`): zet `BASE_URL`, `WP_USER`, `WP_PASSWORD` en run:

```bash
npm run test:p2
```

**Met UI (stap voor stap):**

```bash
BASE_URL=https://merchmanager.local WP_USER=admin WP_PASSWORD=jouw_wachtwoord npm run test:p2:ui
```

Tests staan in `tests/e2e/p2-uat.spec.js` (P2.1 exportknoppen en downloads, P2.2 viewport 375px, P2.3 low stock-links en export, P2.4 tooltipteksten).

**Let op:** De plugin op de testomgeving (bijv. merchmanager.local) moet **versie 1.1.4 of hoger** zijn (inclusief P2.1 "Export detail (Excel)" en overige P2-features). Anders falen de P2.1-tests omdat de detail-exportknop ontbreekt.

**Skip-logica:** De suite faalt niet meer op ontbrekende features; tests worden overgeslagen met een duidelijke reden als:
- Detail-exportknop ontbreekt (plugin &lt; 1.1.4)
- Summary-export triggert geen download (endpoint/data)
- Onboarding niet voltooid (P2.4 Settings)
- Geen bands (P2.4 Reports tooltip) of currency tooltip ontbreekt (plugin/vertaling)

De run eindigt met exit code 0 zolang alle uitgevoerde assertions slagen; overgeslagen tests tellen niet als fout.

---

## Handmatige UAT – Voorbereiding

- WordPress met MerchManager 1.1.4 actief.
- Minimaal één band met merchandise en (optioneel) verkopen en low-stock items voor realistische tests.

---

## P2.1 – Export detail (Excel) – Jim

| Stap | Actie | Verwachting |
|------|--------|--------------|
| 1 | Ga naar **Merchandise Sales → Reports**, tab **Sales**. | Sales report met filters. |
| 2 | Kies band en datumbereik, klik **Apply Filters**. | Totalen en tabellen zichtbaar (tenzij integrity error). |
| 3 | Klik **Export summary to CSV**. | Download: `sales-report-YYYY-MM-DD.csv` met samenvatting (totalen, top merchandise, by payment). |
| 4 | Klik **Export detail (Excel)**. | Download: `sales-detail-YYYY-MM-DD.csv` met **één regel per verkoop** (o.a. datum, band, show, merchandise, quantity, price, payment type). Geschikt voor marge-analyse in Excel. |
| 5 | Open detail-CSV in Excel of editor. | Kolommen kloppen; datumreeks en filters komen overeen met rapport. |

---

## P2.2 – Responsive / mobile-first – Hans & Eric

| Stap | Actie | Verwachting |
|------|--------|--------------|
| 1 | Open **Reports → Sales** in de browser. | Pagina laadt. |
| 2 | Verklein viewport naar **375px** (DevTools of echte telefoon). | Geen horizontaal scrollen; filtervelden en knoppen stapelen; knoppen min. ~44px hoog (makkelijk tappen). |
| 3 | Ga naar **Record Sale** (admin) of een **sales page** (front-end). | Zelfde: geen horizontale scroll; quantity-velden en knoppen groot genoeg om te tappen. |
| 4 | Ga naar **Dashboard**. | Stat boxes en quick links passen; Low Stock-stat en knoppen bruikbaar. |

---

## P2.3 – Low Stock Alert actionable – Hans & Harry

| Stap | Actie | Verwachting |
|------|--------|--------------|
| 1 | Zorg dat er **minimaal één low-stock item** is (voorraad ≤ drempel, > 0). | - |
| 2 | Ga naar **Dashboard**. | Stat **Low Stock Items** heeft gele/waarschuwingsstijl en toont **View for reorder**. Kaart **Low Stock Alerts** toont melding "X item(s) need reordering" en knop **View low stock for reorder**. |
| 3 | Klik **View low stock for reorder** (of **View for reorder** in de stat). | Ga naar **Reports → Inventory**. |
| 4 | Op **Reports → Inventory**: controleer sectie **Low Stock Items**. | Tabel met low-stock items; knop **Export for reorder (CSV)**. |
| 5 | Klik **Export for reorder (CSV)**. | Download: `low-stock-reorder-YYYY-MM-DD.csv` met kolommen Item, SKU, Current Stock, Threshold, Band. |
| 6 | Zet alle voorraad boven de drempel (geen low stock). | Dashboard: geen waarschuwingsstijl; kaart toont "View inventory" i.p.v. "View low stock for reorder". |

---

## P2.4 – Educatieve tooltips – Eric

| Stap | Actie | Verwachting |
|------|--------|--------------|
| 1 | Ga naar **Settings → General**. | Onder **Currency** staat een korte beschrijving: o.a. "EU tours often use Euro (€); US tours use USD ($)." |
| 2 | Ga naar **Reports → Sales**. | Boven of bij de filters staat een regel: "All amounts are shown in your configured currency (Settings → General)." |

---

## Afronding

- Alle stappen groen → P2 UAT geslaagd.
- Bij fouten: noteer stap en gedrag; fix en hertest.
