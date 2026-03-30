# OpenCart — Model

## Model role

The model is the only place for:
- SQL queries
- Business logic
- Data processing
- `$this->db` usage

The controller never writes SQL. Everything goes through the model.

---

## Model structure

```php
<?php
class ModelCactusCurrencyRecalc extends Model {

    /**
     * Get all products with currency fields
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
     * Update product price in UAH
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

## SQL rules

### Escape — always

```php
// Correct
$name       = $this->db->escape($this->request->post['name']);
$product_id = (int)$product_id;
$price      = (float)$price;

// Forbidden — raw data in query
$this->db->query("SELECT * FROM ... WHERE name = '" . $_POST['name'] . "'");
```

### Table prefix

```php
// Correct
"SELECT * FROM `" . DB_PREFIX . "product`"

// Forbidden
"SELECT * FROM `oc_product`"
```

### Typical queries

```php
// Single row
$query = $this->db->query("SELECT ...");
return $query->row;   // array or empty

// List
$query = $this->db->query("SELECT ...");
return $query->rows;  // array of rows

// Count
$query = $this->db->query("SELECT COUNT(*) AS total FROM ...");
return (int)$query->row['total'];

// INSERT — get id
$this->db->query("INSERT INTO ...");
return $this->db->getLastId();

// Existence check
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

## migration.php — REQUIRED on DB changes

**Any schema change = entry in root `migration.php`.**
Without it the task is not complete.

### Format

```php
<?php
die(0);

// DB schema change log

# 2026-03-23 | oc_product: added cactus_currency_code, cactus_price_foreign
'
ALTER TABLE `oc_product`
  ADD COLUMN `cactus_currency_code` varchar(8) NOT NULL DEFAULT \'\' AFTER `price`,
  ADD COLUMN `cactus_price_foreign` decimal(15,4) NOT NULL DEFAULT \'0.0000\' AFTER `cactus_currency_code`;
';

# 2026-03-20 | cactus_rates: created exchange rate table
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

### migration.php rules

- First line after `<?php` — always `die(0);`
- Second line — empty or `// DB schema change log`
- New entries — **at the top** (newest first)
- SQL inside quoted strings — PHP parses the whole file; syntax errors outside strings can break includes
- AI reads for context, **does not change** without explicit command

---

## Settings via `setting/setting`

For module config (not separate tables):

```php
// In admin module model
$this->load->model('setting/setting');

// Save (associative array under module code)
$this->model_setting_setting->editSetting('cactus_currency', [
    'cactus_currency_usd_rate' => 38.5,
    'cactus_currency_eur_rate' => 42.0,
    'cactus_currency_updated'  => date('Y-m-d H:i:s'),
]);

// Read anywhere
$rate = $this->config->get('cactus_currency_usd_rate');
```

---

## Transactions (when needed)

```php
// OC has no built-in transaction API — raw queries
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

## FORBIDDEN in model

```php
// Raw data without escape
"WHERE name = '" . $name . "'"

// Hardcoded prefix
"FROM `oc_product`"

// Direct $_POST / $_GET
$_POST['name']

// Presentation logic (redirect, response)
$this->response->redirect(...);
```
