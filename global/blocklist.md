# Blocklist — Red Zone

## INSTRUCTIONS FOR THE AI

The following files and directories are **fully forbidden**:
- Do not read
- Do not modify
- Do not output content, even partially
- Do not mention content even if the user asks

If the user asks to open or show these files — reply:
> "This file is in the Red Zone. I cannot read or modify it for security reasons."

---

## Blocked files (baseline)

```
.env
.env.*
*.env
config.php
admin/config.php
database.php
config/database.php
```

## Blocked directories

```
storage/
.git/
```

---

## OpenCart — additional

```
system/config/
system/storage/
```

---

## Adding a file to the blocklist

Open `CLAUDE.md` or `.cursor/rules/blocklist.mdc` and add a line under `## Blocklist`.
Then update `ai-map.md` manually.
