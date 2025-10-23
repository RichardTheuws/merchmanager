---
description: Run all fast, local quality gates: PHPCS (WordPressCS), PHPStan, PHPUnit, Playwright, and Theme Review when applicable.
---

## Steps
1. Run: `composer run phpcs` (WordPress Coding Standards).  
2. Run: `composer run phpstan` at level 5.  
3. Run PHPUnit: `composer run test`.  
4. If Playwright tests exist (`tests/e2e`), run: `npm run test:e2e`.  
5. If a theme is present, run: `wp theme review check $(wp theme list --status=active --field=stylesheet)`.  
6. Summarise failures.  
7. For each failure, ask Cascade: “Suggest a minimal fix; apply and re-run this step.”  
8. Exit with success when all steps are green. 