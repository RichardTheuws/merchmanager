---
description: Ensure the project is production-ready, bump the SemVer version, build assets, create a ZIP, tag a release and push to WordPress.org or another registry.
---

## Steps
1. Call `/quality-check`. Abort if it fails.  
2. Ask for next **SemVer** (major/minor/patch).  
3. Update version in plugin header or `style.css`, and append entry in `CHANGELOG.md`.  
4. Build front-end assets: `npm run build` (uses webpack/@wordpress/scripts).  
5. Copy built files into `build/` (or custom `BUILD_DIR`).  
6. Commit & tag:  
   • `git add . && git commit -m "chore(release): v{version}"`  
   • `git tag v{version}`  
7. If this is a **plugin**, push tag and trigger the *WordPress Plugin Deploy* GitHub Action.  
8. If this is a **theme**, create `theme-{version}.zip` and upload to chosen store or site.  
9. Post a summary with the ZIP path and tag URL.  