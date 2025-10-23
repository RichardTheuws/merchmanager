---
description: Profile WordPress execution stages and hooks to reveal bottlenecks.
---

## Steps
1. Ensure the profile package is present:  
   `wp package install wp-cli/profile-command:@stable`
2. Profile bootstrap, query and template stages:  
   `wp profile stage --all --spotlight --fields=stage,component,time`
3. Profile slow hooks (≥ 20 ms):  
   `wp profile hook --spotlight --threshold=20`
4. Output reports to `./reports/profile-{timestamp}.csv`.
5. Ask Cascade: “Highlight the five slowest callbacks and propose optimizations.”
6. Re-run steps 2-3 after fixes; exit green when no callback exceeds threshold.