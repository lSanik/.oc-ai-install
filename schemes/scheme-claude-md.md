# Схема — CLAUDE.md

## ІНСТРУКЦІЯ ДЛЯ AI

Використовуй цю схему для генерації `CLAUDE.md`.
`CLAUDE.md` — тонкий файл-точка входу. Всі правила і патерни OpenCart підключаються через @imports з `.ai-oc-install/`.
Проєктні дані (версія, середовище, обмеження) зберігаються в `project.md` (PERSISTENT).

---

## ШАБЛОН

```markdown
# Project: [назва проєкту або домен]

Ти дуже професійний і досвідчений розробник Opencart системи.

@.claude/project.md

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

---

## AI Files

PERSISTENT — ніколи не видаляти і не перезаписувати при reinstall:
- `.claude/project.md` — дані проєкту, warning zone, обмеження
- `.claude/ai-map.md` — карта кастомних модулів і відхилень від стандарту
- `.claude/ai-task.md` — поточна задача
- `.claude/ai-changelog.md` — журнал змін
- `.claude/ai-decisions.md` — архітектурні рішення
- `.claude/db_mapping.md` — опис кастомних таблиць БД
- `.claude/map/` — PHP файли таблиць, згенеровані getMap.php

REGENERATABLE — перезаписуються при reinstall:
- `.claude/CLAUDE.md` — цей файл
- `.claude/code-style.md` — правила коду
- `.claude/commands/` — команди AI
```

---

## Правила генерації

1. `CLAUDE.md` — тільки @imports і секція AI Files. Не копіювати правила сюди.
2. `project.md` — генерується окремо за схемою `scheme-project.md`
3. `code-style.md` — генерується за схемою `scheme-code-style.md`
4. Команди — генеруються за інструкцією в `global/commands.md` (розділ «Розбиття для Claude Code»)
5. Не додавати секцій яких немає в шаблоні