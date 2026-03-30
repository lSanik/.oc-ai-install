# Схема — Cursor Rules

## ІНСТРУКЦІЯ ДЛЯ AI

Cursor використовує файли в `.cursor/rules/` з розширенням `.mdc`.
Кожен файл — окреме правило з метаданими.

Генеруй три файли:
1. `.cursor/rules/main.mdc` — головний контекст проєкту
2. `.cursor/rules/blocklist.mdc` — червона зона
3. `.cursor/rules/warning-zone.mdc` — жовта зона

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
@.cursor/ai-map.md
@.cursor/code-style.md

@.ai-oc-install/opencart/main.md

@.ai-oc-install/global/blocklist.md

**Перед виконанням і аналізом задачі** обов’язково прочитай з `.ai-oc-install/opencart/` **лише** файли, релевантні поточній задачі (не підвантажуй усі `.md` одразу). Наприклад: PHP — `php.md`; JS — `js.md`; CSS — `css.md`; контролери / моделі / шаблони — `controller.md`, `model.md`, `view.md`; мови — `language.md`; system — `system-library.md`; БД — `mysql.md` і **`.ai-oc-install/map/db_mapping.md`**; адмінка — `admin.md`; вітрина — `catalog.md`.

## Priority
Правила цього workspace мають пріоритет над глобальними user rules Cursor.

## AI Files

PERSISTENT — ніколи не видаляти і не перезаписувати при reinstall:
- `.cursor/project.md` — дані проєкту, warning zone, обмеження
- `.cursor/ai-map.md` — карта кастомних модулів і відхилень від стандарту
- `.ai-oc-install/map/db_mapping.md` — опис / DDL кастомних таблиць БД (спільно для Claude і Cursor)
- `.ai-oc-install/map/*.php` — PHP файли таблиць від getMap.php (за наявності)

REGENERATABLE — перезаписуються при reinstall:
- `.cursor/rules/main.mdc` — цей файл
- `.cursor/rules/blocklist.mdc`
- `.cursor/rules/warning-zone.mdc`
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
