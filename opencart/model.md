# OpenCart — Model

## Роль моделі

Модель — єдине місце де живе:
- SQL запити
- Бізнес-логіка
- Обробка даних
- Робота з `$this->db`

Контролер ніколи не пише SQL. Все через модель.

---

## Структура моделі

```php
<?php
class ModelCactusCurrencyRecalc extends Model {

    /**
     * Отримати всі товари з валютою
     */
    public function getProductsWithCurrency(): array {
        $query = $this->db->query("
            SELECT p.product_id, p.price, p.cactus_currency_code, p.cactus_price_foreign
            FROM `" . DB_PREFIX . "product` p
            WHERE p.cactus_currency_code != ''
              AND p.cactus_currency_code IS NOT NULL
        ");

        return $query->rows;
    }

    /**
     * Оновити ціну товару в UAH
     */
    public function updateProductPrice(int $product_id, float $price_uah): void {
        $this->db->query("
            UPDATE `" . DB_PREFIX . "product`
            SET price = '" . (float)$price_uah . "'
            WHERE product_id = '" . (int)$product_id . "'
        ");
    }
}
```

---

## Правила SQL

### Escape — завжди і для всього

```php
// Правильно
$name       = $this->db->escape($this->request->post['name']);
$product_id = (int)$product_id;
$price      = (float)$price;

// Заборонено — сирі дані в запиті
$this->db->query("SELECT * FROM ... WHERE name = '" . $_POST['name'] . "'");
```

### Префікс таблиць

```php
// Правильно
"SELECT * FROM `" . DB_PREFIX . "product`"

// Заборонено
"SELECT * FROM `oc_product`"
```

### Типові запити

```php
// Один запис
$query = $this->db->query("SELECT ...");
return $query->row;   // масив або порожній масив

// Список
$query = $this->db->query("SELECT ...");
return $query->rows;  // масив масивів

// Кількість
$query = $this->db->query("SELECT COUNT(*) AS total FROM ...");
return (int)$query->row['total'];

// INSERT — отримати id
$this->db->query("INSERT INTO ...");
return $this->db->getLastId();

// Перевірка існування
$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
return (bool)$query->row['total'];
```

### Pagination

```php
public function getProducts(array $data = []): array {
    $sql = "SELECT * FROM `" . DB_PREFIX . "product` WHERE status = '1'";

    if (!empty($data['filter_name'])) {
        $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
    }

    $sort_data = ['p.name', 'p.price', 'p.date_added'];
    $sort  = (isset($data['sort']) && in_array($data['sort'], $sort_data)) ? $data['sort'] : 'p.name';
    $order = (isset($data['order']) && $data['order'] === 'DESC') ? 'DESC' : 'ASC';
    $sql .= " ORDER BY " . $sort . " " . $order;

    if (isset($data['start']) || isset($data['limit'])) {
        $start = max(0, (int)(isset($data['start']) ? $data['start'] : 0));
        $limit = max(1, (int)(isset($data['limit']) ? $data['limit'] : 20));
        $sql .= " LIMIT " . $start . "," . $limit;
    }

    return $this->db->query($sql)->rows;
}
```

---

## migration.php — ОБОВ'ЯЗКОВО при зміні БД

**Будь-яка зміна структури БД = запис в `migration.php` у корені проєкту.**
Без цього запису задача не вважається завершеною.

### Формат

```php
<?php
die(0);

// DB schema change log

# 2026-03-23 | oc_product: додано cactus_currency_code, cactus_price_foreign
'
ALTER TABLE `oc_product`
  ADD COLUMN `cactus_currency_code` varchar(8) NOT NULL DEFAULT \'\' AFTER `price`,
  ADD COLUMN `cactus_price_foreign` decimal(15,4) NOT NULL DEFAULT \'0.0000\' AFTER `cactus_currency_code`;
';

# 2026-03-20 | cactus_rates: створено таблицю курсів валют
'
CREATE TABLE `oc_cactus_rates` (
  `rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(8) NOT NULL,
  `rate` decimal(15,6) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
';
```

### Правила migration.php

- Перший рядок після `<?php` — завжди `die(0);`
- Другий рядок — порожній або `// DB schema change log`
- Новий запис — **зверху** (новіші вище)
- SQL у рядках в лапках — PHP парсить файл цілком, синтаксичні помилки поза рядками можуть зламати підключення
- AI читає для контексту, **не змінює** без явної команди

---

## Налаштування через `setting/setting`

Для збереження конфігів модуля (не окремі таблиці):

```php
// В моделі адмін-модуля
$this->load->model('setting/setting');

// Зберегти (асоціативний масив під кодом модуля)
$this->model_setting_setting->editSetting('cactus_currency', [
    'cactus_currency_usd_rate' => 38.5,
    'cactus_currency_eur_rate' => 42.0,
    'cactus_currency_updated'  => date('Y-m-d H:i:s'),
]);

// Читати в будь-якому місці
$rate = $this->config->get('cactus_currency_usd_rate');
```

---

## Транзакції (якщо потрібні)

```php
// OC не має вбудованого API для транзакцій — через прямий запит
$this->db->query("START TRANSACTION");
try {
    $this->db->query("UPDATE ...");
    $this->db->query("INSERT ...");
    $this->db->query("COMMIT");
} catch (\Exception $e) {
    $this->db->query("ROLLBACK");
    throw $e;
}
```

---

## ЗАБОРОНЕНО в моделі

```php
// Сирі дані без escape
"WHERE name = '" . $name . "'"

// Хардкод префіксу
"FROM `oc_product`"

// Звертання до $_POST, $_GET напряму
$_POST['name']

// Логіка відображення (redirect, response)
$this->response->redirect(...);
```
