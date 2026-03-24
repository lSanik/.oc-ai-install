# OpenCart — Controller

## Роль контролера

Контролер — тільки диригент. Він:
1. Отримує запит
2. Завантажує потрібні моделі / мови / бібліотеки
3. Викликає методи моделі
4. Передає дані у View

**Бізнес-логіка, SQL, обробка даних — тільки в моделі.**

Підготовка даних для view: складні гілки — у **приватні методи** цього контролера. Якщо та сама підготовка потрібна у **кількох контролерах** у **новому** коді (легасі без задачі не чіпати) — **helper**. Наявні helper **читати** можна; **додавати** або **змінювати** helper — **лише після явної згоди користувача** (спершу запитай). Деталі — у згенерованому `code-style.md` (схема `scheme-code-style.md`).

Що саме входить у типову роль контролера в OpenCart і чому неприпустимий «конвеєр» між кількома моделями — [`main.md`](main.md) (розділ «Контролер — типова роль у стилі OpenCart»).

---

## Catalog контролер

### Структура файлу

```php
<?php
class ControllerCactusCurrencyRecalc extends Controller {

    public function index(): void {
        $this->load->language('cactus/currency_recalc');
        $this->load->model('cactus/currency_recalc');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = $this->_buildData();

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('cactus/currency_recalc', $data));
    }

    private function _buildData(): array {
        // підготовка даних для view
        return [];
    }
}
```

### Маршрутизація catalog

```
route=cactus/currency_recalc
→ catalog/controller/cactus/currency_recalc.php
→ class ControllerCactusCurrencyRecalc
→ метод index() за замовчуванням
```

Кастомний метод:
```
route=cactus/currency_recalc/process
→ метод process()
```

### Читання вхідних даних (catalog)

```php
// GET
$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
$search     = isset($this->request->get['search']) ? $this->db->escape($this->request->get['search']) : '';

// POST
$name = isset($this->request->post['name']) ? $this->db->escape($this->request->post['name']) : '';

// Перевірка методу
if ($this->request->server['REQUEST_METHOD'] === 'POST') {
    // обробка форми
}
```

---

## Admin контролер

### Розташування та маршрут

```
admin/controller/extension/module/cactus_currency.php
→ маршрут: extension/module/cactus_currency
→ клас: ControllerExtensionModuleCactusCurrency
```

### Структура admin контролера

```php
<?php
class ControllerExtensionModuleCactusCurrency extends Controller {

    private string $route = 'extension/module/cactus_currency';
    private array $error  = [];

    public function index(): void {
        $this->load->language($this->route);
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->_validate()) {
            $this->model_setting_setting->editSetting('cactus_currency', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true));
        }

        $data = $this->_buildData();
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/cactus_currency', $data));
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

### Збереження налаштувань через `setting/setting`

```php
// Зберегти — асоціативний масив під ключем (code)
$this->model_setting_setting->editSetting('cactus_currency', $this->request->post);

// Читати
$this->config->get('cactus_currency_some_key');
// або
$this->model_setting_setting->getSettingValue('cactus_currency_some_key');
```

### Посилання в адмінці

```php
// завжди з user_token і true (HTTPS)
$this->url->link('extension/module/cactus_currency', 'user_token=' . $this->session->data['user_token'], true);
```

### Права доступу

Після створення нового адмін-модуля — нагадати користувачу додати права:
> В адмінці: System → Users → User Groups → Administrator → додати **access** і **modify** для `extension/module/cactus_[назва]`

---

## Спільні правила (admin і catalog)

### ЗАБОРОНЕНО в контролері

```php
// SQL в контролері
$this->db->query("SELECT ...");

// Модель admin в catalog контролері і навпаки
$this->load->model('admin/...');   // в catalog — заборонено

// Бізнес-логіка в контролері
if ($price > 0) { $discounted = $price * 0.9; ... } // → це в модель

// oc_event без явного прохання
$this->model_extension_event->addEvent(...);

// include/require
include(DIR_APPLICATION . 'model/...');
```

### Завантаження моделей

```php
// catalog завантажує тільки catalog моделі
$this->load->model('catalog/product');       // 
$this->load->model('cactus/my_module');      // 

// admin завантажує admin моделі
$this->load->model('catalog/product');       //  (в admin є свої catalog моделі)
$this->load->model('setting/setting');       // 
$this->load->model('extension/module/cactus_currency'); // 
```

### AJAX відповідь

```php
public function ajaxAction(): void {
    $this->response->addHeader('Content-Type: application/json');
    $json = [];

    try {
        // логіка через модель
        $json['success'] = true;
        $json['data']    = $this->model_cactus_something->getData();
    } catch (\Exception $e) {
        $json['error'] = $e->getMessage();
    }

    $this->response->setOutput(json_encode($json));
}
```
