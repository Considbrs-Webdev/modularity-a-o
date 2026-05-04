# Modularity A-Ö Links

Alphabetical (A-Ö) link list for **Modularity**: **published pages under a chosen root** are resolved on each **frontend page request** in PHP, merged with **manual links**, sorted with Swedish collation, grouped A–Ö, and rendered with **Blade** using Municipio component patterns (`@typography`, `@link`, `c-listing`).

## Structure

- `modularity-a-o.php` — bootstrap, constants, ACF export sync, view path
- `source/php/Helper/PageTreeBuilder.php` — recursive published descendant pages
- `source/php/Helper/ManualLinkRowHelper.php` — repeater row URL / emptiness helpers
- `source/php/Helper/AoIndexBuilder.php` — merge, sort, grouping
- `source/php/Module/views/a-o.blade.php` — jump nav + sections
- `source/php/Module/views/partials/a-o-link-list.blade.php` — listing rows with chevron
- `source/php/AcfFields/php/a-o-module.php` — field group (loaded by AcfExportManager)
- `source/sass/modularity-a-o.scss` — module shell / jump strip utilities

## Editor

- **Dynamic source → Root page**: tree is built from descendants only (not the root).
- **Manual links**: **Link type** Page or External URL (only the matching field shows). Label optional (defaults to page title or the URL text).

## Build assets

From the plugin directory:

```bash
composer install
npm ci
npm run build
```

Optional (e.g. CI): `php build.php` runs the same installs and build. Pass `--no-composer` or `--no-npm` to skip either step. For a minimal release tree, use `--cleanup-release` (does not remove `.git`).

Output: `assets/dist/manifest.json`, hashed CSS.
