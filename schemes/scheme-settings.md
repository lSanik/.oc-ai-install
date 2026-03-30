# Scheme — settings.json

## INSTRUCTIONS FOR THE AI

Generate `.claude/settings.json` — only when `TOOL = claude`.

---

## Generation logic

### permissions.deny — always

Add deny entries for blocklist files (align with `global/blocklist.md` baseline files):

```
"Read(./config.php)", "Edit(./config.php)", "Write(./config.php)"
"Read(./admin/config.php)", "Edit(./admin/config.php)", "Write(./admin/config.php)"
"Read(./database.php)", "Edit(./database.php)", "Write(./database.php)"
"Read(./config/database.php)", "Edit(./config/database.php)", "Write(./config/database.php)"
"Read(./.env)", "Edit(./.env)", "Write(./.env)"
"Read(./.env.*)", "Edit(./.env.*)", "Write(./.env.*)"
```

If the user adds extra blocklist paths during install — add them with the same pattern.

---

## Template

```json
{
  "permissions": {
    "deny": [
      "Read(./config.php)",
      "Edit(./config.php)",
      "Write(./config.php)",
      "Read(./admin/config.php)",
      "Edit(./admin/config.php)",
      "Write(./admin/config.php)",
      "Read(./database.php)",
      "Edit(./database.php)",
      "Write(./database.php)",
      "Read(./config/database.php)",
      "Edit(./config/database.php)",
      "Write(./config/database.php)",
      "Read(./.env)",
      "Edit(./.env)",
      "Write(./.env)",
      "Read(./.env.*)",
      "Edit(./.env.*)",
      "Write(./.env.*)"
    ]
  }
}
```

---

## Generation rules

1. Only for `TOOL = claude` — Cursor does not use this file
2. Do not add empty sections or comments in JSON
3. Do not add other sections (model, hooks, env) — too project-specific
