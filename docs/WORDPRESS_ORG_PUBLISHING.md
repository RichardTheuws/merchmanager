# MerchManager – Publiceren op WordPress.org

**Belangrijk: bewaar je WordPress.org inloggegevens nooit in dit project, in bestanden die naar GitHub gaan, of in de plugin zelf.**

---

## Plugin Check (verplicht vóór indienen)

Voer **Plugin Check (PCP)** uit voordat je indient. Dit is hetzelfde hulpmiddel dat de directory gebruikt en helpt fouten te vinden.

- **Plugin**: [Plugin Check (PCP)](https://wordpress.org/plugins/plugin-check/) op WordPress.org
- **Installatie**: Op je lokale WordPress (bijv. merchmanager.local): **Plugins → Add New** → zoek “Plugin Check” → **Install Now** → **Activate**
- **Uitvoeren**: **Tools → Plugin Check** → kies “Merchandise Sales Plugin” → **Check it!**
- **Richtlijn**: Voor goedkeuring moet de plugin doorgaans alle checks in de categorie **Plugin Repo** doorstaan. Los eerst **Errors** op, daarna **Warnings** waar mogelijk.

Zie ook: [Plugin Check op WordPress.org](https://wordpress.org/plugins/plugin-check/).

---

## Eerste keer: plugin indienen

1. **Log in** op [WordPress.org](https://login.wordpress.org/) (niet in dit bestand; gebruik je browser).
2. Ga naar **Add your plugin**: https://wordpress.org/plugins/developers/add/
3. Lees de [Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/).
4. **Voer Plugin Check uit** (zie sectie hierboven) en los kritieke errors op.
5. **Upload de plugin-ZIP**:
   - **Max. 10 MB** (WordPress.org limiet). Gebruik het release-ZIP dat **geen** `vendor/`, tests of dev-bestanden bevat.
   - Bouw de ZIP met:  
     `./scripts/build-wp-org-zip.sh <versie>`  
     (bijv. `1.0.5`). Output: `merchmanager-<versie>.zip` in de projectroot.
   - De ZIP moet de map `merchmanager/` als root hebben met daarin alle pluginbestanden (geen dubbele map).
6. Verstuur het formulier. De pluginnaam in `merchmanager.php` bepaalt de slug (bijv. `merchandise-sales-plugin`).
7. **Review**: duur vaak 1–10 werkdagen. Na goedkeuring krijg je toegang tot het SVN-repository.

---

## Na goedkeuring: updates via SVN

WordPress.org gebruikt **Subversion (SVN)**. Gebruik je **WordPress.org-gebruikersnaam** en **wachtwoord alleen wanneer SVN erom vraagt** – typ ze in de terminal, zet ze nooit in een bestand of in git.

### SVN-wachtwoord

- Soms wordt een apart **Application Password** voor SVN gebruikt. Controleer je [WordPress.org-profiel](https://login.wordpress.org/me/profile/) → “Application Passwords” of “Security” of de e-mail die je na goedkeuring kreeg.

### Eerste checkout (eenmalig)

```bash
# Maak een werkmap buiten de Git-repo (bijv. op je Bureaublad of in ~/wp-svn)
mkdir -p ~/wp-svn
cd ~/wp-svn

# Checkout het plugin-SVN-repo (vervang SLUG door de slug die WordPress.org je gaf, bijv. merchmanager of merchandise-sales-plugin)
svn co https://plugins.svn.wordpress.org/SLUG SLUG
cd SLUG
```

Je wordt gevraagd om **username** en **password** – vul ze in; ze worden niet opgeslagen in bestanden.

### Bestanden naar trunk zetten

```bash
cd ~/wp-svn/SLUG

# Kopieer pluginbestanden naar trunk (zelfde excludes als release-ZIP: geen vendor, tests, .git, etc.)
# Optie A: gebruik de inhoud van de gebouwde ZIP (aanbevolen – blijft onder 10 MB):
unzip -o merchmanager-<versie>.zip -d /tmp/wp-plugin
rsync -av /tmp/wp-plugin/merchmanager/ trunk/

# Optie B: rsync direct uit het project (exclude vendor en dev-mappen):
rsync -av --exclude='.git' --exclude='.cursor' --exclude='node_modules' --exclude='vendor' \
  --exclude='build-zip' --exclude='*.zip' --exclude='tests' --exclude='docs' --exclude='.github' \
  /Users/richardtheuws/Documents/Theuws-Consulting/merch-manager/ trunk/

# Let op: bestanden moeten direct in trunk/ staan:
# trunk/merchmanager.php, trunk/readme.txt, trunk/includes/, etc.
# NIET trunk/merchmanager/merchmanager.php
```

### Nieuwe bestanden toevoegen en committen

```bash
cd ~/wp-svn/SLUG
svn add trunk/*
svn status   # controleer wat er geadd is
svn ci -m "Initial upload" --username merchandisenl
# Wachtwoord wordt nu gevraagd – typ het in, niet in een bestand zetten
```

### Versie taggen (bij elke release)

```bash
cd ~/wp-svn/SLUG
svn up

# Kopieer trunk naar tags/1.0.4 (gebruik het versienummer uit merchmanager.php en readme.txt)
svn cp trunk tags/<versie>

# Commit
svn ci -m "Tagging version <versie>" --username merchandisenl
```

Controleer dat in **trunk/readme.txt** het veld `Stable tag:` overeenkomt met de nieuwe tag (bijv. `1.0.5`).

---

## Samenvatting veiligheid

| Wat | Doen |
|-----|-----|
| WordPress.org wachtwoord | Alleen intypen wanneer SVN of de browser erom vragen; nooit in bestanden of git. |
| GitHub / repo | Geen wachtwoorden, geen .env met WordPress.org-credentials in de repo. |
| CI/CD (GitHub Actions) | Gebruik **GitHub Secrets** (bijv. `SVN_PASSWORD`) voor SVN; nooit in workflow-bestanden zetten. |

---

## Handige links

- [Using Subversion (Plugin Handbook)](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
- [Plugin readme.txt](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/)
- [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
