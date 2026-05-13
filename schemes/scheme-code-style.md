# Scheme — code-style.md

## INSTRUCTIONS FOR THE AI

Generate **one** file **`code-style.md`** under the chosen tool directory (as in `ai-oc-install.md`):

- If `TOOL = claude` → `.claude/code-style.md`
- If `TOOL = cursor` → `.cursor/code-style.md`

In the **Project Installer** role **do not** read `.ai-oc-install/opencart/*` — build `code-style.md` **only** from this template. MVC / custom folder / `migration.php` details are in `opencart/*.md` (working assistant reads them selectively).

---

## TEMPLATE

```markdown
# Code style — OpenCart / ocStore

Coding rules and refactor boundaries. Read together with the main rules file (`CLAUDE.md` / `main.mdc`).

---

## OpenCart events and hooks (`oc_event`, etc.)

- **Do not** add events, hooks, or core `oc_event`-style handlers unless the user **explicitly** asks in the task.
- If the user wants an event-driven approach — implement the minimum needed and record in `ai-map.md` / the task what was added.

---

## Catalog and admin — no mixing

- **Do not** call or load **admin** models/controllers from **catalog** code and vice versa. Separate contexts, separate entrypoints.
- Shared logic — in **models** in the right area (`catalog/model/[CUSTOM_DIR]/...` and `admin/model/...`) or in **system libraries** only when needed and aligned with Warning Zone / `ai-map.md`.
- No cross-zone `load->model` between admin and storefront.

---

## Helpers and shared utilities

- If the same data prep is needed in **several controllers** in **new** code (do not refactor legacy without a task) — prefer a **helper** (typical path `system/library/` or per `ai-map.md`).
- **Reading** existing helpers — **allowed**.
- **Adding** a helper or **editing** one — **only after explicit user approval**; **ask first**.

---

## Models, methods, and responsibility

- Keep **balance**: do not split every method into its own file, but do not dump unrelated logic into one bucket.
- A **new entity** (domain logic) — usually a **separate model** in the right zone (`catalog/model/[CUSTOM_DIR]/...` or `admin/model/...`).
- If the same entity gets a **separate layer** (e.g. another **API** shape) — logically a **separate model** for that layer, not one bloated generic model.
- If one model covers **different domains** (e.g. both **products** and **orders**) — for **new** code split into **separate models**; do not rewrite legacy without a task.
- Group methods by purpose (read, write, aggregates, etc.) within one model so files stay readable in OpenCart style.

---

## Dev controllers (`catalog/controller/[CUSTOM_DIR]/dev/`)

- For **dev** scripts and one-off actions a **model is optional**: logic may live **in the controller** — that is what dev controllers are for.
- Do not spread dev logic into production models unless it becomes permanent (then refactor per task scope).

---

## PHP syntax (project limits)

- **Do not** use **`match`**.
- **`??`** for default values is **forbidden**. Use **`isset()` + ternary**, e.g. `isset($qwe) ? $qwe : ''` or `($qwe !== null && $qwe !== '') ? $qwe : ''` depending on what counts as "empty" in context.

---

## Arrays and collections

- **Sorting or indexing large arrays by keys** (indexes, external ids, etc.) is **welcome** when it **really simplifies** later work or matches the task (e.g. group rows by `old_product_id` before a loop).
- Do not add extra passes only for style if one clear loop is enough.

---

## Data without globals

- **Do not** pass data via PHP **globals** (`$GLOBALS`, ad-hoc `global $foo`).
- If you need OpenCart core outside normal context — use **`$this->registry`** or **`$this->config`** (and other controller/model properties), not custom global stores.

---

## Parameters by reference (`&`)

- **Do not** declare method arguments **by reference** (`function foo(&$data)` etc.) to avoid hidden side effects through calls. Return values **explicitly** (`return`, structured array, object if needed).
- **Mandatory** for **all new** code. **Do not rewrite legacy** for this unless the user **explicitly** asks in the task (then only listed files / scope).

---

## DRY (Don't Repeat Yourself)

- Apply DRY to **new** code and to code **in the current task scope**.
- **Do not** refactor existing project code "for beauty" or only because you see duplication if it is **not in the task**.
- If the task includes **DRY refactor** (or the user explicitly asks to remove duplicates):
  - Prefer extracting shared code from what is **already listed in the task** or **created in the same task**.
  - Do not stretch refactor into unrelated files without explicit approval.

---

## Code and formatting (reminder)

- In code files (`.php`, `.js`, **`.twig`**; legacy `.tpl` only if editing old templates, `.css`) **do not** add emoji, emoticons, or decorative symbols without explicit user request.
- The rest: SQL escaping, `DB_PREFIX`, custom folder paths, `migration.php` on DB changes — as in `opencart/` docs and the main rules file.

---

## Updates

- After tasks, update these rules or new style agreements manually in `code-style.md` (and briefly in `ai-map.md` if needed).
```

---

## Generation rules

These three points are **for the AI** building **`code-style.md`** in the project (not for the store developer directly).

1. In the generated `code-style.md` heading you may add the project name; if none — keep a generic OpenCart / ocStore subtitle.
2. **Do not paste full text** from `opencart/*.md` — it is read separately. In `code-style.md` keep **only** style and refactor boundaries from this template (and short reminders if needed). The long sections above already cover: models/zones, dev controllers, ban on `match` and `??` for defaults, no `&` params for new code, helpers, arrays, registry, DRY and task scope, events, catalog/admin — **do not** duplicate `opencart/*.md` verbatim.
3. If the user named **special** project constraints during install — add a short **"Project specifics"** subsection to the generated `code-style.md` with those conditions.
