# OpenCart — Language

## Language file structure

Language files are PHP arrays. One file per controller/module.

```
catalog/language/[locale]/{CUSTOM_DIR}/[name].php
admin/language/[locale]/extension/module/{CUSTOM_DIR}_[name].php
```

Locales are set at install. Typical: `uk-ua`, `ru-ru`, `en-gb`.

---

## File structure

```php
<?php
// Headings
$_['heading_title']    = 'Purchase currencies';

// Text
$_['text_edit']        = 'Settings';
$_['text_success']     = 'Settings saved';
$_['text_loading']     = 'Loading...';

// Form fields
$_['entry_usd_rate']   = 'USD rate';
$_['entry_eur_rate']   = 'EUR rate';
$_['entry_updated']    = 'Last updated';

// Buttons
$_['button_save']      = 'Save';
$_['button_cancel']    = 'Cancel';
$_['button_refresh']   = 'Refresh rates';

// Errors
$_['error_permission'] = 'Warning: you do not have permission to modify settings!';
$_['error_rate']       = 'Rate must be greater than zero!';

// Table columns
$_['column_currency']  = 'Currency';
$_['column_rate']      = 'Rate';
$_['column_action']    = 'Action';
```

---

## Loading and usage

### In controller

```php
// Load
$this->load->language('{CUSTOM_DIR}/currency_recalc');           // catalog
$this->load->language('extension/module/{CUSTOM_DIR}_currency'); // admin
```

**3.x (ocStore / OpenCart 3.x):** just load — do NOT assign language strings to `$data`. Twig resolves them directly through the language loader.

**2.x:** assign each needed key to `$data` manually:

```php
// 2.x only
$data['heading_title'] = $this->language->get('heading_title');
$data['button_save']   = $this->language->get('button_save');
```

See full version rule → `controller.md` (section "Language variables").

### In view (Twig)

```twig
{{ heading_title }}
{{ entry_usd_rate }}
<button>{{ button_save }}</button>
```

---

## Multiple languages

If the project is multilingual — create a file for **each** locale.

```
admin/language/uk-ua/extension/module/{CUSTOM_DIR}_currency.php  ← primary
admin/language/ru-ru/extension/module/{CUSTOM_DIR}_currency.php  ← if ru-ru exists
admin/language/en-gb/extension/module/{CUSTOM_DIR}_currency.php  ← if en-gb exists
```

Project locales are set at install. If unknown — ask.

---

## Rules

- All user-visible strings — only via language files, not hardcoded in controller or view
- Keys lowercase with underscores: `heading_title`, `error_permission`
- Prefixes for structure: `text_`, `entry_`, `button_`, `error_`, `column_`, `tab_`
- Do not dedupe across files — each file is independent even if values match
