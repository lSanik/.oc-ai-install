# OpenCart — Головний файл

## ІНСТРУКЦІЯ ДЛЯ AI

Ти працюєш з проєктом на OpenCart / ocStore.

**При старті завжди читай всі файли платформи:**
- `.ai-oc-install/opencart/main.md` ← цей файл
- `.ai-oc-install/opencart/controller.md`
- `.ai-oc-install/opencart/model.md`
- `.ai-oc-install/opencart/view.md`
- `.ai-oc-install/opencart/language.md`
- `.ai-oc-install/opencart/system-library.md`

---

## Визначення версії

Перш за все — визнач версію OC. Читай `index.php` у корені проєкту:

```php
// шукай рядок типу:
define('VERSION', '3.0.3.7');
```

Якщо не знайшов або немає доступу — запитай користувача.

Запиши: `VERSION = 2.x | 3.x | 4.x`

| Версія | Шаблони | Кодстайл | Примітки |
|--------|---------|----------|----------|
| 2.x | `.tpl` (PHP) | PSR-2 | Старий синтаксис |
| 3.x | `.twig` | PSR-2 | Найпоширеніший |
| 4.x | `.twig` | PSR-12 | Новий namespace |

Документація шару view у [`view.md`](view.md) — **лише Twig**. `.tpl` (2.x) — легасі; міграцію на Twig не робимо без явної задачі.

---

## Архітектура OpenCart

OpenCart використовує **MVC(L)** — Model, View, Controller, Language.

```
catalog/                  ← фронтенд (магазин для покупців)
  controller/
  model/
  view/
  language/

admin/                    ← адмінка (для менеджерів)
  controller/
  model/
  view/
  language/

system/
  library/                ← бібліотеки (з обережністю — див. system-library.md)
  engine/                 ← ядро MVC (НЕ ЧІПАТИ)
  storage/                ← кеш, логи, сесії (НЕ ЧІПАТИ, в blocklist)
```

---

## Cactus — стандарт кастомного коду

**Cactus** — патерн для всього нового кастомного коду. Ніколи не змішувати з ядром OC.

### Catalog (фронтенд)
```
catalog/controller/cactus/[name].php
catalog/model/cactus/[name].php
catalog/view/theme/[тема]/template/cactus/[name].twig
catalog/language/[locale]/cactus/[name].php
```

### Admin (адмінка)
Адмін-модулі Cactus реєструються як розширення OC:
```
admin/controller/extension/module/cactus/[name].php
admin/model/extension/module/cactus/[name].php
admin/view/template/extension/module/cactus/[name].twig
admin/language/[locale]/extension/module/cactus/[name].php
```
Маршрут: `extension/module/cactus/[name]`


### Dev / Debug (тільки локально)
```
catalog/controller/cactus/dev/
catalog/controller/cactus/dev/scripts/   ← одноразові скрипти зміни даних
```

---

## OCMOD статус

Запитай або визнач: `OCMOD_MERGED = yes | no`

**OCMOD_MERGED = no** — модифікації живуть в XML, застосовані копії в `system/storage/modification/`
- Не правити файли в `system/storage/modification/` — вони перегенеруються
- Зміни вносити в оригінальний `.ocmod.xml`
- Після зміни XML — нагадати користувачу: Адмін → Extensions → Modifications → **Refresh**

**OCMOD_MERGED = yes** — модифікації злиті напряму в файли ядра
- Правити файли напряму
- Позначати такі файли в `ai-map.md` як "модифіковане ядро"

---

## Завантаження компонентів

```php
// OK: правильно
$this->load->model('catalog/product');
$this->load->library('session');
$this->load->language('catalog/product');

// НЕ можна:
include('...');
require('...');
```

---

## База даних — базові правила

- Завжди `$this->db->escape()` для всіх вхідних даних
- Префікс таблиць через `DB_PREFIX`, ніколи не хардкодити `oc_`
- Запити — тільки в моделях, ніколи в контролерах
- При будь-якій зміні структури БД → записати в `migration.php` (див. model.md)

---

## Контролер — типова роль у стилі OpenCart (**новий** код)

Правила нижче — для **нового** коду (Cactus тощо). **Легасі не переписуємо**, якщо користувач **явно** не просить.

**Це не бізнес-логіка і не SQL.** У контролері нормально:

- `$this->load->language`, `$this->load->model`, `$this->load->library` та інші завантажувачі;
- виклик **однієї** релевантної моделі: отримати дані для виводу, передати в модель дані на збереження тощо;
- збір `$data` для Twig: прості `foreach`, підстановка URL зображень, breadcrumbs, заголовки;
- `$this->load->controller('common/header')` / `footer` / `column_left` і `$this->load->view(...)`.

**Неприпустимо:** ганяти результат **послідовно через кілька моделей** («модель A → у модель B → у модель C») в рамках одного сценарію. Таку координацію **зводити до однієї моделі** (один вхідний виклик з контролера) або явно розмежувати в задачі — без «конвеєра» в контролері.

Деталі структури контролера — [`controller.md`](controller.md); шаблони — [`view.md`](view.md).

---

## ЗАБОРОНЕНО (глобально для всього OC)

- `oc_event` — не використовувати якщо користувач явно не просить
- `include` / `require` для компонентів OC — тільки `$this->load->`
- Хардкодити префікс `oc_` — тільки `DB_PREFIX`
- **Бізнес-логіка, SQL, складні агрегації** — у **моделі** (див. [`model.md`](model.md)), не в контролері
- Підключати моделі з `admin/` в контролерах `catalog/` і навпаки
- Правити `system/engine/` — ніколи
- Правити `system/storage/` — ніколи (в blocklist)
- `var_dump`, `print_r`, `echo` для дебагу в продакшн коді
