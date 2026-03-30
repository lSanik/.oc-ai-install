# Scheme — CLAUDE.md

## INSTRUCTIONS FOR THE AI

Use this scheme to generate `CLAUDE.md`.
`CLAUDE.md` is a thin entry file. OpenCart rules live under `.ai-oc-install/opencart/`; this file only pulls in **`main.md`** plus blocklist — other `opencart/` files are opened **selectively** per task by the working assistant.
Project data (version, environment, constraints) live in `project.md` (PERSISTENT).

---

## TEMPLATE

```markdown
# Project: [project name or domain]

You are a highly skilled OpenCart developer.

@.claude/project.md
@.claude/ai-map.md
@.claude/code-style.md

@.ai-oc-install/opencart/main.md

@.ai-oc-install/global/blocklist.md

**Before working on a task** read from `.ai-oc-install/opencart/` **only** the files relevant to the current task (do not load every `.md` at once). For example: PHP — `php.md`; JS — `js.md`; CSS — `css.md`; controllers / models / templates — `controller.md`, `model.md`, `view.md`; languages — `language.md`; system — `system-library.md`; DB — `mysql.md` and **`.ai-oc-install/map/db_mapping.md`**; if you need **full column-level `CREATE TABLE`** for a specific table from DB introspection, open **`.ai-oc-install/map/db_tables/<table_name>.php`** when it exists; table name index — **`.ai-oc-install/map/db_map.php`**; admin — `admin.md`; storefront — `catalog.md`.

---

## AI Files

PERSISTENT — never delete or overwrite on reinstall:
- `.claude/project.md` — project data, warning zone, constraints
- `.claude/ai-map.md` — map of custom code and deviations from standard
- `.ai-oc-install/map/db_mapping.md` — curated custom / changed table DDL and notes (shared for Claude and Cursor)
- `.ai-oc-install/map/db_map.php` — optional generated index of table names (when map generator was run)
- `.ai-oc-install/map/db_tables/*.php` — optional one file per table: `$ddl` string = full `CREATE TABLE` with all columns, types, nullability, defaults, keys, engine (see `scheme-db-mapping.md`)

REGENERATABLE — overwritten on reinstall:
- `.claude/CLAUDE.md` — this file
- `.claude/settings.json` — Claude permissions deny list (per `scheme-settings.md`)
- `.claude/code-style.md` — code rules
```

---

## Generation rules

1. `CLAUDE.md` — only @imports, the selective-reading paragraph, and AI Files. Do not copy `opencart/` rules here.
2. When generating **do not** read `.ai-oc-install/opencart/*.md` — reproduce the `@` lines as above.
3. `project.md` — generated separately per `scheme-project.md`
4. `code-style.md` — generated per `scheme-code-style.md`
5. `settings.json` — generated per `scheme-settings.md` (Claude only); must appear in REGENERATABLE in `CLAUDE.md` AI Files
6. Do not add sections not in the template
