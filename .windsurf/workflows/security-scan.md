---
description: Run a full vulnerability audit against WordPress core, plugins and themes using WPScan CLI, wp-sec, and the 10up WP-CLI Vulnerability Scanner.
---

## Steps
1. If the WPScan gem is missing, install it:  
   `gem install wpscan`  # requires Ruby ≥ 2.6  (see docs)
2. Run:  
   `wpscan --url={SITE_URL} --no-update --format=json --output=./reports/wpscan.json`
3. Run wp-sec extension (installed via `wp package install markri/wp-sec`):  
   `wp wp-sec scan --format=json > ./reports/wp-sec.json`
4. Run 10up scanner (if installed):  
   `wp vuln scan --api=wpscan --output=./reports/10up-scan.json`
5. Aggregate results and print a concise severity table; fail if any HIGH / CRITICAL issues found.
6. Suggest `wp plugin update --all` or specific patch links for each vulnerable component.