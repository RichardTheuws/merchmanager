---
description: Scaffold a new WordPress **plugin** or **theme**, install PHP & JS dependencies, and commit the first working build.
---

## Steps
1. Ask the user: “Plugin or Theme? Provide a project slug (e.g., merch-manager).”
2. If **Plugin**  
   • Run: `wp scaffold plugin {slug} --skip-tests=false`  
   • Run: `wp scaffold plugin-tests {slug}`  
   • Activate the plugin: `wp plugin activate {slug}`  
3. If **Theme**  
   • Run: `wp scaffold _s {slug} --theme_name="{slug}"`  
   • Optionally: `wp scaffold theme-tests {slug}`  
4. Install composer packages: `composer install`  
5. Install Node packages: `npm install`  
6. Initialise a git repo (if none) and make the first commit: `git add . && git commit -m "chore: scaffold WordPress {type}"`.  
7. Print “Scaffold complete – run /quality-check next.”  