# OpenCart — Admin

## General

Admin (`admin/`) is a protected area for managers and administrators. All routes require `user_token`.

Custom admin module file layout:

```
admin/controller/extension/module/cactus/[name].php
admin/model/extension/module/cactus/[name].php
admin/view/template/extension/module/cactus/[name].twig
admin/language/[locale]/extension/module/cactus/[name].php
```

Route: `extension/module/cactus/[name]`

---

## Registering the module as an extension

After creating the module — remind the user:
1. Admin → Extensions → Extensions → type "Modules" → find and install `cactus/[name]`
2. Or: System → Users → User Groups → Administrator → add **access** and **modify** for `extension/module/cactus/[name]`

---

## Settings

```php
// Save module settings
$this->load->model('setting/setting');
$this->model_setting_setting->editSetting('cactus_mymodule', $this->request->post);

// Read anywhere in OC
$value = $this->config->get('cactus_mymodule_some_key');

// Or via model
$settings = $this->model_setting_setting->getSetting('cactus_mymodule');
```

Setting keys — always prefixed with the module: `cactus_[name]_[key]`.

---

## Admin menu

Adding a menu item via `admin/language/[locale]/extension/module/cactus/[name].php`:

```php
<?php
// If the module needs its own menu entry
// → use OC Events or the standard module mechanism
// → do not hardcode into core templates
```

Simple modules: Extensions → Modules → installed module.

---

## Permissions

```php
// Check in controller before action
if (!$this->user->hasPermission('modify', 'extension/module/cactus_mymodule')) {
    $this->error['warning'] = $this->language->get('error_permission');
}

// Read permission
if (!$this->user->hasPermission('access', 'extension/module/cactus_mymodule')) {
    $this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
}
```

---

## user_token

Every admin URL must include `user_token`:

```php
// Build URL
$this->url->link('extension/module/cactus_mymodule', 'user_token=' . $this->session->data['user_token'], true);

// Pass to Twig
$data['action'] = $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true);
$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
```

---

## Sessions and flash messages

```php
// Set success before redirect
$this->session->data['success'] = $this->language->get('text_success');
$this->response->redirect($this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true));

// In _buildData() read and clear
$data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
unset($this->session->data['success']);
```

---

## Standard admin controller structure

Full example — `controller.md` (section "Admin controller").

Short:
- `index()` — main: validate POST → save → redirect; or buildData → view
- `_validate()` — private validation
- `_buildData()` — private data for Twig
- Breadcrumbs — always: Dashboard → module title
