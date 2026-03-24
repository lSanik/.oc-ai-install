# Схема — db_mapping.md

## ІНСТРУКЦІЯ ДЛЯ AI

Генеруй **один** файл **`db_mapping.md`** у каталозі обраного інструменту (як у `ai-oc-install.md`):

- Якщо `TOOL = claude` → `.claude/db_mapping.md`
- Якщо `TOOL = cursor` → `.cursor/db_mapping.md`

Не створюй дзеркало в іншому каталозі, якщо користувач не обрав обидва інструменти.

---

## Ролі артефактів (OpenCart / ocStore, режим `ddl`)

| Артефакт | Для кого | Призначення |
|----------|----------|-------------|
| **`migration.php`** (корінь проєкту) | Людина | Журнал **ручних** змін схеми БД (що накатити на прод: поля, таблиці, індекси, `ALTER` тощо). |
| **`db_mapping.md`** | ШІ | **Першоджерело моделі БД** проєкту (повний контекст схеми для роботи в коді). |

Після **будь-якої** зміни схеми БД оновлюй **обидва**: спочатку лог у `migration.php` (за правилами проєкту), потім актуальний вміст у `db_mapping.md`, далі `/update_ai_doc`.

---

Режим задається під час інсталяції (**Блок 7** `ai-oc-install.md`):

| Ситуація | Режим | Що кладемо в `db_mapping.md` |
|----------|--------|-----------------------------|
| **OpenCart / ocStore** — стандартний збір DDL | `ddl` | Повні **`CREATE TABLE`** у блоках `` ```sql `` |
| Користувач **явно не надає** формальний DDL (обмеження, NDA, тимчасово) | `skipped` | Короткий текст: формальний мапінг таблиць не ведеться; контекст даних — у **`ai-map.md`** |

Після заголовка `# DB Mapping` вкажи рядок **`DB_MAPPING_MODE: ddl | skipped`**.

---

## Шаблон — режим `ddl` (OpenCart / ocStore)

````markdown
# DB Mapping

**DB_MAPPING_MODE:** ddl

Мапінг — **повні `CREATE TABLE`** з дампу / `SHOW CREATE TABLE`. Без паролів і назв БД.

Шлях до цього файлу: `.claude/db_mapping.md` **або** `.cursor/db_mapping.md` (залежно від TOOL). Оновлюй **лише** той файл, який існує в проєкті.

Префікс таблиць — як у БД; у PHP OpenCart — `DB_PREFIX`.

---

## DDL

Одна таблиця = один блок `` ```sql ``.

```sql
CREATE TABLE `oc_example` (
  `example_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`example_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
```

---

## Нотатки

- 
````

---

## Шаблон — режим `skipped`

```markdown
# DB Mapping

**DB_MAPPING_MODE:** skipped

Формальний мапінг таблиць **не ведеться** (за вибором користувача під час інсталяції).

**Для AI:** див. **`ai-map.md`** — кастомні таблиці, важливі поля, домовленості про дані, обмеження.

## Коротко про дані (за відповіддю користувача)

- 
```

---

## Приклад формату DDL (одна таблиця)

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

## Правила

1. Не дублюй повний мапінг у `ai-map.md` — там посилання, режим і короткий контекст (для `skipped` — детальніший опис даних у `ai-map.md`).
2. **Режим `ddl`:** зміна схеми → онови `db_mapping.md` (актуальні `CREATE TABLE`) + `migration.php` (журнал для проду) + `/update_ai_doc`.
3. **Режим `skipped`:** зміни в домовленостях про дані → оновлюй **`ai-map.md`**, у `db_mapping.md` лише за потреби уточни рядок «коротко про дані».
4. DDL **частинами** у чаті (1/3, 2/3…) — збирай у один вміст **вашого** `db_mapping.md` після останньої частини.
5. Без локального дампу в репо — ок; публічний git + чутлива структура — обережно з комітом `db_mapping.md`.
