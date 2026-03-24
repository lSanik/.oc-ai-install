# OpenCart — Admin

## Загальне

Адмінка (`admin/`) — захищена зона для менеджерів і адміністраторів. Всі маршрути потребують `user_token`.

Структура файлів кастомного адмін-модуля:

```
admin/controller/extension/module/cactus/[name].php
admin/model/extension/module/cactus/[name].php
admin/view/template/extension/module/cactus/[name].twig
admin/language/[locale]/extension/module/cactus/[name].php
```

Маршрут: `extension/module/cactus/[name]`

---

## Реєстрація модуля як розширення

Після створення модуля — нагадати користувачу:
1. Адмін → Extensions → Extensions → тип "Modules" → знайти і встановити `cactus/[name]`
2. Або: System → Users → User Groups → Administrator → додати **access** і **modify** для `extension/module/cactus/[name]`

---

## Налаштування (Settings)

```php
// Зберегти налаштування модуля
$this->load->model('setting/setting');
$this->model_setting_setting->editSetting('cactus_mymodule', $this->request->post);

// Читати в будь-якому місці OC
$value = $this->config->get('cactus_mymodule_some_key');

// Або через модель
$settings = $this->model_setting_setting->getSetting('cactus_mymodule');
```

Ключі налаштувань — завжди з префіксом модуля: `cactus_[name]_[key]`.

---

## Меню в адмінці

Додавання пункту меню через `admin/language/[locale]/extension/module/cactus/[name].php`:

```php
<?php
// Якщо модуль потребує власного пункту в меню
// → використовувати OC Events або стандартний механізм модулів
// → не хардкодити в шаблон ядра
```

Для простих модулів доступ через: Extensions → Modules → встановлений модуль.

---

## Permissions (права доступу)

```php
// Перевірка в контролері перед дією
if (!$this->user->hasPermission('modify', 'extension/module/cactus_mymodule')) {
    $this->error['warning'] = $this->language->get('error_permission');
}

// Перевірка на читання
if (!$this->user->hasPermission('access', 'extension/module/cactus_mymodule')) {
    $this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
}
```

---

## user_token

Кожен URL в адмінці повинен містити `user_token`:

```php
// Генерація URL
$this->url->link('extension/module/cactus_mymodule', 'user_token=' . $this->session->data['user_token'], true);

// Передача в Twig
$data['action'] = $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true);
$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
```

---

## Сесії та flash-повідомлення

```php
// Встановити success повідомлення перед redirect
$this->session->data['success'] = $this->language->get('text_success');
$this->response->redirect($this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true));

// У _buildData() прочитати і очистити
$data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
unset($this->session->data['success']);
```

---

## Стандартна структура admin контролера

Детальний приклад — у `controller.md` (розділ "Admin контролер").

Коротко:
- `index()` — головний метод: валідація POST → зберегти → redirect; або buildData → view
- `_validate()` — private метод перевірки
- `_buildData()` — private метод збору даних для Twig
- Breadcrumbs — завжди: Dashboard → назва модуля