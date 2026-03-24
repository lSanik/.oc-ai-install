# OpenCart — JavaScript

## Загальне

OpenCart 2.x / 3.x використовує **jQuery**. Підключати інші фреймворки без явної потреби — не треба.

---

## Додавання скрипта через контролер

```php
// Catalog — у методі index() або _buildData()
$this->document->addScript('catalog/view/theme/' . $this->config->get('config_theme') . '/javascript/cactus/my-script.js');

// Admin
$this->document->addScript('view/javascript/cactus/my-admin-script.js');

// Стиль
$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_theme') . '/stylesheet/cactus/my-style.css');
```

Або безпосередньо у Twig в кінці файлу:

```twig
<script src="{{ 'catalog/view/theme/..../javascript/cactus/my-script.js' | raw }}"></script>
```

---

## AJAX до OC контролера

### Catalog

```javascript
$.ajax({
    url: 'index.php?route=cactus/my_module/ajaxMethod',
    type: 'POST',
    data: {
        product_id: productId
    },
    dataType: 'json',
    success: function(json) {
        if (json.error) {
            // обробка помилки
        }
        if (json.success) {
            // обробка успіху
        }
    }
});
```

### Admin (з user_token)

```javascript
$.ajax({
    url: 'index.php?route=extension/module/cactus_currency/ajaxMethod&user_token=' + userToken,
    type: 'POST',
    data: { key: value },
    dataType: 'json',
    success: function(json) {
        if (json.error) {
            $('#alert-container').html('<div class="alert alert-danger">' + json.error + '</div>');
        }
        if (json.success) {
            location.reload();
        }
    }
});
```

`userToken` — передавати з контролера через `$data['user_token']`.

---

## Розташування JS файлів

```
catalog/view/theme/[тема]/javascript/cactus/   ← кастомний JS каталогу
admin/view/javascript/cactus/                  ← кастомний JS адмінки
```

---

## Правила

- Не писати JS inline в PHP — тільки у Twig або окремому файлі
- Не підключати зовнішні CDN без явної потреби
- `var_dump`, `console.log` прибирати з продакшн коду
- jQuery — завжди через `$` або `jQuery`, не конфліктує з іншими бібліотеками через OC wrapper