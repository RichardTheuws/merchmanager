# MerchManager – Ultieme roadmap

**Versie:** 1.0  
**Datum:** Februari 2026  
**Doelgroep:** Ontwikkelteam, WordPress-developers, UI/UX-ontwerpers, producteigenaar  
**Bron:** Expert sessie op basis van Personas Merchsupply en bestaande PRD/UAT-documenten.

---

## 1. Doel en leeswijzer

Dit document is de **ultieme roadmap** voor de verdere ontwikkeling van de MerchManager WordPress-plugin. De roadmap is opgesteld met input van:

- **Product & persona's:** Vertaling van de vier Merchsupply-persona's naar functionele behoeften (zie sectie 3).
- **WordPress:** Aansluiting bij WordPress Coding Standards, Plugin Handbook, performance en onderhoudbaarheid.
- **UI/UX:** Mobile-first, toegankelijkheid (WCAG 2.1 AA), duidelijke feedback en geen overbodige features.

**Leidende principes:** Less is more; geen bloat; alles moet zeer goed functioneel zijn; belangrijke weergegeven informatie moet een failsafe hebben.

**Leeswijzer:**

- **Sectie 2:** Principes en randvoorwaarden.
- **Sectie 3:** Persona's en vertaling naar behoeften (basis voor prioritering).
- **Sectie 4:** Data-integriteit en failsafe (verplicht voor betrouwbare rapporten).
- **Sectie 5:** Roadmap in prioriteit (P0 → P1 → P2).
- **Sectie 6:** Expliciet uit scope.
- **Sectie 7:** Verwijzingen naar andere documenten.

---

## 2. Principes en randvoorwaarden

### 2.1 Principes

| Principe | Toelichting |
|----------|-------------|
| **Less is more** | Alleen verbeteringen die direct bijdragen aan betrouwbaarheid, gebruikservaring of persona-doelen. Geen bloated features. |
| **Alles zeer goed functioneel** | Elke feature moet robuust zijn, edge cases afgehandeld, en getest (handmatig en waar mogelijk geautomatiseerd). |
| **Failsafe voor weergave** | Getoonde cijfers (rapporten, totalen, voorraad) moeten verifieerbaar zijn; bij inconsistentie geen foute waarden tonen maar duidelijke foutmelding (zie sectie 4). |
| **WordPress best practices** | Code voldoet aan [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/), veilige escaping/sanitization, geen deprecated APIs; Plugin Check (PCP) zonder critical issues. |
| **UI/UX: duidelijk en toegankelijk** | Mobile-first waar gebruikers op telefoon werken (Hans, Eric); duidelijke empty states, foutmeldingen en CTAs; WCAG 2.1 AA waar van toepassing. |

### 2.2 Randvoorwaarden

- Bestaande functionaliteit (bands, tours, shows, merchandise, sales, reports, sales pages, stock) blijft de kern; de roadmap voegt **verbeteringen** en **failsafe** toe, geen parallelle systemen.
- Geen vaste release-datums in dit document; volgorde is **eerst P0, dan P1, dan P2**.
- Wijzigingen in datamodel of breaking changes worden expliciet gedocumenteerd (o.a. in CHANGELOG en database_schema).

---

## 3. Persona's en functionele behoeften

De roadmap is gebaseerd op de vier Merchsupply-persona's. Elke P2-item is gekoppeld aan één of meer persona's.

### 3.1 De vier persona's

| Persona | Kenmerken | Gedrag / Ervaring | Focus |
|--------|-----------|-------------------|--------|
| **Harry Jensen (52)** – De Ervaren Bandleider | Methodisch, zoekt zekerheid en expertise. | 30 jaar bandervaring, tientallen tours EU. Regelt alles zelf; duidelijk beeld van oplages; begrijpt complexe EU-regels (o.a. Zwitserland/Noorwegen). | Merch = hoofdinkomen; prijsbewust maar kiest voor kwaliteit. |
| **Hans VonDurscht (38)** – Freelance Tourmanager | Competitief, zoekt directe oplossing. | Volledig mobiel ("altijd onderweg"), kent de markt, nieuw bij Merchsupply. Geen emotionele band met artiest; zakelijke transactie voor de duur van de tour. | Snelheid en spoed cruciaal. |
| **Eric McDouglas (24)** – De Jonge Strategist | Methodisch, zoekt langetermijnpartner. | Eerste trip naar Europa, onbekend met markt en verkoopverwachtingen. Native op social media/internet; doet alles via telefoon. | Groei; vijfjarenplan voor zijn band. |
| **Jim Johnson (35)** – De Zakelijke Manager | Competitief, focus op maximale marge. | Managet diverse artiesten. Wil ontzorgd worden ("zo min mogelijk zelf doen"). | Vakmanschap en topkwaliteit; wil vooraf exact de winstmarges weten. |

