# OpenCart — View (Twig)

## ІНСТРУКЦІЯ ДЛЯ AI

**У цьому пакеті view описаний лише як Twig** (файли `.twig`). Як збирається `$data` у контролері й викликається `$this->load->view` — див. [`controller.md`](controller.md).

Тема проєкту визначається при інсталяції. Якщо невідома — запитай:
> Яка тема? (назва папки в `catalog/view/theme/`)

---

## Два різних світи: адмінка і каталог

| | Admin | Catalog |
|---|---|---|
| Шлях | `admin/view/template/` | `catalog/view/theme/[тема]/template/` |
| Движок | **Twig** | **Twig** |
| Стилі | Bootstrap (вбудований OC) | Тема (своя, довільна) |
| Layout | `common/header` + `common/column_left` + `common/footer` | `common/header` + `common/footer` |

---

## Admin View

### Розташування

```
admin/view/template/extension/module/cactus_currency.twig
```

### Базовий шаблон адмін-сторінки

```twig
{{ header }}{{ column_left }}
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-cactus-currency" class="btn btn-primary">
          <i class="fa fa-save"></i> {{ button_save }}
        </button>
        <a href="{{ cancel }}" class="btn btn-default">
          <i class="fa fa-reply"></i> {{ button_cancel }}
        </a>
      </div>
      <h1>{{ heading_title }}</h1>
      <ul class="breadcrumb">
        {% for breadcrumb in breadcrumbs %}
          <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
        {% endfor %}
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    {% if error_warning %}
      <div class="alert alert-danger">{{ error_warning }}</div>
    {% endif %}
    {% if success %}
      <div class="alert alert-success">{{ success }}</div>
    {% endif %}

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title">{{ text_edit }}</h3></div>
      <div class="panel-body">
        <form id="form-cactus-currency" action="{{ action }}" method="post">

          <div class="form-group">
            <label class="col-sm-2 control-label">{{ entry_usd_rate }}</label>
            <div class="col-sm-10">
              <input type="text" name="cactus_currency_usd_rate" value="{{ cactus_currency_usd_rate }}" class="form-control" />
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
{{ footer }}
```

### Правила адмін view

- Стилі — Bootstrap 3 (OC 3.x вбудований), не підключати зовнішні CSS без потреби
- Форми — `id="form-*"`, кнопка save з атрибутом `form="form-*"` (поза формою)
- Іконки — FontAwesome (`fa fa-*`)
- Таблиці — клас `table table-bordered table-hover`
- JS — писати в кінці файлу в тегу `<script>`

---

## Catalog View

### Розташування

```
catalog/view/theme/[ТЕМА]/template/cactus/[назва].twig
```

Якщо тема невідома — **запитай** перед тим як писати шлях.

### Базовий шаблон catalog сторінки

```twig
{{ header }}
<div class="container">
  <ul class="breadcrumb">
    {% for breadcrumb in breadcrumbs %}
      <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
    {% endfor %}
  </ul>

  <div class="row">
    <div id="content" class="col-sm-12">
      <h1>{{ heading_title }}</h1>

      {# основний контент #}

    </div>
  </div>
</div>
{{ footer }}
```

### Кастомний CSS

Питай де зберігати кастомний CSS — кожна тема має своє місце. Не розкидати стилі по різних файлах без потреби.

Типові місця:
- `catalog/view/theme/[тема]/stylesheet/custom.css`
- `catalog/view/theme/[тема]/stylesheet/[назва].css`

Якщо невідомо — запитай користувача.

---

## Twig — базові правила

```twig
{# Виведення змінної (екранується автоматично) #}
{{ variable }}

{# Без екранування (тільки для довіреного HTML) #}
{{ variable | raw }}

{# Умова #}
{% if condition %}...{% endif %}

{# Цикл #}
{% for item in items %}
  {{ item.name }}
{% endfor %}

{# Посилання (href з контролера — див. controller.md) #}
<a href="{{ item.href }}">{{ item.text }}</a>

{# Переклад #}
{{ text_save }}   {# змінна з language файлу, передана з контролера #}
```

### Передача даних з контролера у view

```php
// Контролер
$data['heading_title'] = $this->language->get('heading_title');
$data['products']      = $this->model_cactus_products->getProducts();
$data['action']        = $this->url->link('cactus/products/save', '', true);

$this->response->setOutput($this->load->view('cactus/products', $data));
```

```twig
{# View — змінні доступні напряму #}
<h1>{{ heading_title }}</h1>
{% for product in products %}
  <p>{{ product.name }} — {{ product.price }}</p>
{% endfor %}
```

---

## Логіка у Twig

- **Допустимо:** прості умови й цикли, **фільтри** Twig (`|date`, `|raw` для довіреного HTML тощо), легке форматування для відображення.
- **Краще в контролері:** складні обчислення, багато проміжних змінних, нетривіальні гілки логіки, підготовка даних для кількох частин шаблону. У контролері для цього **допускається приватний метод**, що збирає/нормалізує `$data` для view.
- Якщо та сама підготовка потрібна у **кількох контролерах** у **новому** коді (старий код без задачі не рефакторити) — винось у **helper**; див. згенерований `code-style.md` (політика helper: читати можна, додавати/змінювати — лише з згоди користувача).

---

## Заборонено у Twig

- SQL або звертання до моделі з шаблону.
- Підключення зовнішніх ресурсів без потреби:

```twig
<link rel="stylesheet" href="https://external.com/style.css">
```

---

## OC 2.x і `.tpl`

У **2.x** у проєктах ще бувають шаблони **`.tpl`** (PHP). **Цей документ їх не описує** — орієнтир **Twig**, як у 3.x / 4.x. Легасі `.tpl` **не** переписуємо на Twig без явної задачі користувача.
