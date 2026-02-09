# Plugin Check (PCP) – Rapport Merchandise Sales Plugin

**Datum**: 8 februari 2026  
**Omgeving**: merchmanager.local (Local by Flywheel), Plugin Check (PCP) 1.8.0  
**Plugin**: Merchandise Sales Plugin (merchmanager), versie op site: 1.0.4  

---

## Resultaat

**Checks complete. Errors were found.**

De check is uitgevoerd via **Tools → Plugin Check** na installatie en activatie van [Plugin Check (PCP)](https://wordpress.org/plugins/plugin-check/). Categorie **Plugin Repo** was geselecteerd; **Error** en **Warning** waren aangevinkt.

---

## Gevonden issues (samenvatting)

De meerderheid van de **Errors** komt uit de WordPress Coding Standards voor **internationalisatie (i18n)**:

### 1. WordPress.WP.I18n.MissingTranslatorsComment (ERROR)

- **Betekenis**: Een aanroep van `__()` (of vergelijkbaar) met placeholders in de tekst heeft een **"translators:"** comment nodig op de regel erboven, om de betekenis van de placeholders voor vertalers te verduidelijken.
- **Voorbeeldbestanden**: o.a.  
  `includes/models/class-merchmanager-tour.php`,  
  `includes/services/class-merchmanager-sales-service.php`,  
  `includes/services/class-merchmanager-report-service.php`,  
  `includes/services/class-merchmanager-stock-service.php`,  
  `includes/services/class-merchmanager-sales-recording-service.php`,  
  en diverse admin/public partials en meta-boxes.

### 2. WordPress.WP.I18n.UnorderedPlaceholdersText (ERROR)

- **Betekenis**: Placeholders in vertaalbare strings moeten **genummerd** zijn zodat de volgorde in vertalingen correct blijft (bijv. `%1$d, %2$s` in plaats van `%d, %s`).
- **Voorbeelden uit de check**:
  - `class-merchmanager-tour.php` (o.a. regel 463):  
    `'Import completed: %d shows imported, %d skipped.'` → gebruik `%1$d` en `%2$d`.  
  - Idem (o.a. regel 552):  
    `'Export completed: %d shows exported to %s.'` → `%1$d, %2$s`.  
  - `class-merchmanager-sales-service.php` (o.a. regels 512, 601):  
    o.a. `'Export completed: %d sales exported to %s.'`, `'Stock change due to %s (ID: %d)'` → genummerde placeholders.

Er zijn meer van dit type op andere regels en in andere bestanden; de volledige lijst staat in de Plugin Check UI (**Tools → Plugin Check**).

---

## Bestanden die alleen in dev zitten (niet in release-ZIP)

De check is gedaan op de **volledige pluginmap** op Local (inclusief dev-bestanden). Onder andere de volgende bestanden/mappen zitten **niet** in de WordPress.org release-ZIP (zie `scripts/build-wp-org-zip.sh`):

- `.cursor`, `.github`, `.editorconfig`, `.stylelintrc.js`, `.gitattributes`, `.gitignore`
- `tests/`, `docs/`, `src/` (alleen `build/` gaat mee)
- `composer.json`, `composer.lock`, `package.json`, `package-lock.json`
- `ROADMAP.md`, `IMPROVEMENTS.md`, etc.

Events/warnings over deze bestanden gelden dus niet voor de ZIP die je naar WordPress.org uploadt. Wel blijven alle **plugin-PHP-bestanden** (includes, admin, public) onder de check vallen.

---

## Aanbevolen vervolgstappen

1. **Errors oplossen** (vooral Plugin Repo-categorie):
   - Bij elke `__()` / `_e()` etc. met placeholders: direct boven de regel een **translators:**-comment toevoegen.
   - Alle placeholders in vertaalbare strings **nummeren** (`%1$d`, `%2$s`, …).
2. **Plugin Check opnieuw draaien** na wijzigingen (Tools → Plugin Check → Check it!).
3. **Release-ZIP opnieuw bouwen** met `./scripts/build-wp-org-zip.sh <versie>` en deze ZIP gebruiken voor indiening/updates.

---

## Referentie

- [Plugin Check (PCP) op WordPress.org](https://wordpress.org/plugins/plugin-check/)
- [How to Internationalize Your Plugin (placeholders, translators comment)](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/)
- [WORDPRESS_ORG_PUBLISHING.md](./WORDPRESS_ORG_PUBLISHING.md) in deze repo voor de volledige publicatiestappen inclusief Plugin Check.
