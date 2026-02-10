# UAT P0 Failsafe (v1.1.3)

**Doel:** Verifiëren dat de report-integrity check werkt en dat sales totalen correct worden getoond.

**Omgeving:** Local (merchmanager.local) of staging met demo data.

---

## 1. Normale rapportweergave (totalen kloppen)

1. Ga naar **MerchManager → Reports**, tab **Sales**.
2. Kies een band en datumbereik waarvoor je sales hebt (bijv. na "Load demo data": All Bands, deze maand).
3. **Verwacht:** Total Sales, Total Quantity en Total Revenue worden getoond; geen rode foutmelding.
4. **Controle:** Wijzig het filter (andere band of periode) en klik Apply. Totalen moeten logisch overeenkomen met de filters (bijv. "All Bands" ≥ totaal van één band).

---

## 2. Failsafe-melding (alleen indien er ooit een mismatch zou zijn)

De integriteitscheck vergelijkt de getoonde totalen met een tweede query. Bij een mismatch toont de plugin **geen** totalen maar een foutmelding.

- **Normale situatie:** Je ziet geen foutmelding; totalen worden getoond (zoals in stap 1).
- **Als je de foutmelding wilt testen** (optioneel, voor ontwikkelaars): tijdelijk in `class-merchmanager-report-service.php` in `generate_sales_report` na de berekening van `$total_sales` bijvoorbeeld `$total_sales = $total_sales + 1;` toevoegen, pagina verversen. Je zou dan de rode melding "Data consistency check failed. Please try again or contact support." moeten zien en **geen** totalen/tabellen. Daarna de testwijziging ongedaan maken.

---

## 3. CSV-export

1. Op dezelfde Reports → Sales-pagina, klik **Export to CSV**.
2. **Verwacht:** Een CSV wordt gedownload met sales-data (of lege/header-only als er geen sales zijn).
3. Bij een integrity-fout (zie stap 2) zou de export moeten worden afgebroken met dezelfde foutmelding in plaats van een lege/onjuiste CSV.

---

## 4. Geen regressie

- Dashboard: Total Sales en Total Revenue in de stat-blokken blijven kloppen (zij gebruiken geen report-service maar direct get_sales_summary).
- Andere Reports-tabs (Inventory, Stock History, Tour Report) blijven werken.

---

**Resultaat:** Alle stappen OK = P0 UAT geslaagd.
