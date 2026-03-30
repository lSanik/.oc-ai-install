# Warning Zone — Yellow Zone

## INSTRUCTIONS FOR THE AI

Warning Zone — files you **may read**, but **edit only with explicit permission** from the user.

**Workflow when touching a Warning Zone file:**

1. Confirm the file is listed in the Warning Zone
2. Before any edit, output a warning:

```
 WARNING ZONE: [path to file]
Reason: [reason from ai-map.md]
Possible impact: [what could break]

Continue? (yes / no)
```

3. Wait for explicit user confirmation
4. After editing — remind them to verify behaviour manually

---

## Generating the Warning Zone during install

During installer block 8 — ask:

> Are there files that can be read but should only be edited with care?
>
> For each file provide:
> - Path to the file
> - What could break if something goes wrong
>
> **Examples:**
> ```
> system/library/seopro.php
> → Handles SEO URLs for the whole site.
>   Bug here = project SEO at risk.
>   Symptom: site URLs stop working.
>
> catalog/model/catalog/product.php (modified core)
> → Core product model, modified for custom fields.
>   Bug here = products not shown or not saved.
> ```

---

## Record structure in ai-map.md

```markdown
## Warning Zone

| File | Reason | Possible impact |
|------|--------|-----------------|
| system/library/seopro.php | SEO module, handles URLs | All site URLs stop working |
| catalog/model/catalog/product.php | Modified core, custom fields | Products not displayed |
```

---

## Warning Zone categories

### SEO-critical
Files that URL structure, meta tags, or sitemap depend on.
Bug = ranking loss or pages unavailable.

### Modified core
Core platform files changed by hand.
Bug = unpredictable behaviour across the site.

### Payment integrations
Files that process payments or talk to payment APIs.
Bug = lost transactions or checkout errors.

### Critical libraries
Libraries many modules depend on.
Bug = cascading errors across the site.

### Log files and DB change journal (read OK; do not edit without instruction)
Human source of truth; execution intentionally disabled where needed.
Example: `migration.php` in the OC project root.
- After `<?php` — first executable line is `die(0);` / `die();`
- Only **facts** of DB architecture changes: fields, tables, indexes, `ALTER`, etc. — to know what to apply on production
- Full schema model for the AI lives in **`.ai-oc-install/map/db_mapping.md`**; on schema change, update both artifacts
- PHP parses the whole file — keep SQL inside string literals; avoid syntax errors outside the header block
- AI reads for context; does not change entries without explicit user command
