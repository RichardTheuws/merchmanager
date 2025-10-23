---
description: Generate or refresh the master POT file for translations and commit it.
---

## Steps
1. Run:  
   `wp i18n make-pot ./ ./languages/{slug}.pot --exclude=node_modules,vendor,tests`
2. If a `languages` folder is missing, create it.
3. Stage and commit changes:  
   `git add languages/{slug}.pot && git commit -m "i18n: update POT"`
4. Inform the user of new/removed strings; suggest pushing to translate.w.org.