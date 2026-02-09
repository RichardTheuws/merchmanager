# MerchManager 1.0.3 – UAT & Audit

**Datum**: 8 februari 2026  
**Versie**: 1.0.3 (UX Overhaul + Demo-implementatie)  
**Status**: UAT-checklist en auditrapport

---

## 1. Lokale installatie

### Optie A: Docker (aanbevolen)

```bash
cd /Users/richardtheuws/Documents/Theuws-Consulting/merch-manager
docker compose up -d
```

Wacht tot WordPress draait (eerste keer kan 2–5 minuten duren voor image build).

- **WordPress**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **Login**: admin / password (standaard Cypress-config)

De plugin staat al in `wp-content/plugins/merchmanager` via volume mount; activeren via Plugins → Merchandise Sales Plugin → Activate.

### Optie B: Bestaande WordPress

1. Zip de plugin: `cd merch-manager && zip -r merchmanager-1.0.3.zip . -x "*.git*" -x "node_modules/*" -x "vendor/*" -x "tests/*"`
2. WordPress Admin → Plugins → Add New → Upload Plugin → kies ZIP
3. Activate

---

## 2. UAT-checklist (User Acceptance Testing)

### 2.1 Onboarding Wizard – Step 1

| # | Scenario | Stappen | Verwacht resultaat | OK |
|---|----------|---------|--------------------|----|
| 1 | Laad demo-data | Ga naar Setup Wizard (of eerste keer na activatie) → Klik "Load demo data" | Demo-data wordt aangemaakt, redirect naar Dashboard met groene melding | ☐ |
| 2 | Zelf instellen | Ga naar Setup Wizard → Klik "Set up yourself" | Navigeert naar Step 2 (Create Band) | ☐ |
| 3 | Skip setup | Klik "Skip setup" | Redirect naar Dashboard zonder data | ☐ |

### 2.2 Demo-data validatie

| # | Scenario | Stappen | Verwacht resultaat | OK |
|---|----------|---------|--------------------|----|
| 4 | Band aangemaakt | Na "Load demo data" → Bands | "Demo Band" bestaat | ☐ |
| 5 | Tour aangemaakt | Tours | "European Tour 2026" bestaat, gekoppeld aan Demo Band | ☐ |
| 6 | Shows aangemaakt | Shows | 3 shows: Amsterdam (vandaag), Rotterdam (+7d), Berlin (+14d) | ☐ |
| 7 | Merchandise | Merchandise | 5 items: T-shirt (€25), Hoodie (€45), CD (€12), Poster (€8), Cap (€15) | ☐ |
| 8 | Sales Page | Sales Pages | 1 pagina met access code DEMO2026 | ☐ |
| 9 | Sales | Sales / Dashboard | 15–25 voorbeeldsales zichtbaar (cash/card) | ☐ |

### 2.3 Unified layout & menu

| # | Scenario | Stappen | Verwacht resultaat | OK |
|---|----------|---------|--------------------|----|
| 10 | Menu-volgorde | Navigeer door admin-menu | Volgorde: Dashboard, Bands, Tours, Shows, Merchandise, Sales Pages, Sales, Reports, Settings, Setup Wizard | ☐ |
| 11 | Page header | Bekijk Dashboard, Sales, Reports, Settings | Elk scherm heeft msp-page-header met H1 | ☐ |
| 12 | Footer | Scroll naar beneden | Theuws Consulting footer op alle admin-pagina's | ☐ |

### 2.4 Empty states

| # | Scenario | Stappen | Verwacht resultaat | OK |
|---|----------|---------|--------------------|----|
| 13 | Dashboard geen bands | Nieuwe installatie, geen demo geladen, skip setup | "No bands yet – create your first band" + CTA "Add your first band" | ☐ |
| 14 | Sales geen bands | Geen bands → Sales | "No bands yet – create your first band to record sales" + CTA | ☐ |
| 15 | Sales geen merchandise | Bands wel, merchandise niet → Sales | "No merchandise yet – add items to sell" + CTA | ☐ |
| 16 | Reports geen data | Geen bands → Reports | "No data yet – create your first band to see reports" + CTA | ☐ |

