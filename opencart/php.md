# OpenCart — PHP

## Версія та стандарти

| OC версія | PHP | Стандарт | Type hints |
|-----------|-----|----------|-----------|
| 2.x | 5.6 / 7.x | PSR-2 | Обережно, не скрізь |
| 3.x | 7.x | PSR-2 | Рекомендовано |
| 4.x | 8.x | PSR-12 | Обов'язково |

Визнач версію PHP (`PHP =`) і дотримуйся відповідного стандарту. Якщо невідомо — **запитай**.

---

## Type hints (PHP 7+)

```php
// Параметри і повернення — тільки в новому коді (cactus)
public function getProduct(int $product_id): array {
    // ...
}

public function updatePrice(int $product_id, float $price): void {
    // ...
}

public function findBySlug(string $slug): ?array {
    // ... повертає масив або null
}
```

**Легасі код без задачі — не чіпати, type hints не додавати.**

---

## Null coalescing (PHP 7+)

```php
// Писати:
$val = isset($array['key']) ? $array['key'] : 'default';

// Замість:
$val = $array['key'] ?? 'default';

Ніколи не використовуй ??

// Але в OC 2.x / PHP 5.6 — тільки isset() варіант
```

---

## Масиви

```php
// Короткий синтаксис — завжди (PHP 5.4+)
$data = [];
$ids  = [1, 2, 3];

// Не використовувати array()
$data = array();  // застаріло
```

---

## Рядки

```php
// Конкатенація vs інтерполяція — обидва варіанти ОК
$sql = "SELECT * FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$id . "'";

// Багаторядковий SQL — через heredoc або відступи. Цей варіант бажаний
$sql = "
    SELECT p.product_id, p.price
        FROM `" . DB_PREFIX . "product` p
    WHERE p.status = '1'
";
```

---

## Обробка помилок в OC

OC не використовує exceptions системно в 2.x/3.x. В новому коді (cactus):

```php
// В моделі — кидай exception при критичній помилці
public function processPayment(array $data): array {
    if (empty($data['order_id'])) {
        throw new \InvalidArgumentException('order_id required');
    }
    // ...
}

// В контролері — лови і повертай як error
try {
    $result = $this->model_cactus_payment->processPayment($data);
    $json['success'] = true;
} catch (\Exception $e) {
    $json['error'] = $e->getMessage();
}
```

---

## Константи OC

```php
// Завжди доступні в контексті OC
DIR_APPLICATION   // шлях до catalog/ або admin/
DIR_SYSTEM        // шлях до system/
DIR_IMAGE         // шлях до image/
HTTP_SERVER       // базовий URL
DB_PREFIX         // префікс таблиць (ніколи не хардкодити 'oc_')
VERSION           // версія OC
```

---

## Заборонено

```php
// Прямий доступ до суперглобальних
$_POST['key']   // → $this->request->post['key']
$_GET['key']    // → $this->request->get['key']
$_SESSION       // → $this->session->data

// Прямий echo/print (крім скриптів dev/). Бажано використання echo
echo $variable;


//Дебаг через функції vd(),dd(); Vd Виведе красивий дамп. dd - die dump
vd($qwe,$ewq);
dd($zxc,$gds);//Буде вивід як в vd(), потім die()

// include/require для OC компонентів
include DIR_APPLICATION . 'model/catalog/product.php';
```

---

## Анонімні функції (closures)

**Заборонено.** Не використовувати `function(...) use (...)` в коді.

Якщо без анонімної функції технічно не обійтись — **зупинись і запитай користувача** перед написанням коду.

```php
// ПОГАНО — анонімна функція
$this->session->data[$key] = array_filter(
    $this->session->data[$key],
    function ($t) use ($now, $window) {
        return ($now - $t) < $window;
    }
);

// ДОБРЕ — логіка в окремому методі класу
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
