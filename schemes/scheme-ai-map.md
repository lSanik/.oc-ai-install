# Scheme — ai-map.md

## INSTRUCTIONS FOR THE AI

`ai-map.md` maps deviations from standard and custom project code.
It does not describe standard platform files — only what is unique to this project.

Fill from install data: **Block 5** (theme and languages), **6** (structure), **7** (DB), **8** (Warning Zone — table in template below), **9** (extra notes — section below).

---

## TEMPLATE

```markdown
# AI Map — [project name]

Map of custom code and deviations from standard.
Read before working on any project file.

---

## Blocklist
Do not read or touch:
- config.php, admin/config.php, .env
[project-specific]

---

## Warning Zone

| File | Reason | Impact if buggy |
|------|--------|-----------------|
| [path] | [reason] | [impact] |

[Always add for OpenCart / ocStore:]
| `migration.php` | Manual DB schema log for prod; after `<?php` — `die(0);`/`die();`, then blank line or log comment. AI DB model — `.ai-oc-install/map/db_mapping.md`. | Edit only on explicit command; read for context. |

[If empty: "None at this time."]

---

## Safe Zone — Custom code

### Cactus modules (OpenCart / ocStore)

**Catalog (storefront):**
| File | Purpose |
|------|---------|
| catalog/controller/cactus/[name].php | [description] |
| catalog/model/cactus/[name].php | [description] |

**Admin:**
| File | Purpose |
|------|---------|
| admin/controller/cactus/[name].php | [description] |

**Libraries:**
| File | Purpose | Warning Zone |
|------|---------|--------------|
| system/library/seopro.php | SEO URLs | WZ: yes |
| system/library/[other custom] | [description] | |

---

## Modified core

[If OCMOD_MERGED = yes or direct core edits:]

| Core file | What changed | Why | Warning Zone |
|-----------|--------------|-----|--------------|
| [path] | [change summary] | [reason] | WZ / — |

[If OCMOD_MERGED = no and no direct edits:]
Core not modified directly.
Modifications via OCMOD XML files.

---

## Frontend / Themes

**Store theme:** catalog/view/theme/[name]/
- Custom CSS: [path or "default"]
- Custom JS: [path or "default"]
- Catalog-specific features: [list or "standard"]

**Admin:**
- Admin-specific features: [list or "standard"]

---

## Database

### Custom tables (non-standard for the platform)

| Table | Purpose | Related files |
|-------|---------|---------------|
| [name] | [description] | [controller/model] |

### Modified standard tables

| Table | Added fields | Reason |
|-------|--------------|--------|
| [name] | [fields] | [reason] |

[If nothing: "DB structure is standard for the platform."]

### migration.php
Root file — **human schema change journal** (what to apply on prod). **Primary AI schema source** — `.ai-oc-install/map/db_mapping.md` (one path for Claude and Cursor). On schema change, update both.
Readable; edit `migration.php` only on explicit command. May already exist on legacy projects.

---

## Additional notes

[From Block 9 — legacy, constraints, business rules]

[If nothing: "No specific extra notes."]
```

---

## Fill rules

1. Describe only custom parts and deviations — do not duplicate platform docs
2. Warning Zone — section always present (even if empty)
3. Modified core — critical for OC projects
4. Update manually after tasks that change structure
5. DB mapping — primary link **`.ai-oc-install/map/db_mapping.md`**; optional deep schema per table: **`.ai-oc-install/map/db_tables/<table>.php`**; table index: **`db_map.php`**. `project.md` / `ai-map.md` — under chosen tool dir (`.claude/` or `.cursor/`) per `TOOL`
