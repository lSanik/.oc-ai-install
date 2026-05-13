# OpenCart — MySQL

## Basics (repeat from model.md — for context)

- All queries — in models only
- `$this->db->escape()` for string input
- `(int)`, `(float)` for numbers
- `DB_PREFIX` instead of hardcoded `oc_`

---

## JOIN patterns

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

// LEFT JOIN (right row may be missing)
$query = $this->db->query("
    SELECT p.product_id, p.price, pd.name
    FROM `" . DB_PREFIX . "product` p
    LEFT JOIN `" . DB_PREFIX . "product_description` pd
        ON (pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
    WHERE p.status = '1'
");
```

---

## Filtering and pagination

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
    // Same WHERE but SELECT COUNT(*)
    $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id > '0'";
    // ... same filters without ORDER/LIMIT
    return (int)$this->db->query($sql)->row['total'];
}
```

---

## Aggregations

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

## Typical OC table structure

```sql
-- Entity + description (multilingual)
CREATE TABLE `oc_{CUSTOM_DIR}_item` (
    `item_id`   int(11) NOT NULL AUTO_INCREMENT,
    `sort_order` int(3) NOT NULL DEFAULT '0',
    `status`    tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `oc_{CUSTOM_DIR}_item_description` (
    `item_id`     int(11) NOT NULL,
    `language_id` int(11) NOT NULL,
    `name`        varchar(255) NOT NULL,
    PRIMARY KEY (`item_id`, `language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Indexes

```sql
-- Add index if column is often used in WHERE or JOIN
ALTER TABLE `oc_{CUSTOM_DIR}_item` ADD INDEX `status` (`status`);
ALTER TABLE `oc_{CUSTOM_DIR}_log` ADD INDEX `date_added` (`date_added`);
```

**Any schema change → record in `migration.php`** (format — `model.md`).

---

## LIKE search

```php
// % both sides — full search
" AND name LIKE '%" . $this->db->escape($search) . "%'"

// Prefix only — faster, can use index
" AND name LIKE '" . $this->db->escape($search) . "%'"
```

---

## Forbidden

```sql
-- Hardcoded prefix
FROM `oc_product`          -- forbidden
FROM `" . DB_PREFIX . "product"  -- correct

-- Raw data without escape
WHERE name = '$name'       -- forbidden
```