### 3.2 Vertaling naar functionele behoeften (less is more)

**A. Gebruiksvriendelijkheid & interface (Hans & Eric)**

- **Mobile-first:** Responsive admin en sales-recording die op telefoon goed bruikbaar zijn (geen aparte native app). Focus: voorraadbeheer en snelle verkoop-invoer na een show.
- **Onboarding:** Educatieve tooltips waar relevant (bijv. EU BTW, valuta $ → €) voor gebruikers die de EU-markt niet kennen (Eric).

**B. Rapportage & financiën (Jim & Harry)**

- **Jim – marge:** Export van verkoopcijfers naar Excel (of CSV geschikt voor Excel) met alle nodige velden (omzet, aantallen, per item/band/tour/periode) zodat Jim marges buiten de plugin kan berekenen. Geen volwaardige marge-calculator mét drukkosten/venue-fees in de plugin (bloat).
- **Harry – zekerheid:** Low Stock Alert die tot actie leidt; optioneel eenvoudige indicatie op basis van verkoopsnelheid ("overweeg bijbestellen") zonder zware voorspellingsmodule.

**C. Logistiek & spoed (Hans)**

- **Panic Button / Low Stock Alert:** Duidelijk wanneer voorraad kritiek laag is; waarschuwing + directe actie (CTA): bijv. export of overzicht voor herbestelling bij drukker.

### 3.3 Voorbeeld user stories

- **Jim:** Als Zakelijke Manager wil ik verkoopcijfers naar Excel kunnen exporteren, zodat ik artiesten precies kan laten zien wat ze verdiend hebben zonder zelf handmatige berekeningen te doen.
- **Hans:** Als Tourmanager wil ik op mijn telefoon snel een verkoop kunnen invoeren na een show en een duidelijke waarschuwing zien wanneer de voorraad kritiek laag is, met een snelle actie om bij te bestellen.
- **Harry:** Als Ervaren Bandleider wil ik in één oogopslag zien wanneer een item bijna op is en een overzicht kunnen gebruiken om bij te bestellen, zodat ik nooit zonder voorraad kom.
- **Eric:** Als Jonge Strategist wil ik korte uitleg zien bij velden die met EU/valuta te maken hebben, zodat ik geen fouten maak bij mijn eerste Europese tour.

---

## 4. Data-integriteit en failsafe

Omdat de plugin met belangrijke financiële en voorraadgegevens werkt, moet de **weergegeven informatie correct** zijn. Onderstaande aanpak is verplicht voor rapporten en overzichten.

### 4.1 Doel

- Geen stille fouten: getoonde totalen mogen niet afwijken van de onderliggende transacties zonder dat de gebruiker wordt gewaarschuwd.
- Bij twijfel: geen foute waarde tonen, wel duidelijke foutmelding en optioneel log voor beheer.

### 4.2 Aanpak

1. **Single source of truth**  
   Rapporten blijven uitsluitend afgeleid uit dezelfde bron (sales-/stock-tabellen) via bestaande services. Geen aparte cache-totalen die kunnen afwijken. Eventuele performance-optimalisaties (bijv. transients) alleen als ze herleidbaar zijn en meedoen in de reconciliatie-check.

2. **Reconciliatie-check (nieuw)**  
   - **Plaats:** Na berekening van rapporttotalen (bijv. in `Merchmanager_Report_Service` of een kleine helper), vóór teruggeven naar de UI.  
   - **Logica:** Voor dezelfde filter (band, periode, etc.):  
     - Berekend totaal (omzet, aantal verkopen) uit de rapportquery.  
     - Zelfde totaal via een eenvoudige aggregatie op de ruwe sales-data (bijv. `SUM(quantity*price)`, `COUNT(*)`).  
     - Als verschil groter dan drempel (bijv. 0,01 voor bedragen, 0 voor aantallen): **niet** de mogelijk foute waarde tonen; in plaats daarvan duidelijke melding ("Data consistency check failed – please try again or contact support") en eventueel loggen.  
   - **Scope:** Minimaal voor de belangrijkste rapporten (sales summary, revenue totalen, tour/band totalen). Stock-overzichten: vergelijk getoonde totalen met actuele voorraad uit de stock-logica.

