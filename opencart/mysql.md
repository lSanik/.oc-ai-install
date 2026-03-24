# OpenCart — MySQL

## Базові правила (повтор з model.md — для контексту)

- Всі запити — тільки в моделях
- `$this->db->escape()` для всіх рядкових вхідних даних
- `(int)`, `(float)` — для числових
- `DB_PREFIX` замість хардкоду `oc_`

---

## JOIN патерни

```php
// INNER JOIN
$query = $this->db->query("
    SELECT o.order_id, o.total, od.name
    FROM `" . DB_PREFIX . "order` o
    INNER JOIN `" . DB_PREFIX . "order_product` od ON (od.order_id = o.order_id)
    WHERE o.customer_id = '" . (int)$customer_id . "'
      AND o.order_status_id > '0'
    ORDER BY o.date_added DESC
");

// LEFT JOIN (коли правий запис може бути відсутній)
$query = $this->db->query("
    SELECT p.product_id, p.price, pd.name
    FROM `" . DB_PREFIX . "product` p
    LEFT JOIN `" . DB_PREFIX . "product_description` pd
        ON (pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
    WHERE p.status = '1'
");
```

---

## Фільтрація та пагінація

```php
public function getOrders(array $data = []): array {
    $sql = "
        SELECT o.order_id, o.firstname, o.lastname, o.total, o.date_added
        FROM `" . DB_PREFIX . "order` o
        WHERE o.order_status_id > '0'
    ";

    if (!empty($data['filter_customer'])) {
        $sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
    }

    if (!empty($data['filter_date_from'])) {
        $sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_from']) . "')";
    }

    $sort_data = ['o.order_id', 'o.firstname', 'o.total', 'o.date_added'];
    $sort  = (isset($data['sort']) && in_array($data['sort'], $sort_data)) ? $data['sort'] : 'o.date_added';
    $order = (isset($data['order']) && $data['order'] === 'ASC') ? 'ASC' : 'DESC';
    $sql  .= " ORDER BY " . $sort . " " . $order;

    if (isset($data['start'], $data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

public function getTotalOrders(array $data = []): int {
    // Той самий WHERE, але SELECT COUNT(*)
    $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id > '0'";
    // ... ті самі фільтри без ORDER і LIMIT
    return (int)$this->db->query($sql)->row['total'];
}
```

---

## Агрегації

```php
// SUM
$query = $this->db->query("
    SELECT SUM(op.total) AS revenue
    FROM `" . DB_PREFIX . "order_product` op
    INNER JOIN `" . DB_PREFIX . "order` o ON (o.order_id = op.order_id)
    WHERE o.order_status_id > '0'
      AND op.product_id = '" . (int)$product_id . "'
");
$revenue = (float)$query->row['revenue'];

// GROUP BY
$query = $this->db->query("
    SELECT DATE(o.date_added) AS sale_date, COUNT(*) AS cnt, SUM(o.total) AS daily_total
    FROM `" . DB_PREFIX . "order` o
    WHERE o.order_status_id > '0'
    GROUP BY DATE(o.date_added)
    ORDER BY sale_date DESC
    LIMIT 30
");
```

---

## Структура таблиць OC (типова)

```sql
-- Сутність + опис (мультимовність)
CREATE TABLE `oc_cactus_item` (
    `item_id`   int(11) NOT NULL AUTO_INCREMENT,
    `sort_order` int(3) NOT NULL DEFAULT '0',
    `status`    tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oc_cactus_item_description` (
    `item_id`     int(11) NOT NULL,
    `language_id` int(11) NOT NULL,
    `name`        varchar(255) NOT NULL,
    PRIMARY KEY (`item_id`, `language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Індекси

```sql
-- Додавати індекс якщо поле використовується в WHERE або JOIN часто
ALTER TABLE `oc_cactus_item` ADD INDEX `status` (`status`);
ALTER TABLE `oc_cactus_log` ADD INDEX `date_added` (`date_added`);
```

**Будь-яка зміна структури БД → записати в `migration.php`** (формат — в `model.md`).

---

## LIKE пошук

```php
// % з обох сторін — повний пошук
" AND name LIKE '%" . $this->db->escape($search) . "%'"

// Тільки початок — швидший, використовує індекс
" AND name LIKE '" . $this->db->escape($search) . "%'"
```

---

## Заборонено

```sql
-- Хардкод префіксу
FROM `oc_product`          -- заборонено
FROM `" . DB_PREFIX . "product"  -- правильно

-- Сирі дані без escape
WHERE name = '$name'       -- заборонено