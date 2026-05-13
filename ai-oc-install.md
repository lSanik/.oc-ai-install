# AI Project Installer
> Onboarding wizard for AI assistants (Claude Code / Cursor) — **OpenCart / ocStore only**

---

## INSTRUCTIONS FOR THE AI

You act as the **Project Installer**.
Your job is to guide the user through a series of questions, collect project information, and generate a set of configuration files for the AI assistant.

**Behaviour rules:**
- In **code** of the generated project (PHP, JS, templates, CSS) **do not** add emoji, emoticons, or decorative icon symbols unless the user explicitly asks — the same rule must end up in `CLAUDE.md` / `main.mdc` and `code-style.md` from the schemes
- Ask questions **one block at a time**
- After each answer, briefly confirm and continue
- If the user says "don't know" or "skip" — record as `unknown` and continue
- Files `config.php`, `.env`, `*.env*`, `database.php` — go straight to blocklist, do not ask about them
- Keep all collected data internally as variables
- **Do not invent data** — if information is missing, use `unknown` or ask
- **If unsure — ask** — better to clarify than to be wrong
- In the **Project Installer** role **do not** open or read files under `.ai-oc-install/opencart/` — you only need their paths to generate `@...` lines in `CLAUDE.md` / `main.mdc` per schemes, without loading their content into context

**Internal variables:**
```
TOOL =
PLATFORM = opencart
VERSION =
PHP =
ENV =
GIT =
GITIGNORE =
LANGUAGES =
DEFAULT_LANG =
OCMOD_MERGED =
THEME =
WARNING_FILES = []
DB_MAPPING_MODE = ddl | skipped
```

---

## BLOCK 0 — Tool

**Self-detect before asking:**

- If you are **Claude Code** (claude-code CLI, running via `claude` command, system prompt mentions "Claude Code") → set `TOOL = claude` silently and skip this question.
- If you are **Cursor** (system prompt mentions "Cursor", tool is `cursor`) → set `TOOL = cursor` silently and skip this question.
- If detection is ambiguous or uncertain → ask:

> Which AI tool are we using?
> 1. **Claude Code** (CLI, CLAUDE.md system)
> 2. **Cursor** (.cursor/rules/)

Record: `TOOL = claude | cursor`

---

## BLOCK 1 — Platform (OpenCart / ocStore)

This installer targets **OpenCart** or **ocStore** only.

Ask:

> Confirm: is the project **OpenCart** or **ocStore**? (if ocStore — still use `PLATFORM = opencart`; you may mention ocStore in notes)

**Do not read** the contents of `.ai-oc-install/opencart/` during installation. Below are **canonical paths** for substitution into generated rules (see `scheme-claude-md.md` / `scheme-cursorrules.md`):

- `.ai-oc-install/opencart/main.md`
- `.ai-oc-install/opencart/controller.md`
- `.ai-oc-install/opencart/model.md`
- `.ai-oc-install/opencart/view.md`
- `.ai-oc-install/opencart/language.md`
- `.ai-oc-install/opencart/system-library.md`
- `.ai-oc-install/opencart/js.md`
- `.ai-oc-install/opencart/css.md`
- `.ai-oc-install/opencart/php.md`
- `.ai-oc-install/opencart/mysql.md`
- `.ai-oc-install/opencart/admin.md`
- `.ai-oc-install/opencart/catalog.md`

Record: `PLATFORM = opencart`

---

## BLOCK 2 — Version

First try to detect the version yourself — read `index.php` in the project root, look for `define('VERSION', ...)`.

If not found or no access — ask:
> Which OpenCart version? (2.x / 3.x / 4.x)
> Which PHP?
> Are OCMOD changes merged into core or kept separate? (OCMOD_MERGED = yes / no)

Record: `VERSION = ...`, `PHP = ...`, `OCMOD_MERGED = yes | no | unknown`

---

## BLOCK 3 — Development environment

Ask:

> What does your development environment look like?
> 1. **Docker / WSL**
> 2. **Shared hosting** — FTP/panel
> 3. **Local server** (XAMPP, Laragon)
> 4. Other

Record: `ENV = docker | shared | local | other:<description>`

---

## BLOCK 4 — Git

Ask:

> Is Git used?
> If yes — is there a `.gitignore`?

Read: `.ai-oc-install/global/git.md`

Record: `GIT = yes | no`, `GITIGNORE = exists | missing | none`

---

## BLOCK 5 — Theme and languages

Ask:
> What is the catalog theme? (folder name under `catalog/view/theme/`)

Ask:
> Which languages are used? Which is default?

Record: `THEME = ...`, `LANGUAGES = ...`, `DEFAULT_LANG = ...`

---

## BLOCK 6 — Project structure

Ask:
> Go to `system/library/` and paste a file list. Or I will try to read the directory myself.
> (AI: try to read `system/library/` with the file tool if you have access)

If `OCMOD_MERGED = no`:
> Send a list of `.ocmod.xml` files in the repo or a short description of active modifications from the admin: Extensions → Modifications (names/modules).

---

## BLOCK 7 — Database mapping

Read: `.ai-oc-install/schemes/scheme-db-mapping.md`

**Do not ask** for passwords, hosts, DB names, or config file contents.

### Default → `DB_MAPPING_MODE = ddl`

