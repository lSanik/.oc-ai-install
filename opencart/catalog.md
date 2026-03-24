# OpenCart — Catalog

## Загальне

Каталог (`catalog/`) — публічна частина магазину. Доступна покупцям без авторизації (якщо не закрита).

Кастомний код:

```
catalog/controller/cactus/[name].php
catalog/model/cactus/[name].php
catalog/view/theme/[ТЕМА]/template/cactus/[name].twig
catalog/language/[locale]/cactus/[name].php
```

---

## Маршрутизація

```
index.php?route=cactus/my_page         → catalog/controller/cactus/my_page.php → index()
index.php?route=cactus/my_page/process → catalog/controller/cactus/my_page.php → process()
```

З увімкненими SEO URL: `/my-seo-slug` → `route=cactus/my_page` (налаштовується через `url_alias`).

---

## SEO URL

```php
// Отримати SEO URL для кастомного маршруту
$url = $this->url->link('cactus/my_page', 'param=value', true);
// → OC автоматично підставить SEO alias якщо він є

// Додати SEO alias для маршруту не потрібно!
```

**Не хардкодити URL** — завжди через `$this->url->link()`.

---

## Типові моделі каталогу

```php
// Продукти
$this->load->model('catalog/product');
$product = $this->model_catalog_product->getProduct($product_id);
$products = $this->model_catalog_product->getProducts(['filter_category_id' => $category_id]);

// Категорії
$this->load->model('catalog/category');
$categories = $this->model_catalog_category->getCategories(['parent_id' => 0]);

// Замовлення (якщо є доступ з каталогу)
$this->load->model('account/order');

// Кошик — через бібліотеку
$this->cart->add($product_id, $quantity, $option);
$this->cart->getProducts();
$this->cart->getTotal();
```

---

## Мова в каталозі

```php
// Поточна мова
$language_id = (int)$this->config->get('config_language_id');
$language_code = $this->session->data['language'] ?? $this->config->get('config_language');

// Завантаження мовного файлу
$this->load->language('cactus/my_page');
$data['heading_title'] = $this->language->get('heading_title');
```

---

## Кошик і checkout

```php
// Додати товар
$this->cart->add($product_id, $quantity, $option, $override_price);

// Перевірити кошик
if ($this->cart->hasProducts()) { ... }

// Redirect після дії з кошиком
$this->response->redirect($this->url->link('checkout/cart'));
```

---

## Customer (авторизований покупець)

```php
// Перевірка авторизації
if (!$this->customer->isLogged()) {
    $this->session->data['redirect'] = $this->url->link('cactus/my_page', '', true);
    $this->response->redirect($this->url->link('account/login', '', true));
}

// Дані покупця
$customer_id    = $this->customer->getId();
$customer_email = $this->customer->getEmail();
$customer_name  = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
```

---

## Breadcrumbs у каталозі

```php
$data['breadcrumbs'] = [
    [
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/home'),
    ],
    [
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('cactus/my_page'),
    ],
];
```

---

## Правила

- Не звертатись до `admin/` моделей з `catalog/` контролерів
- SEO-чутливі зміни (структура URL, мета-теги) — попереджати користувача
- `$this->config->get('config_theme')` — для отримання назви теми, не хардкодити