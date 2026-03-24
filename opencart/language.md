# OpenCart — Language

## Структура мовних файлів

Language файли — PHP масиви. Один файл на один контролер/модуль.

```
catalog/language/[locale]/cactus/[назва].php
admin/language/[locale]/extension/module/cactus_[назва].php
```

Локалі визначаються при інсталяції. Типові: `uk-ua`, `ru-ru`, `en-gb`.

---

## Структура файлу

```php
<?php
// Заголовки
$_['heading_title']    = 'Валюти закупівлі';

// Текст
$_['text_edit']        = 'Налаштування';
$_['text_success']     = 'Налаштування збережено';
$_['text_loading']     = 'Завантаження...';

// Поля форми
$_['entry_usd_rate']   = 'Курс USD';
$_['entry_eur_rate']   = 'Курс EUR';
$_['entry_updated']    = 'Останнє оновлення';

// Кнопки
$_['button_save']      = 'Зберегти';
$_['button_cancel']    = 'Скасувати';
$_['button_refresh']   = 'Оновити курси';

// Помилки
$_['error_permission'] = 'Попередження: у вас немає прав для зміни налаштувань!';
$_['error_rate']       = 'Курс має бути більше нуля!';

// Стовпці таблиць
$_['column_currency']  = 'Валюта';
$_['column_rate']      = 'Курс';
$_['column_action']    = 'Дія';
```

---

## Завантаження і використання

### В контролері

```php
// Завантаження
$this->load->language('cactus/currency_recalc');           // catalog
$this->load->language('extension/module/cactus_currency'); // admin

// Передача конкретних рядків у view
$data['heading_title'] = $this->language->get('heading_title');
$data['button_save']   = $this->language->get('button_save');

// Або передати всі одразу (зручно для великих форм)
// — але тоді у view будуть всі ключі з файлу
```

### У view (Twig)

```twig
{{ heading_title }}
{{ entry_usd_rate }}
<button>{{ button_save }}</button>
```

---

## Мультимовність

Якщо проєкт багатомовний — створювати файл для **кожної** локалі.

```
admin/language/uk-ua/extension/module/cactus_currency.php  ← основна
admin/language/ru-ru/extension/module/cactus_currency.php  ← якщо є ru-ru
admin/language/en-gb/extension/module/cactus_currency.php  ← якщо є en-gb
```

Локалі проєкту визначаються при інсталяції. Якщо невідомі — запитай.

---

## Правила

- Всі рядки що виводяться користувачу — тільки через language файли, не хардкодити в контролері чи view
- Ключі в нижньому регістрі з підкресленням: `heading_title`, `error_permission`
- Префікси для структури: `text_`, `entry_`, `button_`, `error_`, `column_`, `tab_`
- Не дублювати рядки — якщо `button_save` є в іншому файлі і значення однакове, все одно дублювати (кожен файл незалежний)
