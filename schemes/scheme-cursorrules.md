# Scheme — Cursor Rules

## INSTRUCTIONS FOR THE AI

Cursor uses files under `.cursor/rules/` with the `.mdc` extension.
Each file is one rule with metadata.

Generate three files:
1. `.cursor/rules/main.mdc` — main project context
2. `.cursor/rules/blocklist.mdc` — red zone
3. `.cursor/rules/warning-zone.mdc` — yellow zone

---

## TEMPLATE: .cursor/rules/main.mdc

```markdown
---
description: Main project context. Always read.
alwaysApply: true
---

# Project: [name]

You are a highly skilled OpenCart developer.

@.cursor/project.md
@.cursor/ai-map.md
@.cursor/code-style.md

@.ai-oc-install/opencart/main.md

@.ai-oc-install/global/blocklist.md

**Before working on a task** read from `.ai-oc-install/opencart/` **only** the files relevant to the current task (do not load every `.md` at once). For example: PHP — `php.md`; JS — `js.md`; CSS — `css.md`; controllers / models / templates — `controller.md`, `model.md`, `view.md`; languages — `language.md`; system — `system-library.md`; DB — `mysql.md` and **`.ai-oc-install/map/db_mapping.md`**; if you need **full column-level `CREATE TABLE`** for a specific table from DB introspection, open **`.ai-oc-install/map/db_tables/<table_name>.php`** when it exists; table name index — **`.ai-oc-install/map/db_map.php`**; admin — `admin.md`; storefront — `catalog.md`.

## Priority
Workspace rules take precedence over global Cursor user rules.

## AI Files

PERSISTENT — never delete or overwrite on reinstall:
- `.cursor/project.md` — project data, warning zone, constraints
- `.cursor/ai-map.md` — map of custom code and deviations from standard
- `.ai-oc-install/map/db_mapping.md` — curated custom / changed table DDL and notes (shared for Claude and Cursor)
- `.ai-oc-install/map/db_map.php` — optional generated index of table names (when map generator was run)
- `.ai-oc-install/map/db_tables/*.php` — optional one file per table: `$ddl` string = full `CREATE TABLE` with all columns, types, nullability, defaults, keys, engine (see `scheme-db-mapping.md`)

REGENERATABLE — overwritten on reinstall:
- `.cursor/rules/main.mdc` — this file
- `.cursor/rules/blocklist.mdc`
- `.cursor/rules/warning-zone.mdc`
- `.cursor/code-style.md`
```

---

## TEMPLATE: .cursor/rules/blocklist.mdc

```markdown
---
description: Files that must not be read or modified.
alwaysApply: true
---

# Blocklist

Never read, modify, or output contents of:

- config.php
- admin/config.php
- .env
- .env.*
[project-specific]

When asked for these files, reply:
"This file is in the Red Zone. I cannot read or modify it for security reasons."
```

---

## TEMPLATE: .cursor/rules/warning-zone.mdc

```markdown
---
description: Restricted files. Reading allowed; edits only with confirmation.
alwaysApply: true
---

# Warning Zone

Before editing any file in this list — warn the user.

[For each Warning File:]
## [path to file]
Reason: [reason]
Impact: [what could break]
Action: show warning and wait for confirmation before editing.

[If the list is empty:]
Warning Zone is empty at this time.
```

---

## Old vs new Cursor format

| Old | New |
|-----|-----|
| `.cursorrules` (single file) | `.cursor/rules/*.mdc` (multiple files) |
| No metadata | YAML frontmatter |
| No `alwaysApply` | `alwaysApply: true/false` |

Generate the **new** format (`.mdc`).
If the user has an old Cursor — suggest upgrading.