3. **Traceerbaarheid (light)**  
   Waar mogelijk: getoonde totalen koppelen aan onderliggende records (bijv. "dit totaal is gebaseerd op X verkopen" of export/drill-down). Geen grote nieuwe UI; wel voldoende voor support/beheer om te verifiëren.

4. **Geen stille correcties**  
   Bij afwijking: geen automatische "fix" van getoonde cijfers; wel blokkeren van onbetrouwbare weergave + duidelijke foutmelding + optioneel log.

### 4.3 Technische richtlijn (WordPress)

Implementatie kan in één kleine laag (bijv. class `Merchmanager_Report_Integrity` of methodes in de bestaande Report Service) die rapport-uitvoer langs dezelfde check stuurt. Geen wijziging in bestaande post types of custom tables voor alleen deze check; wel herbruikbare validatie voor alle rapport-endpoints.

---

## 5. Roadmap (prioriteit P0 → P1 → P2)

### 5.1 P0 – Failsafe (data-integriteit)

**Doel:** Garantie dat getoonde rapport- en voorraadcijfers kloppen.

| Taak | Beschrijving | Acceptatie |
|------|--------------|------------|
| Reconciliatie rapporten | Reconciliatie-check implementeren voor sales summary en revenue totalen (zelfde filter: vergelijk geaggregeerde waarde met ruwe sales-data). | Bij afwijking > drempel wordt geen totaal getoond maar een duidelijke foutmelding. |
| Foutmelding en logging | Bij falen van de check: gebruikersvriendelijke melding in de UI; optioneel log (bijv. `error_log` of WordPress debug log) voor beheer. | Melding is begrijpelijk; beheer kan incidenten terugvinden in logs. |
| Geen stille correcties | Geen automatische correctie van afwijkende totalen; alleen blokkeren + melding. | Geen code die totalen "repareert" zonder gebruiker te informeren. |

**WordPress:** Geen nieuwe DB-tabellen voor alleen deze check; bestaande Report Service en sales-queries hergebruiken.  
**UI/UX:** Foutmelding kort en actiegericht (bijv. "Probeer opnieuw of neem contact op met support").

---

### 5.2 P1 – Betrouwbaarheid (bestaande flows)

**Doel:** Bestaande functionaliteit robuuster maken; geen nieuwe features.

| Taak | Beschrijving | Acceptatie |
|------|--------------|------------|
| Edge cases UAT 1.0.4 | Bekende edge cases uit [UAT_AND_AUDIT_1.0.4_REPORT.md](UAT_AND_AUDIT_1.0.4_REPORT.md) aanpakken (bijv. sales page filter, edit tour/merchandise). | Geen wit scherm of onverwacht gedrag in beschreven scenario's. |
| Plugin Check (PCP) | Resteren van waarschuwingen uit [PLUGIN_CHECK_REPORT_2026-02-08.md](PLUGIN_CHECK_REPORT_2026-02-08.md) waar relevant (nonce, unslash, session, etc.). | Geen critical/blocker issues; documenteer bewuste uitzonderingen. |
| Validatie en escaping | Controleren dat alle gebruikersinput gesanitized/geëscaped wordt volgens WordPress standards; geen `echo` van unescaped data. | Geen nieuwe PHPCS/Plugin Check waarschuwingen voor security. |

