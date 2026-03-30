# OpenCart — CSS

## Catalog — custom styles

For catalog styles, **only** edit this file (create if missing).
Do not touch other theme stylesheets unless necessary; override with a selector and `!important` if required.

```
catalog/view/theme/[THEME]/stylesheet/snk.css ← or the theme's general custom file
```

If the theme is unknown — **ask** before writing paths.

---

## Admin — styles

Admin OC 3.x uses **Bootstrap 3** (built-in). Do not switch to Bootstrap 4/5 without a task.

Custom admin CSS:

```
admin/view/stylesheet/cactus/   ← custom admin styles
```

Load:

```php
$this->document->addStyle('view/stylesheet/cactus/my-admin-style.css');
```

---

## Overriding theme styles

Do not overwrite core theme CSS files. Instead:

1. Create a separate file under a `cactus/` subfolder
2. Load it after main theme styles (higher cascade priority)

---

## Rules

- Do not hardcode theme name in CSS (only via PHP variables in the controller)
- Ask where to store CSS if the theme is unfamiliar
