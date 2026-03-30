# Scheme — db_mapping.md

## INSTRUCTIONS FOR THE AI

Generate **one** file **`db_mapping.md`** at this **fixed** path (same for Claude and Cursor):

**`.ai-oc-install/map/db_mapping.md`**

Create **`.ai-oc-install/map/`** if it does not exist.

---

## `.ai-oc-install/map/` — what lives here

| Path | Origin | Role for the AI |
|------|--------|------------------|
| **`db_mapping.md`** | **Install / manual** | Curated DDL and notes: custom tables, modified tables, `ddl` vs `skipped` mode. Start here for DB context. |
| **`db_map.php`** | **Optional generator** (e.g. `getMap.php` in this repo) | Small PHP file: a string listing **all table names** in the DB (plus metadata). Use to see what exists before opening per-table files. |
| **`db_tables/<table_name>.php`** | **Same generator** | **One file per table.** Each file sets `$ddl` to a **full `CREATE TABLE ...` statement** as returned by MySQL (`SHOW CREATE TABLE`). That string includes **every column** with name, type, `NULL`/`NOT NULL`, `DEFAULT`, `AUTO_INCREMENT`, **primary key**, **indexes**, `ENGINE`, `CHARSET` — i.e. complete field-level schema for that table. Use when you must match exact types in queries/migrations or when `db_mapping.md` does not list that table. **Do not** treat these PHP files as executable application code — they are schema carriers; the project root still uses `migration.php` for human rollout steps. |

`db_map.php` and `db_tables/` may be absent until someone runs the generator; **`db_mapping.md`** is created by the installer when the user supplies DDL (or `skipped` text).

---

## Artifact roles (OpenCart / ocStore, `ddl` mode)

| Artifact | Audience | Purpose |
|----------|----------|---------|
| **`migration.php`** (project root) | Human | **Manual** DB schema change log (what to apply on prod: fields, tables, indexes, `ALTER`, etc.). |
| **`.ai-oc-install/map/db_mapping.md`** | AI | **Primary curated DB model** for coding (tables the installer documented). |
| **`.ai-oc-install/map/db_tables/*.php`** (optional) | AI | **Machine snapshot**: full `CREATE TABLE` per table for exact column/type/index detail. |

After **any** schema change, update **both**: first the log in `migration.php` (per project rules), then the content of `.ai-oc-install/map/db_mapping.md`. Regenerate `db_map.php` / `db_tables/` when you refresh from the live database.

---

Mode is set during install (**Block 7** in `ai-oc-install.md`):

| Situation | Mode | What goes in `db_mapping.md` |
|-----------|------|------------------------------|
| **OpenCart / ocStore** — standard DDL collection | `ddl` | Full **`CREATE TABLE`** in `` ```sql `` blocks |
| User **explicitly skips** formal DDL (constraints, NDA, temporary) | `skipped` | Short text: formal table mapping not maintained; data context in **`ai-map.md`** |

After the `# DB Mapping` heading, add **`DB_MAPPING_MODE: ddl | skipped`**.

---

## Template — `ddl` mode (OpenCart / ocStore)

````markdown
# DB Mapping

**DB_MAPPING_MODE:** ddl

Mapping — full **`CREATE TABLE`** from dump / `SHOW CREATE TABLE`. No passwords or DB names.

Path to this file: **`.ai-oc-install/map/db_mapping.md`** (always, Claude or Cursor).

Table prefix — as in the database; in OpenCart PHP use `DB_PREFIX`.

---

## DDL

One table = one `` ```sql `` block.

```sql
CREATE TABLE `oc_example` (
  `example_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`example_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
```

---

## Notes

- 
````

---

## Template — `skipped` mode

```markdown
# DB Mapping

**DB_MAPPING_MODE:** skipped

Formal table mapping is **not maintained** (user choice during install).

**For AI:** see **`ai-map.md`** — custom tables, important fields, data conventions, constraints.

## Brief data notes (from user reply)

- 
```

---

## DDL format example (one table)

```sql
CREATE TABLE `oc_address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `company` varchar(40) NOT NULL,
  `address_1` varchar(128) NOT NULL,
  `address_2` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT 0,
  `zone_id` int(11) NOT NULL DEFAULT 0,
  `custom_field` text NOT NULL,
  PRIMARY KEY (`address_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
```

---

## Rules

1. Do not duplicate the full mapping in `ai-map.md` — there: reference, mode, and short context (for `skipped` — richer data description in `ai-map.md`).
2. **`ddl` mode:** schema change → update `.ai-oc-install/map/db_mapping.md` (current `CREATE TABLE`) + `migration.php` (prod journal).
3. **`skipped` mode:** data convention changes → update **`ai-map.md`**; in `db_mapping.md` only refine the "brief data notes" line if needed.
4. DDL **in parts** in chat (1/3, 2/3…) — merge into one **`.ai-oc-install/map/db_mapping.md`** after the last part.
5. No local dump in repo — OK; public git + sensitive structure — be careful committing `db_mapping.md`.