**WordPress:** Volgen [Plugin Handbook](https://developer.wordpress.org/plugins/) en Coding Standards.  
**UI/UX:** Bestaande flows (o.a. Record sale, Reports, Dashboard) blijven bruikbaar; foutmeldingen duidelijker waar nodig.

---

### 5.3 P2 – Verbeteringen voor persona's

Elk item is gekoppeld aan één of meer persona's (sectie 3). Volgorde binnen P2 kan worden aangepast op basis van capaciteit.

#### P2.1 Export verkoopcijfers naar Excel (Jim)

- **Doel:** Jim kan verkoopcijfers exporteren met alle velden nodig voor margeberekening (omzet, aantallen, per item/band/tour/periode, betaalmethode, etc.) zonder handmatig te hoeven rekenen in de plugin.
- **Implementatie:** CSV-export (Excel-compatibel) of directe Excel-export vanuit Reports, met filter op band/periode/tour. Geen marge-calculator in de plugin (geen drukkosten/venue-fees in core).
- **Acceptatie:** Export bevat minstens: datum, band, tour/show, merchandise, quantity, price, payment type, totaal; gebruiker kan in Excel marges berekenen.
- **Persona:** Jim (Zakelijke Manager).

#### P2.2 Responsive / mobile-first admin en sales-recording (Hans & Eric)

- **Doel:** Admin (voorraad, overzichten) en sales-recording flow goed bruikbaar op telefoon; snelle invoer na een show.
- **Implementatie:** Responsive layout (breakpoints, touch-vriendelijke knoppen, geen te kleine targets); prioriteit voor Sales-recording en Low Stock-overzicht op small screens.
- **Acceptatie:** Kritieke flows (één verkoop invoeren, voorraad zien, low stock zien) zijn op een 375px-brede viewport bruikbaar zonder horizontaal scrollen of onbereikbare knoppen.
- **Persona's:** Hans (Tourmanager), Eric (Jonge Strategist).  
- **UI/UX:** Mobile-first; tap targets min. 44×44px; WCAG 2.1 AA waar van toepassing.

#### P2.3 Low Stock Alert actionable (Hans & Harry)

- **Doel:** Duidelijk wanneer voorraad kritiek laag is; directe actie mogelijk (export of overzicht voor herbestelling bij drukker).
- **Implementatie:** Bestaande "Low Stock" op Dashboard uitbreiden: duidelijke waarschuwing + CTA (bijv. "Exporteer overzicht voor herbestelling" of link naar gefilterde merchandise-lijst met lage voorraad). Optioneel: drempel instelbaar (bijv. per item of globaal).
- **Acceptatie:** Gebruiker ziet bij kritieke voorraad een duidelijke waarschuwing en kan in één of twee stappen een bruikbaar overzicht voor herbestelling krijgen.
- **Persona's:** Hans (spoed), Harry (zekerheid, nooit zonder voorraad).

#### P2.4 Educatieve tooltips (Eric)

- **Doel:** Korte uitleg bij velden die met EU/valuta/btw te maken hebben, voor gebruikers die de EU-markt niet kennen.
- **Implementatie:** Lichte tooltips of help-tekst (bijv. bij valuta-instelling, of bij eerste gebruik van Reports) over EU BTW of $ → €. Geen zware onboarding-wizard; alleen waar het echt helpt.
- **Acceptatie:** Op minstens één relevante plek (bijv. Settings of eerste Report) is korte uitleg beschikbaar; geen bloat in de rest van de UI.
- **Persona:** Eric (Jonge Strategist).

---

## 6. Expliciet uit scope

De volgende onderwerpen vallen **niet** binnen deze roadmap (tenzij productbesluit anders):

- Aparte **native mobile app** (wel: responsive admin/sales in de browser).
- **Community forum**, extension marketplace of uitgebreide **extension ecosystem**.
- **WooCommerce-** of **Shopify-integratie**.
- **Multi-warehouse** of complexe voorraadlocaties.
- **Volwaardige marge-calculator** in de plugin (drukkosten, venue-fees, belastingen in core); wel Excel-export voor margeberekening buiten de plugin.
- **Zware voorraadvoorspellingsmodule** (ML of complexe modellen); wel eenvoudige "reorder suggestie" of indicatie op basis van verkoopsnelheid waar haalbaar.
- **Complexe tour/festival-features** (multi-band tours, festival-specifieke logica) tot nader order.

---

## 7. Verwijzingen

| Document | Gebruik |
|----------|--------|
| [prd.md](prd.md) | Product scope, rollen, huidige features. |
| [development_roadmap.md](development_roadmap.md) | Legacy roadmap; vervangen door dit document voor prioritering. |
| [UAT_AND_AUDIT_1.0.4_REPORT.md](UAT_AND_AUDIT_1.0.4_REPORT.md) | Edge cases en testscenario's voor P1. |
| [PLUGIN_CHECK_REPORT_2026-02-08.md](PLUGIN_CHECK_REPORT_2026-02-08.md) | Plugin Check bevindingen voor P1. |
| [database_schema.md](database_schema.md) | Datamodel bij wijzigingen. |
| [Personas Merchsupply.pdf](Personas%20Merchsupply.pdf) | Bron persona's (inhoud verwerkt in sectie 3). |

---

*Laatste update: februari 2026. Wijzigingen in deze roadmap worden in het document bijgewerkt en waar relevant in CHANGELOG vermeld.*
