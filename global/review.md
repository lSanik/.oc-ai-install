# OpenCart — Review Checklist

## INSTRUCTIONS FOR THE AI

Execute this checklist when the user says: **review / ревью / ревʼю / рев'ю / перевір задачу**.

Apply the checks to all files changed during the current task. Read `project.md` to get `CUSTOM_DIR`, `OCMOD_MERGED`, and Warning Zone entries before reviewing.

---

## 1. PHP Syntax

- Run `php -l <file>` for every modified `.php` file (if PHP is available in PATH).
- If PHP is not in PATH — visually scan for unclosed braces `{}`, missing semicolons `;`, unpaired quotes.
- Check for parse-breaking constructs: mismatched `if/else/endif`, broken `foreach`, stray characters outside `<?php`.

---

## 2. Debug Cleanup

- No `var_dump()`, `print_r()`, `die()`, `exit()`, `vd()`, `dd()` left outside of intentional dev scripts.
- No raw `echo` used for debugging in controllers, models, or views.
- No commented-out debug blocks left in production files.

---

## 3. OpenCart Architecture Rules

- **SQL only in models** — controllers must not contain raw SQL or `$this->db->query()`.
- **Loading** — only `$this->load->model()`, `$this->load->library()`, `$this->load->language()`. No bare `include` / `require` for OC components.
- **DB prefix** — `DB_PREFIX` used everywhere; no hardcoded `oc_` prefix.
- **Escaping strings** — every string value passed to SQL must go through `$this->db->escape()`, no exceptions.
- **Escaping numbers** — every numeric value must be cast to the exact type matching the column: `(int)` for integer columns, `(float)` for decimal/float columns.
- **No cross-zone loading** — admin models not loaded from catalog controllers and vice versa.
- **No `oc_event`** unless the task explicitly required it.

---

## 4. Protected Zones

- `system/engine/` — must not be touched.
- `system/storage/` — must not be touched (blocklist).
- If `OCMOD_MERGED = no`: files under `system/storage/modification/` must not be edited — only the source `.ocmod.xml`. If `.ocmod.xml` was changed → remind the user: **Admin → Extensions → Modifications → Refresh**.

---

## 5. Custom Folder Discipline

- New custom files live under `{CUSTOM_DIR}` paths (read value from `project.md`).
- No new custom logic placed directly into OC core directories without a clear reason noted in `ai-map.md`.

---

## 6. Database Changes

- Any `CREATE TABLE` or `ALTER TABLE` → must be recorded in `migration.php`.
- If the table is documented in `.ai-oc-install/map/` — update `db_mapping.md` or the relevant `db_tables/<table>.php`.

---

## 7. Cross-Component Impact

- **`system/library/` changes** affect the entire site (catalog + admin) — flag explicitly.
- **Language files** — if one locale was changed, check that all other active locales are in sync.
- **Views / templates** — verify the template is not shared across scopes that were not updated (e.g., a catalog template used in multiple routes).
- **OCMOD** — if XML was changed, remind the user to run Modifications → Refresh.

---

## 8. Cache & JS Links

- **`&amp;` in JS** — URLs built by `$this->url->link()` are HTML-escaped in Twig (`&` → `&amp;`). In JavaScript strings, `window.location`, `ajax url:`, or `onclick` attributes the raw `&` must be used. Check that no `&amp;` leaks into JS context.
- **OC cache** — after template changes remind to clear theme cache: Admin → Dashboard → blue refresh button. After language file changes — clear OC cache. After OCMOD changes — Modifications → Refresh (see section 4).

---

## 9. ai-map.md Sync

- Any new custom file → added to `ai-map.md`.
- Any modified core file → marked as "modified core" in `ai-map.md`.
- If the task touched Warning Zone files — confirm user was warned before editing.

---

## 10. Logic Check

- Does the implementation match the stated task requirement?
- Edge cases covered: empty result sets, missing records, zero quantities, permission-denied paths.
- No business logic or heavy aggregation placed in the controller.
- No SQL or data processing placed in Twig templates.

---

## 11. Security

- **Request data** — all access to user input must go through `$this->request->post`, `$this->request->get`, `$this->request->cookie`, `$this->request->files`. Direct use of `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE` is not allowed.
- **Admin tokens** — admin controllers must verify user token/permission before processing actions.
- **File uploads** — uploaded filenames and extensions must be validated; store outside webroot or with strict mime check.
- **Unsafe functions** — no `eval()`, `unserialize()` on untrusted data, `shell_exec()`, `system()`, `passthru()`.

---

## 12. Performance

- No N+1 queries introduced — queries must not run inside loops.
- Models must not be loaded inside loops.
- No repeated `$this->config->get()` or `$this->db->query()` calls for the same data inside a loop — fetch once, reuse.
- Heavy filesystem operations must not run inside large loops.

---

## Output Format

Always report results in this structure:

```
## Review: [short task description]

### Passed
- [item]

### Warnings
- [item] — [note for the user]

### Issues
- [item] — [what needs to be fixed]
```

If everything is clean: output **Review passed** with the Passed list only.
