# Схема — ai-changelog.md

## ІНСТРУКЦІЯ ДЛЯ AI

`ai-changelog.md` — журнал змін по сесіях роботи з AI.
Замінює git log для проєктів без git або доповнює його.

При інсталяції — генеруй з першим записом про інсталяцію.
При `/update_ai_doc` — додавай новий запис зверху (новіше — вище).

---

## ШАБЛОН

```markdown
# AI Changelog — [назва проєкту]

---

## [YYYY-MM-DD] — Initial Setup

**Що зроблено:**
- Встановлено AI-конфігурацію проєкту
- Проаналізовано структуру проєкту
- Описано Warning Zone: [перелік або "немає"]
- Описано кастомні модулі: [перелік або "немає"]

**Файли створено:**
- **Claude:** `.claude/CLAUDE.md`, `.claude/settings.json`, `.claude/ai-map.md`, `.claude/ai-task.md`, `.claude/ai-changelog.md`, `.claude/ai-decisions.md`, `.claude/db_mapping.md`, `.claude/code-style.md`, `.claude/commands/*` (якщо генерувались)
- **Cursor:** `.cursor/rules/main.mdc`, `blocklist.mdc`, `warning-zone.mdc`, `commands.mdc`, `.cursor/ai-map.md`, `.cursor/ai-task.md`, `.cursor/ai-changelog.md`, `.cursor/ai-decisions.md`, `.cursor/db_mapping.md`, `.cursor/code-style.md`
- [інші файли за шаблоном інсталяції]

**Відкриті питання:**
- [питання або "Немає"]

---

## [YYYY-MM-DD] — [Назва задачі]

**Що зроблено:**
- [опис зміни 1]
- [опис зміни 2]

**Файли змінено:**
- [створено]: [шлях]
- [змінено]: [шлях]
- [видалено]: [шлях]

**Оновлено AI-файли:**
- [файл]: [коротко що змінено]

**Відкриті питання:**
- [питання або "Немає"]

---
```

---

## Правила ведення

1. Новіший запис — зверху (зворотній хронологічний порядок)
2. "Файли змінено" — тільки файли проєкту, не AI-файли
3. "Оновлено AI-файли" — тільки AI-файли (`CLAUDE.md` / `.cursor/rules/*.mdc`, `ai-map.md` тощо)
4. Не видаляй старі записи — це архів
