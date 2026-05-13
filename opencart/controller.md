# OpenCart — Controller

## Controller role

The controller is only the conductor. It:
1. Receives the request
2. Loads models / languages / libraries as needed
3. Calls model methods
4. Passes data to the view

**Business logic, SQL, data processing — only in the model.**

Complex view data prep — **private methods** on this controller. If the same prep is needed in **several controllers** in **new** code (leave legacy alone without a task) — use a **helper**. Existing helpers may be **read**; **adding** or **changing** helpers — **only after explicit user approval** (ask first). Details — generated `code-style.md` (scheme `scheme-code-style.md`).

What belongs in a typical OpenCart controller and why a multi-model "pipeline" is wrong — [`main.md`](main.md) (section "Controller — typical role in OpenCart style").

---

## Catalog controller

### File structure

```php
<?php
class Controller{CustomDir}CurrencyRecalc extends Controller {

    public function index(): void {
        $this->load->language('{CUSTOM_DIR}/currency_recalc');
        $this->load->model('{CUSTOM_DIR}/currency_recalc');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = $this->_buildData();

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('{CUSTOM_DIR}/currency_recalc', $data));
    }

    private function _buildData(): array {
        // prepare data for view
        return [];
    }
}
```

### Catalog routing

```
route={CUSTOM_DIR}/currency_recalc
→ catalog/controller/{CUSTOM_DIR}/currency_recalc.php
→ class Controller{CustomDir}CurrencyRecalc
→ index() by default
```

Custom method:
```
route={CUSTOM_DIR}/currency_recalc/process
→ process()
```

### Reading input (catalog)

```php
// GET
$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
$search     = isset($this->request->get['search']) ? $this->db->escape($this->request->get['search']) : '';

// POST
$name = isset($this->request->post['name']) ? $this->db->escape($this->request->post['name']) : '';

// Method check
if ($this->request->server['REQUEST_METHOD'] === 'POST') {
    // handle form
}
```

---

## Admin controller

### Path and route

```
admin/controller/extension/module/{CUSTOM_DIR}_currency.php
→ route: extension/module/{CUSTOM_DIR}_currency
→ class: ControllerExtensionModule{CustomDir}Currency
```

### Admin controller structure

```php
<?php
class ControllerExtensionModule{CustomDir}Currency extends Controller {

    private string $route = 'extension/module/{CUSTOM_DIR}_currency';
    private array $error  = [];

    public function index(): void {
        $this->load->language($this->route);
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->_validate()) {
            $this->model_setting_setting->editSetting('{CUSTOM_DIR}_currency', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true));
        }

        $data = $this->_buildData();
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/{CUSTOM_DIR}_currency', $data));
    }

    private function _validate(): bool {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    private function _buildData(): array {
        $data = [];
        // breadcrumbs
        $data['breadcrumbs'] = [
            ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)],
            ['text' => $this->language->get('heading_title'), 'href' => $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true)],
        ];
        $data['action']      = $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel']      = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success']       = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);
        return $data;
    }
}
```

### Saving settings via `setting/setting`

```php
// Save — associative array under module code
$this->model_setting_setting->editSetting('{CUSTOM_DIR}_currency', $this->request->post);

// Read
$this->config->get('{CUSTOM_DIR}_currency_some_key');
// or
$this->model_setting_setting->getSettingValue('{CUSTOM_DIR}_currency_some_key');
```

### Admin URLs

```php
// always with user_token and true (HTTPS)
$this->url->link('extension/module/{CUSTOM_DIR}_currency', 'user_token=' . $this->session->data['user_token'], true);
```

### Permissions

After creating a new admin module — remind the user to add permissions:
> In admin: System → Users → User Groups → Administrator → add **access** and **modify** for `extension/module/{CUSTOM_DIR}_[name]`

---

## Language variables (version-specific rule)

> **Before applying rules below** — check `VERSION` in `.claude/project.md` (field `Platform`). Rules differ between 2.x and 3.x.


### OpenCart / ocStore 3.x — load only, do NOT assign to $data

In version 3.x the Twig template receives all language strings directly through the language object — **no need** to extract each variable into `$data`.

```php
// CORRECT for 3.x — load and stop
$this->load->language('extension/module/{CUSTOM_DIR}_currency');

// WRONG for 3.x — unnecessary manual extraction
$data['heading_title'] = $this->language->get('heading_title');
$data['text_edit']     = $this->language->get('text_edit');
// ...and so on for every key — do not do this
```

In the Twig template use `{{ heading_title }}` — it resolves through the language loader automatically.

**Exception — inline language use inside computed structures is still allowed:**

```php
// CORRECT even in 3.x — language is used inline inside a computed array, not extracted as a standalone $data key
$data['breadcrumbs'] = [
    ['text' => $this->language->get('text_home'), 'href' => $this->url->link(...)],
    ['text' => $this->language->get('heading_title'), 'href' => $this->url->link(...)],
];

// CORRECT — error/success are computed values, not plain language extractions
$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
$data['success']       = isset($this->session->data['success']) ? $this->session->data['success'] : '';
```

### OpenCart 2.x — assign to $data as usual

In version 2.x (`.tpl` templates) language variables **must** be passed through `$data`:

```php
$this->load->language('extension/module/{CUSTOM_DIR}_currency');
$data['heading_title'] = $this->language->get('heading_title');
```

---

## Shared rules (admin and catalog)

### FORBIDDEN in controller

```php
// SQL in controller
$this->db->query("SELECT ...");

// Admin model in catalog controller and vice versa
$this->load->model('admin/...');   // in catalog — forbidden

// Business logic in controller
if ($price > 0) { $discounted = $price * 0.9; ... } // → belongs in model

// oc_event without explicit request
$this->model_extension_event->addEvent(...);

// include/require
include(DIR_APPLICATION . 'model/...');
```

### Loading models

```php
// catalog loads only catalog models
$this->load->model('catalog/product');
$this->load->model('{CUSTOM_DIR}/my_module');

// admin loads admin models
$this->load->model('catalog/product');       // admin has its own catalog models
$this->load->model('setting/setting');
$this->load->model('extension/module/{CUSTOM_DIR}_currency');
```

### AJAX response

```php
public function ajaxAction(): void {
    $this->response->addHeader('Content-Type: application/json');
    $json = [];

    try {
        // logic via model
        $json['success'] = true;
        $json['data']    = $this->model_{CUSTOM_DIR}_something->getData();
    } catch (\Exception $e) {
        $json['error'] = $e->getMessage();
    }

    $this->response->setOutput(json_encode($json));
}
```
