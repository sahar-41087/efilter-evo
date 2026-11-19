//<?php
/**
 * eLists
 *
 * manage product params lists
 *
 * @author      webber (web-ber12@yandex.ru)
 * @category    module
 * @version     2.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal    @guid eFilters
 * @internal    @properties &param_tv_id=ID TV параметров товара tovarparams;string;&product_templates_id=ID шаблонов товара;string; &param_cat_id=ID категории параметров;string;;;Остаются только разрешенные для данного типа товара параметры, остальные скрываются &param_cat_id_common=ID категории общих параметров;string;;;Не скрываются для любого вида товаров &exclude_tvs_from_list=Не включать ТВ в параметры при выводе;string; &tovarChunkName=Имя чанка вывода товара;string; &pattern_folder=Папка паттернов;string;assets/images/pattern/; &tv_category_tag=ID TV, используемого для связки товар-категории через tagSaver;string;
 * @internal    @modx_category Filters
 * @internal    @installset base, sample
 */

/*
 Модуль управления списками параметров и их значениями.
 После установки включите общие параметры модуля и настройте зависимости
 (плагины/сниппеты), либо используйте плагин filter_install из пакета eFilter-evo3.

 Файл модуля публикуется командой:
   php artisan vendor:publish --tag=efilter-admin
 в assets/modules/eLists/
*/

require_once MODX_BASE_PATH . 'assets/modules/eLists/module.eLists.php';
