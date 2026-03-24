# OpenCart — System / Library

## Головне правило

```
system/  →  НЕ ЧІПАТИ
            виняток: system/library/ — тільки з явного дозволу
```

---

## system/library/ — з дозволу

Бібліотеки — це спільні компоненти які використовуються по всьому проєкту.
Помилка тут = каскадний збій по всьому сайту.

**Перед будь-якою правкою файлу в `system/library/` — обов'язково попередити:**

```
 SYSTEM/LIBRARY: [шлях до файлу]
Це спільна бібліотека. Зміна може вплинути на весь сайт.
Продовжити? (так / ні)
```

### Коли може знадобитись

- Додати нову кастомну бібліотеку: `system/library/cactus/[назва].php`
- Розширити існуючу бібліотеку (рідко, з обережністю)
- Підключити стороннє рішення

### Кастомні бібліотеки

Новий функціонал — в підпапку cactus:

```
system/library/cactus/currency_helper.php
system/library/cactus/image_processor.php
```

Підключення:
```php
$this->load->library('cactus/currency_helper');
// доступно як $this->cactus_currency_helper
```

### Відомі критичні бібліотеки (Warning Zone за замовчуванням)

Ці файли є в більшості OC проєктів і критичні:

| Файл | Чому критичний |
|------|---------------|
| `system/library/seopro.php` | SEO URL по всьому сайту — баг = всі URL ламаються |
| `system/library/db/mysqli.php` | DB підключення — баг = сайт падає |
| `system/library/session.php` | Сесії — баг = логін не працює |
| `system/library/cache.php` | Кеш — баг = сповільнення або некоректні дані |

Додаткові Warning Zone файли — визначаються при інсталяції конкретного проєкту.

---

## system/engine/ — НІКОЛИ

```
system/engine/action.php
system/engine/controller.php
system/engine/front.php
system/engine/loader.php
system/engine/model.php
system/engine/registry.php
system/engine/router.php
```

Це серце MVC. Правка тут = непередбачувана поведінка по всьому сайту.
**Не читати, не пропонувати правки, не обговорювати зміни без крайньої потреби.**

---

## system/storage/ — ЗАБОРОНЕНО (Blocklist)

```
system/storage/cache/
system/storage/logs/
system/storage/modification/   ← згенеровані OCMOD файли
system/storage/session/
system/storage/upload/
```

В blocklist. Не читати, не чіпати, не виводити вміст.

---

## Все інше в system/ — НЕ ЧІПАТИ

```
system/config/    ← конфіги (в blocklist частково)
system/helper/    ← хелпери (читати можна, правити — тільки з питанням)
system/vendor/    ← Composer залежності
```

Якщо задача вимагає правки чогось в `system/` поза `library/` — **спочатку запитай** користувача чи це дійсно потрібно і чи немає іншого способу.