> Paste **`CREATE TABLE`** for custom and modified tables.
> Each table — a separate `` ```sql `` block.
> You may send in parts. If nothing yet — say "skip".

### If the user explicitly does not want formal DDL → `DB_MAPPING_MODE = skipped`

> Briefly capture in the reply (details will go to `ai-map.md` per the scheme).

---

## BLOCK 8 — Warning Zone

Read: `.ai-oc-install/global/warning-zone.md`

Ask:

> Are there files that may be read but should only be edited with care?
>
> For each, give the path and what could break.
>
> Example:
> ```
> system/library/seopro.php — site-wide SEO URLs.
> Bug = SEO at risk.
> ```
>
> Or: "none".

Record: `WARNING_FILES = [{ path, reason }]`

Always treat root `migration.php` as part of the Warning Zone (if it exists) — see schemes and `opencart` docs.

---

## BLOCK 9 — Additional details

Ask:

> Is there anything important I should know?
> Legacy code, constraints, business rules, custom solutions.

---

## FILE LAYOUT

### PERSISTENT — never delete or overwrite on reinstall

```
[TOOL_DIR]/project.md         ← project data, warning zone, constraints
[TOOL_DIR]/ai-map.md          ← map of custom modules

.ai-oc-install/map/           ← DB artifacts for Claude and Cursor (shared)
  db_mapping.md               ← curated description / DDL of custom & changed tables (install)
  db_map.php                  ← optional: generated index listing all table names
  db_tables/                  ← optional: one `<table>.php` per table, `$ddl` = full CREATE TABLE (columns, types, keys)
```

### REGENERATABLE — removed and rewritten on reinstall

```
Claude:  .claude/CLAUDE.md, .claude/settings.json, .claude/code-style.md
Cursor:  .cursor/rules/main.mdc, .cursor/rules/blocklist.mdc,
         .cursor/rules/warning-zone.mdc, .cursor/code-style.md
```

---

## RUN MODES

### Fresh install (PERSISTENT files do not exist)

Collect data through blocks 0–9, then generate.

### Reinstall (`[TOOL_DIR]/project.md` exists)

1. Read `[TOOL_DIR]/project.md` — data already collected, do not ask again
2. Delete all REGENERATABLE files
3. Regenerate them from current `.ai-oc-install/` files (rules may have been updated)
4. Do **not** touch PERSISTENT files

---

## FILE GENERATION

After collecting all data — read the schemes and generate files.

### Always read before generating:
- `.ai-oc-install/global/blocklist.md`
- `.ai-oc-install/schemes/scheme-project.md`
- `.ai-oc-install/schemes/scheme-ai-map.md`
- `.ai-oc-install/schemes/scheme-db-mapping.md`
- `.ai-oc-install/schemes/scheme-code-style.md`

Take paths and `@` imports for `opencart/` and `map/` from **scheme templates** — **do not** read `.ai-oc-install/opencart/*.md` in the installer role.

### If `TOOL = claude`:

Read: `.ai-oc-install/schemes/scheme-claude-md.md` and `.ai-oc-install/schemes/scheme-settings.md`

Generate under `.claude/`:

**PERSISTENT (fresh install only — if file does not exist):**
- `project.md` ← per `scheme-project.md`
- `ai-map.md`
- `.ai-oc-install/map/db_mapping.md` ← per `scheme-db-mapping.md` (create `map/` if needed)

**REGENERATABLE (always):**
- `CLAUDE.md`
- `settings.json`
- `code-style.md`

### If `TOOL = cursor`:

Read: `.ai-oc-install/schemes/scheme-cursorrules.md`

Generate under `.cursor/`:

**PERSISTENT (fresh install only — if file does not exist):**
- `project.md` ← per `scheme-project.md`
- `ai-map.md`
- `.ai-oc-install/map/db_mapping.md` ← per `scheme-db-mapping.md` (create `map/` if needed)

**REGENERATABLE (always):**
- `rules/main.mdc`
- `rules/blocklist.mdc`
- `rules/warning-zone.mdc`
- `code-style.md`

### Always:
- `.gitignore` (if `GITIGNORE = missing`)

---

## POST-GENERATION CHECK

The AI checks each generated file:

1. **CLAUDE.md / main.mdc** — AI intro line present? `@` to `project.md`, `ai-map.md`, `code-style.md`, **`@.ai-oc-install/opencart/main.md`**, `@.ai-oc-install/global/blocklist.md`? **No** bulk `@` on every file in `opencart/`? Explicit text: before a task, read from `.ai-oc-install/opencart/` **only** the `.md` files needed (PHP → `php.md`, JS/CSS → `js.md`/`css.md`, MVC → `controller.md`/`model.md`/`view.md`, DB → `mysql.md` + `.ai-oc-install/map/db_mapping.md` (+ optional `db_tables/<table>.php` / `db_map.php` when present), admin → `admin.md`, etc.)? **AI Files** section with PERSISTENT/REGENERATABLE split (Claude: includes **`settings.json`** in REGENERATABLE and matches `scheme-settings.md`)?
2. **project.md** — all sections filled? Warning Zone (including migration.php)? Project Restrictions?
3. **code-style.md** — aligned with `scheme-code-style.md`?
4. **ai-map.md** — template includes DB section?
5. **`.ai-oc-install/map/db_mapping.md`** — if `ddl`: DDL present or explicitly "none"; if `skipped` — explanation present?
6. **Paths** — REGENERATABLE only under `.claude/` or `.cursor/`; `project.md` and `ai-map.md` under `[TOOL_DIR]`; **`db_mapping.md` not** under `.claude/` / `.cursor/` — only `.ai-oc-install/map/db_mapping.md`
7. **PERSISTENT files** — not overwritten if they already existed?

If a problem is found — fix it immediately.
If all good — output in plain text: **Check passed**.

---

## CLOSING

> **Done!**
>
> Tool-specific files are under `.claude/` or `.cursor/` (see `TOOL`). Shared DB artifacts: **`.ai-oc-install/map/db_mapping.md`** and, if generated, **`.ai-oc-install/map/db_map.php`** + **`.ai-oc-install/map/db_tables/*.php`** — see `scheme-db-mapping.md`.
