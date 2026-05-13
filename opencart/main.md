# OpenCart — Main file

## INSTRUCTIONS FOR THE AI

You work on an OpenCart / ocStore project.

> **Custom folder placeholder:** Throughout `.ai-oc-install/opencart/` docs, `{CUSTOM_DIR}` means the project's custom plugin folder — read its value from `project.md` field `Custom folder` (default: `my_custom`). `{CustomDir}` is its PascalCase form used in class names (e.g. `my_custom` → `MyCustom`; `cactus` → `Cactus`). Apply these values when following path, route, and class name patterns in this documentation.

Platform instructions live under **`.ai-oc-install/opencart/`**. **Do not** load every `.md` from this folder at once: **before analysing and executing a task** open **only** the files you need.

**DB mapping** — under **`.ai-oc-install/map/`**: start with **`db_mapping.md`** (curated DDL from install). For a **full column-level** definition of one table (as in `SHOW CREATE TABLE`), open **`db_tables/<table_name>.php`** when present (`$ddl` string). **`db_map.php`** lists all table names. SQL conventions — [`mysql.md`](mysql.md).

### Task → which files to read

| Task context | Files in `.ai-oc-install/opencart/` |
|--------------|--------------------------------------|
| Overview, version, custom folder, OCMOD | `main.md` (this file) |
| Controllers, routes, `$this->load` | `controller.md` |
| Models, SQL in data layer | `model.md`, `mysql.md` |
| Twig / templates | `view.md` |
| Language files, translations | `language.md` |
| `system/library`, core | `system-library.md` |
| Storefront / admin JS | `js.md` |
| Styles | `css.md` |
| PHP syntax, project limits | `php.md` |
| Admin (extension, forms) | `admin.md`, plus `controller.md`, `view.md` if needed |
| Storefront (catalog) | `catalog.md`, plus `controller.md`, `view.md` if needed |

---

## Detecting version and platform variant

First check `project.md` — version and platform are already stored there after install.

If `project.md` is missing — read `index.php` in the project root:

```php
// VERSION is the engine version — present in both OpenCart and ocStore:
define('VERSION', '3.0.3.7');

// VERSION_CORE is the ocStore marker — absent in standard OpenCart:
define('VERSION_CORE', 'ocStore');
define('VERSION_BUILD', '0002');
```

- `VERSION_CORE` present and equals `'ocStore'` → **ocStore** (platform = opencart, note ocStore in context)
- `VERSION_CORE` absent → standard **OpenCart**
- `VERSION` value → engine version for both

If `index.php` is unreadable — ask the user.

Record: `VERSION = 2.x | 3.x | 4.x`, `OCSTORE = yes | no`

| Version | Templates | Code style | Notes |
|---------|-----------|------------|-------|
| 2.x | `.tpl` (PHP) | PSR-2 | Legacy syntax |
| 3.x | `.twig` | PSR-2 | Most common |
| 4.x | `.twig` | PSR-12 | Namespaces |

View layer docs in [`view.md`](view.md) cover **Twig only**. `.tpl` (2.x) is legacy; do not migrate to Twig without an explicit task.

---

## OpenCart architecture

OpenCart uses **MVC(L)** — Model, View, Controller, Language.

```
catalog/                  ← storefront (customers)
  controller/
  model/
  view/
  language/

admin/                    ← admin (managers)
  controller/
  model/
  view/
  language/

system/
  library/                ← libraries (careful — see system-library.md)
  engine/                 ← MVC core (DO NOT TOUCH)
  storage/                ← cache, logs, sessions (DO NOT TOUCH, blocklist)
```

---

## Custom plugin folder — standard for custom code (`{CUSTOM_DIR}`)

**Custom plugin folder** is the pattern for all new custom code. Never mix it into OC core.

### Catalog (storefront)
```
catalog/controller/{CUSTOM_DIR}/[name].php
catalog/model/{CUSTOM_DIR}/[name].php
catalog/view/theme/[theme]/template/{CUSTOM_DIR}/[name].twig
catalog/language/[locale]/{CUSTOM_DIR}/[name].php
```

### Admin
Admin custom modules register as OC extensions:
```
admin/controller/extension/module/{CUSTOM_DIR}/[name].php
admin/model/extension/module/{CUSTOM_DIR}/[name].php
admin/view/template/extension/module/{CUSTOM_DIR}/[name].twig
admin/language/[locale]/extension/module/{CUSTOM_DIR}/[name].php
```
Route: `extension/module/{CUSTOM_DIR}/[name]`


### Dev / debug (local only)
```
catalog/controller/{CUSTOM_DIR}/dev/
catalog/controller/{CUSTOM_DIR}/dev/scripts/   ← one-off data scripts
```

---

## OCMOD status

Ask or detect: `OCMOD_MERGED = yes | no`

**OCMOD_MERGED = no** — modifications live in XML; applied copies in `system/storage/modification/`
- Do not edit files in `system/storage/modification/` — they are regenerated
- Change the original `.ocmod.xml`
- After XML changes — remind the user: Admin → Extensions → Modifications → **Refresh**

**OCMOD_MERGED = yes** — modifications merged into core files
- Edit files directly
- Mark such files in `ai-map.md` as "modified core"

---

## Loading components

```php
// OK: correct
$this->load->model('catalog/product');
$this->load->library('session');
$this->load->language('catalog/product');

// NOT allowed:
include('...');
require('...');
```

---

## Database — basics

- Always `$this->db->escape()` for string input
- Table prefix via `DB_PREFIX`, never hardcode `oc_`
- Queries — in models only, never in controllers
- Any DB schema change → record in `migration.php` (see model.md)

---

## Controller — typical role in OpenCart style (**new** code)

Rules below are for **new** code (`{CUSTOM_DIR}`, etc.). **Do not rewrite legacy** unless the user **explicitly** asks.

**This is not business logic or SQL.** In a controller it is normal to:

- `$this->load->language`, `$this->load->model`, `$this->load->library` and other loaders;
- call **one** relevant model: fetch data for output, pass data to save, etc.;
- build `$data` for Twig: simple `foreach`, image URLs, breadcrumbs, titles;
- `$this->load->controller('common/header')` / `footer` / `column_left` and `$this->load->view(...)`.

**Not allowed:** piping results **sequentially through several models** ("model A → model B → model C") in one flow. Fold that coordination **into one model** (one call from the controller) or split explicitly in the task — no "pipeline" in the controller.

Controller structure details — [`controller.md`](controller.md); templates — [`view.md`](view.md).

---

## FORBIDDEN (globally for OC)

- `oc_event` — do not use unless the user explicitly asks
- `include` / `require` for OC components — only `$this->load->`
- Hardcode prefix `oc_` — only `DB_PREFIX`
- **Business logic, SQL, heavy aggregation** — in the **model** (see [`model.md`](model.md)), not the controller
- Load `admin/` models from `catalog/` controllers and vice versa
- Edit `system/engine/` — never
- Edit `system/storage/` — never (blocklist)
- `var_dump`, `print_r`, `echo` for debugging in production code
