# OpenCart — JavaScript

## General

OpenCart 2.x / 3.x uses **jQuery**. Do not add other frameworks without explicit need.

---

## Adding scripts from the controller

```php
// Catalog — in index() or _buildData()
$this->document->addScript('catalog/view/theme/' . $this->config->get('config_theme') . '/javascript/{CUSTOM_DIR}/my-script.js');

// Admin
$this->document->addScript('view/javascript/{CUSTOM_DIR}/my-admin-script.js');

// Style
$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_theme') . '/stylesheet/{CUSTOM_DIR}/my-style.css');
```

Or directly in Twig at the end:

```twig
<script src="{{ 'catalog/view/theme/..../javascript/{CUSTOM_DIR}/my-script.js' | raw }}"></script>
```

---

## AJAX to OC controllers

### Catalog

```javascript
$.ajax({
    url: 'index.php?route={CUSTOM_DIR}/my_module/ajaxMethod',
    type: 'POST',
    data: {
        product_id: productId
    },
    dataType: 'json',
    success: function(json) {
        if (json.error) {
            // handle error
        }
        if (json.success) {
            // handle success
        }
    }
});
```

### Admin (with user_token)

```javascript
$.ajax({
    url: 'index.php?route=extension/module/{CUSTOM_DIR}_currency/ajaxMethod&user_token=' + userToken,
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

Pass `userToken` from the controller via `$data['user_token']`.

---

## JS file locations

```
catalog/view/theme/[theme]/javascript/{CUSTOM_DIR}/   ← custom catalog JS
admin/view/javascript/{CUSTOM_DIR}/                   ← custom admin JS
```

---

## Rules

- Do not write JS inline in PHP — only in Twig or a separate file
- Do not load external CDNs without explicit need
- Remove `var_dump`, `console.log` from production code
- jQuery — use `$` or `jQuery`; OC wrapper avoids conflicts with other libs
