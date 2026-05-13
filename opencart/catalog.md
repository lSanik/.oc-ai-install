# OpenCart — Catalog

## General

Catalog (`catalog/`) is the public storefront. Available to shoppers without login (unless restricted).

Custom code:

```
catalog/controller/cactus/[name].php
catalog/model/cactus/[name].php
catalog/view/theme/[THEME]/template/cactus/[name].twig
catalog/language/[locale]/cactus/[name].php
```

---

## Routing

```
index.php?route=cactus/my_page         → catalog/controller/cactus/my_page.php → index()
index.php?route=cactus/my_page/process → catalog/controller/cactus/my_page.php → process()
```

With SEO URLs enabled: `/my-seo-slug` → `route=cactus/my_page` (via `url_alias`).

---

## SEO URL

```php
// Get SEO URL for custom route
$url = $this->url->link('cactus/my_page', 'param=value', true);
// → OC substitutes SEO alias when present

// You do not need to add SEO alias for the route manually in code!
```

**Do not hardcode URLs** — always `$this->url->link()`.

---

## Typical catalog models

```php
// Products
$this->load->model('catalog/product');
$product = $this->model_catalog_product->getProduct($product_id);
$products = $this->model_catalog_product->getProducts(['filter_category_id' => $category_id]);

// Categories
$this->load->model('catalog/category');
$categories = $this->model_catalog_category->getCategories(['parent_id' => 0]);

// Orders (if accessible from catalog)
$this->load->model('account/order');

// Cart — via library
$this->cart->add($product_id, $quantity, $option);
$this->cart->getProducts();
$this->cart->getTotal();
```

---

## Language in catalog

```php
// Current language
$language_id   = (int)$this->config->get('config_language_id');
$language_code = isset($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');

// Load language file
$this->load->language('cactus/my_page');
// 3.x: stop here — Twig resolves language strings directly, no $data assignments needed
// 2.x only: $data['heading_title'] = $this->language->get('heading_title');
```

---

## Cart and checkout

```php
// Add product
$this->cart->add($product_id, $quantity, $option, $override_price);

// Check cart
if ($this->cart->hasProducts()) { ... }

// Redirect after cart action
$this->response->redirect($this->url->link('checkout/cart'));
```

---

## Customer (logged-in shopper)

```php
// Auth check
if (!$this->customer->isLogged()) {
    $this->session->data['redirect'] = $this->url->link('cactus/my_page', '', true);
    $this->response->redirect($this->url->link('account/login', '', true));
}

// Customer data
$customer_id    = $this->customer->getId();
$customer_email = $this->customer->getEmail();
$customer_name  = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
```

---

## Breadcrumbs in catalog

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

## Rules

- Do not call `admin/` models from `catalog/` controllers
- Warn the user on SEO-sensitive changes (URL structure, meta tags)
- `$this->config->get('config_theme')` for theme name — do not hardcode
