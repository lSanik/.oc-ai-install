# OpenCart — View (Twig)

## INSTRUCTIONS FOR THE AI

**In this pack the view layer is Twig only** (`.twig` files). How `$data` is built in the controller and `$this->load->view` is called — see [`controller.md`](controller.md).

Theme is set at install. If unknown — ask:
> Which theme? (folder name under `catalog/view/theme/`)

---

## Two worlds: admin and catalog

| | Admin | Catalog |
|---|-------|---------|
| Path | `admin/view/template/` | `catalog/view/theme/[theme]/template/` |
| Engine | **Twig** | **Twig** |
| Styles | Bootstrap (built into OC) | Theme (custom) |
| Layout | `common/header` + `common/column_left` + `common/footer` | `common/header` + `common/footer` |

---

## Admin view

### Location

```
admin/view/template/extension/module/cactus_currency.twig
```

### Basic admin page template

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

### Admin view rules

- Styles — Bootstrap 3 (OC 3.x built-in), do not add external CSS without need
- Forms — `id="form-*"`, save button with `form="form-*"` (outside form)
- Icons — FontAwesome (`fa fa-*`)
- Tables — class `table table-bordered table-hover`
- JS — at end of file in `<script>`

---

## Catalog view

### Location

```
catalog/view/theme/[THEME]/template/cactus/[name].twig
```

If theme unknown — **ask** before writing paths.

### Basic catalog page template

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

      {# main content #}

    </div>
  </div>
</div>
{{ footer }}
```

### Custom CSS

Ask where to store custom CSS — each theme differs. Do not scatter styles without reason.

Typical locations:
- `catalog/view/theme/[theme]/stylesheet/custom.css`
- `catalog/view/theme/[theme]/stylesheet/[name].css`

If unknown — ask the user.

---

## Twig — basics

```twig
{# Variable output (auto-escaped) #}
{{ variable }}

{# No escaping (trusted HTML only) #}
{{ variable | raw }}

{# Condition #}
{% if condition %}...{% endif %}

{# Loop #}
{% for item in items %}
  {{ item.name }}
{% endfor %}

{# Link (href from controller — see controller.md) #}
<a href="{{ item.href }}">{{ item.text }}</a>

{# Translation #}
{{ text_save }}   {# from language file, passed from controller #}
```

### Passing data from controller to view

**3.x:** do NOT assign language strings to `$data` — Twig resolves them directly. Pass only computed data:

```php
// Controller (3.x)
$this->load->language('cactus/products');
$data['products'] = $this->model_cactus_products->getProducts();
$data['action']   = $this->url->link('cactus/products/save', '', true);

$this->response->setOutput($this->load->view('cactus/products', $data));
```

**2.x:** language strings must be in `$data`:

```php
// Controller (2.x only)
$data['heading_title'] = $this->language->get('heading_title');
$data['products']      = $this->model_cactus_products->getProducts();
```

```twig
{# View — variables available directly #}
<h1>{{ heading_title }}</h1>
{% for product in products %}
  <p>{{ product.name }} — {{ product.price }}</p>
{% endfor %}
```

---

## Logic in Twig

- **Allowed:** simple conditions and loops, **Twig filters** (`|date`, `|raw` for trusted HTML, etc.), light display formatting.
- **Better in controller:** heavy computation, many intermediate variables, non-trivial branches, data prep for several template parts. A **private method** that normalizes `$data` for the view is OK in the controller.
- If the same prep is needed in **several controllers** in **new** code (do not refactor old code without a task) — use a **helper**; see generated `code-style.md` (helper policy: read OK, add/change only with user consent).

---

## Forbidden in Twig

- SQL or model calls from the template.
- External resources without need:

```twig
<link rel="stylesheet" href="https://external.com/style.css">
```

---

## OC 2.x and `.tpl`

In **2.x** projects may still use **`.tpl`** (PHP). **This doc does not cover them** — focus is **Twig** as in 3.x / 4.x. Legacy `.tpl` **is not** rewritten to Twig without an explicit user task.