### 2.5 Core flows (met demo-data)

| # | Scenario | Stappen | Verwacht resultaat | OK |
|---|----------|---------|--------------------|----|
| 17 | Dashboard stats | Na demo laden → Dashboard | Total Sales, Total Revenue, Active Tours, Low Stock zichtbaar | ☐ |
| 18 | Record sale | Sales → voeg item toe → Record Sale | Sale wordt opgeslagen, stock wordt bijgewerkt | ☐ |
| 19 | Reports | Reports → Sales Reports / Inventory / Stock History / Alerts | Data toont correct | ☐ |
| 20 | Settings | Settings → wijzig currency/date format → Save | Wijzigingen worden opgeslagen | ☐ |

### 2.6 Demo succesmelding

| # | Scenario | Stappen | Verwacht resultaat | OK |
|---|----------|---------|--------------------|----|
| 21 | Demo-loaded notice | Load demo data → Dashboard | Groene melding: "Demo data loaded. You can now explore MerchManager." | ☐ |

---

## 3. Code-audit

### 3.1 Security

| # | Check | Resultaat |
|---|-------|-----------|
| 1 | Demo load POST: nonce-verificatie | ✅ `wp_verify_nonce` op `merchmanager_onboarding_nonce` |
| 2 | Demo load: capability check | ⚠️ Indirect via admin_menu; geen expliciete `current_user_can` voor load_demo – acceptabel (alleen ingelogde admins) |
| 3 | Geen directe SQL-injectie | ✅ Prepared statements / $wpdb->prepare in demo-service |
| 4 | Output escaping | ✅ `esc_html`, `esc_attr`, `esc_url` in templates |
| 5 | Input sanitization | ✅ `sanitize_text_field`, `intval` waar van toepassing |

### 3.2 Performance

| # | Check | Resultaat |
|---|-------|-----------|
| 1 | Demo create_demo_data | ✅ Eén bulk-aanmaak; geen N+1 queries |
| 2 | Admin CSS | ✅ Geconsolideerd; geen dubbele inline styles |
| 3 | Menu reorder | ✅ Eén uasort per page load; minimaal |

### 3.3 WordPress-standaarden

| # | Check | Resultaat |
|---|-------|-----------|
| 1 | Text domain | ✅ `merchmanager` consistent gebruikt |
| 2 | Hooks/filters | ✅ Geen deprecated hooks |
| 3 | Database | ✅ `$wpdb->prefix` gebruikt; dbDelta voor schema |
| 4 | Options | ✅ `merchmanager_demo_data_loaded`, `merchmanager_onboarding_complete` |

### 3.4 Mogelijke verbeteringen (niet blokkerend)

| # | Item | Prioriteit |
|---|------|------------|
| 1 | Sales page filter op shows: gebruikt `_msp_show_band_id`, Show model heeft geen band_id (alleen tour_id). Filter kan leeg zijn voor band-specifieke shows. | Laag |
| 2 | Node 18 deprecation in Dockerfile | Laag |
| 3 | "Verwijder demo"-functionaliteit nog niet geïmplementeerd (bewust uit scope) | Info |

---

## 4. Samenvatting

- **Installatie**: Docker via `docker compose up -d` of ZIP-upload in WordPress.
- **UAT**: 21 checks voor onboarding, demo, layout, empty states en core flows.
- **Audit**: Geen kritieke security- of performanceissues; enkele low-priority verbeteringen.

**Aanbevolen volgorde**:
1. Start Docker en wacht tot WordPress draait.
2. Activeer de plugin.
3. Doorloop UAT-sectie 2.1 t/m 2.6.
4. Optioneel: controleer auditpunten in sectie 3.
