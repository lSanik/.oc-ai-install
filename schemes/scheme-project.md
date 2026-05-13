# Scheme — project.md

## INSTRUCTIONS FOR THE AI

This file is the template for generating `project.md`.
`project.md` is **PERSISTENT** — **never overwrite** on reinstall.
Filled once during install. Updated only when the user explicitly asks.

---

## TEMPLATE

```markdown
# Project Data

> Generated: [date]
> Tool: [TOOL]

---

## Install Config

- Platform: OpenCart / ocStore [VERSION]
- OCSTORE: [yes | no]
- PHP: [PHP]
- OCMOD_MERGED: [yes | no | unknown]
- Theme: [THEME]
- Custom folder: [CUSTOM_DIR]
- Languages: [LANGUAGES] | Default: [DEFAULT_LANG]
- Environment: [ENV]
- Git: [GIT] | .gitignore: [GITIGNORE]
- DB Mapping Mode: [ddl | skipped]

---

## Warning Zone

[If there are Warning Files — for each:]
### [path to file]
- Reason: [reason]
- Impact: [what could break]
- Action: warn the user and wait for confirmation before editing

[Always — for OpenCart / ocStore:]
### migration.php
- Reason: manual DB schema change log for production rollout
- Format: `die(0);` as first line after `<?php`; newer entries at the top; SQL in string literals
- Action: AI reads for context; changes only on explicit user command (see `opencart/model.md`). Never edit arbitrary data without instruction.

[If nothing else:]
None at this time.

---

## Project Restrictions

[Legacy limits, business rules, custom solutions from Block 9.]
[If none — write "No project-specific restrictions at this time."]

---

## Gotchas

[Important project quirks to know.]
[If none — write "No specific gotchas at this time."]
```

---

## Generation rules

1. Fill all sections from collected data
2. If data is missing — use `unknown` or a short explanation
3. Save as `project.md` under `.claude/` or `.cursor/` depending on `TOOL`
4. **This is PERSISTENT — do not overwrite on reinstall**
