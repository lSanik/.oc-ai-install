# Схема — CLAUDE.md

## ІНСТРУКЦІЯ ДЛЯ AI

Використовуй цю схему для генерації `CLAUDE.md`.
`CLAUDE.md` — тонкий файл-точка входу. Правила платформи OpenCart лежать у `.ai-oc-install/opencart/`; у цей файл підключається лише **`main.md`** плюс blocklist — решту файлів з `opencart/` робочий асистент відкриває **вибірково** під задачу.
Проєктні дані (версія, середовище, обмеження) зберігаються в `project.md` (PERSISTENT).

---

## ШАБЛОН

```markdown
# Project: [назва проєкту або домен]

Ти дуже професійний і досвідчений розробник Opencart системи.

@.claude/project.md
@.claude/ai-map.md
@.claude/code-style.md

@.ai-oc-install/opencart/main.md

@.ai-oc-install/global/blocklist.md

**Перед виконанням і аналізом задачі** обов’язково прочитай з `.ai-oc-install/opencart/` **лише** файли, релевантні поточній задачі (не підвантажуй усі `.md` одразу). Наприклад: PHP — `php.md`; JS — `js.md`; CSS — `css.md`; контролери / моделі / шаблони — `controller.md`, `model.md`, `view.md`; мови — `language.md`; system — `system-library.md`; БД — `mysql.md` і **`.ai-oc-install/map/db_mapping.md`**; адмінка — `admin.md`; вітрина — `catalog.md`.

---

## AI Files

PERSISTENT — ніколи не видаляти і не перезаписувати при reinstall:
- `.claude/project.md` — дані проєкту, warning zone, обмеження
- `.claude/ai-map.md` — карта кастомних модулів і відхилень від стандарту
- `.ai-oc-install/map/db_mapping.md` — опис / DDL кастомних таблиць БД (спільно для Claude і Cursor)
- `.ai-oc-install/map/*.php` — PHP файли таблиць від getMap.php (за наявності)

REGENERATABLE — перезаписуються при reinstall:
- `.claude/CLAUDE.md` — цей файл
- `.claude/code-style.md` — правила коду
```

---

## Правила генерації

1. `CLAUDE.md` — тільки @imports, абзац про вибіркове читання та секція AI Files. Не копіювати правила з `opencart/` сюди.
2. При генерації **не** читай вміст `.ai-oc-install/opencart/*.md` — відтвори шаблон `@`-рядків як вище.
3. `project.md` — генерується окремо за схемою `scheme-project.md`
4. `code-style.md` — генерується за схемою `scheme-code-style.md`
5. Не додавати секцій яких немає в шаблоні
