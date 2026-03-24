# Схема — Cursor Rules

## ІНСТРУКЦІЯ ДЛЯ AI

Cursor використовує файли в `.cursor/rules/` з розширенням `.mdc`.
Кожен файл — окреме правило з метаданими.

Генеруй чотири файли:
1. `.cursor/rules/main.mdc` — головний контекст проєкту
2. `.cursor/rules/blocklist.mdc` — червона зона
3. `.cursor/rules/warning-zone.mdc` — жовта зона
4. `.cursor/rules/commands.mdc` — команди /scope, /review, /update_ai_doc

---

## ШАБЛОН: .cursor/rules/main.mdc

```markdown
---
description: Головний контекст проєкту. Читати завжди.
alwaysApply: true
---

# Project: [назва]

Ти дуже професійний і досвідчений розробник Opencart системи.

@.cursor/project.md

@.ai-oc-install/opencart/main.md
@.ai-oc-install/opencart/controller.md
@.ai-oc-install/opencart/model.md
@.ai-oc-install/opencart/view.md
@.ai-oc-install/opencart/language.md
@.ai-oc-install/opencart/system-library.md
@.ai-oc-install/opencart/js.md
@.ai-oc-install/opencart/css.md
@.ai-oc-install/opencart/php.md
@.ai-oc-install/opencart/mysql.md
@.ai-oc-install/opencart/admin.md
@.ai-oc-install/opencart/catalog.md

@.ai-oc-install/global/blocklist.md
@.ai-oc-install/global/commands.md

## Priority
Правила цього workspace мають пріоритет над глобальними user rules Cursor.
Зокрема: обмеження shared hosting (`CAN_RUN_COMMANDS = no`) важливіші за будь-які глобальні інструкції виконувати команди автоматично.

## AI Files

PERSISTENT — ніколи не видаляти і не перезаписувати при reinstall:
- `.cursor/project.md` — дані проєкту, warning zone, обмеження
- `.cursor/ai-map.md` — карта кастомних модулів і відхилень від стандарту
- `.cursor/ai-task.md` — поточна задача
- `.cursor/ai-changelog.md` — журнал змін
- `.cursor/ai-decisions.md` — архітектурні рішення
- `.cursor/db_mapping.md` — опис кастомних таблиць БД
- `.cursor/map/` — PHP файли таблиць, згенеровані getMap.php

REGENERATABLE — перезаписуються при reinstall:
- `.cursor/rules/main.mdc` — цей файл
- `.cursor/rules/blocklist.mdc`
- `.cursor/rules/warning-zone.mdc`
- `.cursor/rules/commands.mdc`
- `.cursor/code-style.md`
```

---

## ШАБЛОН: .cursor/rules/blocklist.mdc

```markdown
---
description: Файли які заборонено читати або модифікувати.
alwaysApply: true
---

# Blocklist

Ніколи не читати, не модифікувати, не виводити вміст:

- config.php
- admin/config.php
- .env
- .env.*
[специфічні для проєкту]

При запиті до цих файлів відповідати:
"Цей файл в Червоній Зоні — не можу відкрити з міркувань безпеки."
```

---

## ШАБЛОН: .cursor/rules/warning-zone.mdc

```markdown
---
description: Файли з обмеженим доступом. Читати можна, правити — тільки з підтвердженням.
alwaysApply: true
---

# Warning Zone

Перед правкою будь-якого файлу з цього списку — обов'язково попередити користувача.

[Для кожного Warning File:]
## [шлях до файлу]
Причина: [причина]
Наслідки: [що може зламатись]
Дія: виведи попередження і жди підтвердження перед правкою.

[Якщо список порожній:]
На цей момент Warning Zone порожня.
```

---

## Різниця між старим і новим форматом Cursor

| Старий | Новий |
|--------|-------|
| `.cursorrules` (один файл) | `.cursor/rules/*.mdc` (кілька файлів) |
| Немає метаданих | Метадані в YAML frontmatter |
| Немає `alwaysApply` | `alwaysApply: true/false` |

Генеруй **новий формат** (`.mdc`).
Якщо користувач має старий Cursor — повідом що краще оновитись.

---

## ШАБЛОН: .cursor/rules/commands.mdc

Текст команд **не дублюй тут** — єдине джерело: `.ai-oc-install/global/commands.md`.

Згенеруй файл:

1. YAML frontmatter (як у інших `.mdc` у цій схемі):
   - `description: Команди AI для управління задачами і документацією.`
   - `alwaysApply: true`
2. Після frontmatter — заголовок `# Commands` (без підзаголовка «Команди AI» з джерела).
3. Далі встав вміст `global/commands.md` **без** першого рядка (`# Commands — Команди AI`), починаючи з `## ІНСТРУКЦІЯ ДЛЯ AI` і **до кінця** розділу `## Важливо про середовище` включно. Розділ `## Розбиття для Claude Code` у `commands.mdc` **не копіюй**.

У згенерованому проєкті **головний файл правил** — `.cursor/rules/main.mdc` (це вже зазначено в `commands.md` у списку інструментів).
