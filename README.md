# eFilter for Evolution CMS 3.x

Composer-пакет фильтра товаров для **Evolution CMS 3.1.x** (PHP 8.1+).
Архитектура: **Laravel-роуты + контроллер + Blade-вью**. Без сниппетов,
без DocLister, без multiTV.

## Установка

```bash
composer require sahar-41087/efilter-evo
```

После установки опубликуйте ассеты (CSS/JS):

```bash
php artisan vendor:publish --tag=efilter-assets
```

Сервис-провайдер автоматически регистрирует:
- Blade-вью с namespace `efilter`
- Frontend-роуты `/filter/{docid}`
- Bind движка `Sahar41087\EFilterEvo\Support\Engine`

## Конфигурация

Задайте параметры фильтра через конфиг Evo (`core/custom/config/cms/settings.php`):

```php
return [
    'efilter.param_tv_id'          => 25,     // ID TV tovarparams
    'efilter.product_templates_id' => '9,10', // ID шаблонов «Товар»
    'efilter.depth'                => '3',
    'efilter.tpl'                  => 'tovarDL', // чанк вывода товара
    'efilter.display'              => 15,
    'efilter.ajax'                 => 0,      // 1 = AJAX-режим
];
```

Либо передавайте их через query-строку роута: `/filter/12?product_templates_id=9&tpl=tovarDL`.

## Использование

| Метод | URL | Что делает |
|-------|-----|------------|
| GET/POST | `/filter/{docid}` | Форма фильтра + список товаров |
| GET | `/filter/{docid}/result` | Только список (для AJAX) |

Пример: открыть `/filter/12` — категория с id=12.

В Blade-шаблоне страницы можно подключить фильтр напрямую:

```blade
@include('efilter::filter')
```

## Как работает JS

Весь JavaScript находится в отдельных файлах `assets/efilter/js/`:

| Файл | Назначение |
|------|-----------|
| `eFilter.js` | Логика фильтра: авто-сабмит, слайдеры, выбор цвета/паттерна, AJAX, сброс |
| `jquery-ui.min.js` | jQuery UI (слайдер диапазона) |
| `jquery.ui.touch-punch.min.js` | Touch-support для слайдера |
| `eFSortBlock.js` | Блок сортировки |
| `ratyupdate.js` | Рейтинги |

Конфиг режима работы (`ajax`, `autoSubmit`, `ajaxMode`, `changeState`) передаётся
через **data-атрибуты формы** (`data-ajax`, `data-auto-submit` и т.д.), которые
инжектит контроллер. Никаких inline-скриптов в шаблонах нет.

## Настройка внешнего вида формы

Шаблоны элементов формы лежат в `src/Support/config/config.default.php`.
Опубликуйте копию для кастомизации:

```bash
php artisan vendor:publish --tag=efilter-config
```

Затем правьте `custom/efilter/config.default.php`.

## Админка (настройка фильтра)

Настройка фильтра категории выполняется через **TV `tovarparams` (Webix)** и
**модуль `eLists`** — они не входят в этот пакет и устанавливаются отдельно
(см. репозиторий-оригинал eFilter). Пакет отвечает только за фронтенд-вывод.

## Зависимости

- Evolution CMS 3.1.x (PHP 8.1+)
- jQuery ≥ 1.9
- Нет зависимостей от DocLister / multiTV / сниппетов

## Лицензия

GPL-3.0-or-later
