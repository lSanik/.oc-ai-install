# OpenCart — System / Library

## Main rule

```
system/  →  DO NOT TOUCH
            exception: system/library/ — only with explicit permission
```

---

## system/library/ — with permission

Libraries are shared components used across the project.
A mistake here = cascading failure across the site.

**Before editing any file in `system/library/` — warn the user:**

```
 SYSTEM/LIBRARY: [path to file]
This is a shared library. Changes may affect the whole site.
Continue? (yes / no)
```

### When it may be needed

- Add a new custom library: `system/library/{CUSTOM_DIR}/[name].php`
- Extend an existing library (rare, with care)
- Integrate a third-party package

### Custom libraries

New functionality — under the custom folder:

```
system/library/{CUSTOM_DIR}/currency_helper.php
system/library/{CUSTOM_DIR}/image_processor.php
```

Load:
```php
$this->load->library('{CUSTOM_DIR}/currency_helper');
// available as $this->{CUSTOM_DIR}_currency_helper
```

### Known critical libraries (default Warning Zone)

Common in OC projects:

| File | Why critical |
|------|--------------|
| `system/library/seopro.php` | SEO URLs site-wide — bug breaks all URLs |
| `system/library/db/mysqli.php` | DB connection — bug takes site down |
| `system/library/session.php` | Sessions — bug breaks login |
| `system/library/cache.php` | Cache — bug slows or corrupts data |

Extra Warning Zone files — defined at project install.

---

## system/engine/ — NEVER

```
system/engine/action.php
system/engine/controller.php
system/engine/front.php
system/engine/loader.php
system/engine/model.php
system/engine/registry.php
system/engine/router.php
```

MVC core. Editing here = unpredictable behaviour site-wide.
**Do not read for edits, do not suggest changes, do not discuss changes** except in extreme cases.

---

## system/storage/ — FORBIDDEN (Blocklist)

```
system/storage/cache/
system/storage/logs/
system/storage/modification/   ← generated OCMOD output
system/storage/session/
system/storage/upload/
```

In blocklist. Do not read, modify, or output contents.

---

## Everything else under system/ — DO NOT TOUCH

```
system/config/    ← configs (partly blocklist)
system/helper/    ← helpers (read OK, edit only after asking)
system/vendor/    ← Composer dependencies
```

If a task requires editing something in `system/` outside `library/` — **ask first** whether it is really needed and if there is another way.
