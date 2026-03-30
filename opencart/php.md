# OpenCart — PHP

## Version and standards

| OC version | PHP | Standard | Type hints |
|------------|-----|----------|------------|
| 2.x | 5.6 / 7.x | PSR-2 | Use carefully |
| 3.x | 7.x | PSR-2 | Recommended |
| 4.x | 8.x | PSR-12 | Required |

Detect PHP version (`PHP =`) and follow the matching standard. If unknown — **ask**.

---

## Type hints (PHP 7+)

```php
// Parameters and return types — new code (cactus) only
public function getProduct(int $product_id): array {
    // ...
}

public function updatePrice(int $product_id, float $price): void {
    // ...
}

public function findBySlug(string $slug): ?array {
    // ... returns array or null
}
```

**Legacy code without a task — leave alone; do not add type hints.**

---

## Null coalescing (PHP 7+)

```php
// Write:
$val = isset($array['key']) ? $array['key'] : 'default';

// Instead of:
$val = $array['key'] ?? 'default';

Never use ??

// In OC 2.x / PHP 5.6 — isset() only
```

---

## Arrays

```php
// Short syntax — always (PHP 5.4+)
$data = [];
$ids  = [1, 2, 3];

// Do not use array()
$data = array();  // outdated
```

---

## Strings

```php
// Concatenation vs interpolation — both OK
$sql = "SELECT * FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$id . "'";

// Multi-line SQL — heredoc or indentation. This style is preferred
$sql = "
    SELECT p.product_id, p.price
        FROM `" . DB_PREFIX . "product` p
    WHERE p.status = '1'
";
```

---

## Error handling in OC

OC does not use exceptions systematically in 2.x/3.x. In new code (cactus):

```php
// In model — throw on critical failure
public function processPayment(array $data): array {
    if (empty($data['order_id'])) {
        throw new \InvalidArgumentException('order_id required');
    }
    // ...
}

// In controller — catch and return as error
try {
    $result = $this->model_cactus_payment->processPayment($data);
    $json['success'] = true;
} catch (\Exception $e) {
    $json['error'] = $e->getMessage();
}
```

---

## OC constants

```php
// Always available in OC context
DIR_APPLICATION   // path to catalog/ or admin/
DIR_SYSTEM        // path to system/
DIR_IMAGE         // path to image/
HTTP_SERVER       // base URL
DB_PREFIX         // table prefix (never hardcode 'oc_')
VERSION           // OC version
```

---

## Forbidden

```php
// Direct superglobals
$_POST['key']   // → $this->request->post['key']
$_GET['key']    // → $this->request->get['key']
$_SESSION       // → $this->session->data

// Raw echo/print (except dev scripts). Prefer controlled output
echo $variable;


// Debug via vd(), dd(); vd() — pretty dump; dd — dump then die
vd($qwe,$ewq);
dd($zxc,$gds);// same as vd() then die()

// include/require for OC components
include DIR_APPLICATION . 'model/catalog/product.php';
```

---

## Anonymous functions (closures)

**Forbidden.** Do not use `function(...) use (...)` in code.

If you truly cannot avoid it — **stop and ask the user** before writing.

```php
// BAD — closure
$this->session->data[$key] = array_filter(
    $this->session->data[$key],
    function ($t) use ($now, $window) {
        return ($now - $t) < $window;
    }
);

// GOOD — logic in a class method
$this->session->data[$key] = $this->filterByWindow(
    $this->session->data[$key], $now, $window
);

// ...

private function filterByWindow(array $timestamps, int $now, int $window): array {
    $result = [];
    foreach ($timestamps as $t) {
        if (($now - $t) < $window) {
            $result[] = $t;
        }
    }
    return $result;
}
```
