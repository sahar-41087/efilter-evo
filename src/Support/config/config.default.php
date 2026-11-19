<?php
if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

// ============================================================
// Шаблоны вывода элементов формы фильтра eFilter
// Весь JS вынесен в assets/efilter/js/eFilter.js
// Элементы используют data-* атрибуты и CSS-классы
// ============================================================

// --- Чекбокс ---
$tplRowCheckbox = '<label class="eFiltr_checkbox"><input type="checkbox" name="f[[+tv_id+]][]" value="[+value+]" [+selected+] [+disabled+] /> [+name+] <span class="eFiltr_count">[+count+]</span></label>';

// --- Список (select) ---
$tplRowSelect = '<option value="[+value+]" [+selected+] [+disabled+]>[+name+] ([+count+])</option>';

// --- Диапазон (числа) ---
$tplRowInterval = '<div class="eFiltr_interval">
    <input type="range" class="eFiltr_min" min="[+minvalcurr+]" max="[+maxvalcurr+]" value="[+minval+]" step="1" />
    <input type="number" name="f[[+tv_id+]][min]" value="[+minval+]" min="[+minvalcurr+]" max="[+maxvalcurr+]" step="1" />
    <span class="eFiltr_sep">–</span>
    <input type="range" class="eFiltr_max" min="[+minvalcurr+]" max="[+maxvalcurr+]" value="[+maxval+]" step="1" />
    <input type="number" name="f[[+tv_id+]][max]" value="[+maxval+]" min="[+minvalcurr+]" max="[+maxvalcurr+]" step="1" />
</div>';

// --- Радио ---
$tplRowRadio = '<label class="eFiltr_radio"><input type="radio" name="f[[+tv_id+]]" value="[+value+]" [+selected+] [+disabled+] /> [+name+] <span class="eFiltr_count">[+count+]</span></label>';

// --- Мультиселект ---
$tplRowMultySelect = '<option value="[+value+]" [+selected+] [+disabled+]>[+name+] ([+count+])</option>';

// --- Слайдер (jQuery UI) ---
$tplRowSlider = '<div class="eFiltr_slider" data-min="[+minvalcurr+]" data-max="[+maxvalcurr+]" data-minval="[+minval+]" data-maxval="[+maxval+]">
    <input type="hidden" name="f[[+tv_id+]][min]" value="[+minval+]" />
    <input type="hidden" name="f[[+tv_id+]][max]" value="[+maxval+]" />
</div>';

// --- Цвет ---
$tplRowColors = '<div class="eFiltr_color_swatch [+label_selected+]" data-tv-id="[+tv_id+]" data-value="[+value+]" title="[+name+]">[+name+]</div>';

// --- Паттерн ---
$tplRowPattern = '<div class="eFiltr_pattern_swatch [+label_selected+]" data-tv-id="[+tv_id+]" data-value="[+value+]" title="[+name+]" style="background-image:url([+pattern_folder+][+value+].png)">[+name+]</div>';

// --- Одиночный чекбокс ---
$tplRowSingleCheckbox = '<label class="eFiltr_single_checkbox"><input type="checkbox" name="f[[+tv_id+]]" value="1" [+selected+] [+disabled+] /> [+block_name+]</label>';

// --- Обёртки типов ---
$tplOuterCheckbox = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span><div class="eFiltr_wrapper">[+wrapper+]</div></div>';

$tplOuterSelect = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span><select name="f[[+tv_id+]][]" multiple="multiple" size="5">[+wrapper+]</select></div>';

$tplOuterInterval = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span>[+wrapper+]</div>';

$tplOuterRadio = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span><div class="eFiltr_wrapper">[+wrapper+]</div></div>';

$tplOuterMultySelect = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span><select name="f[[+tv_id+]][]" multiple="multiple" size="5">[+wrapper+]</select></div>';

$tplOuterSlider = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span>[+wrapper+]</div>';

$tplOuterColors = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span><div class="eFiltr_colors">[+wrapper+]</div></div>';

$tplOuterPattern = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span><div class="eFiltr_patterns">[+wrapper+]</div></div>';

$tplOuterSingleCheckbox = '<div class="eFiltr_block eFiltr_block[+active_block_class+]" data-tv-id="[+tv_id+]"><span class="eFiltr_title">[+name+]</span>[+wrapper+]</div>';

// --- Категория ---
$tplOuterCategory = '<div class="eFiltr_category" data-cat-name="[+cat_name+]"><h3>[+cat_name+]</h3>[+wrapper+]</div>';

// --- Форма целиком ---
$tplFilterForm = '<form action="[+url+]" method="[+form_method+]" id="eFiltr_form">[+wrapper+]<button type="submit" class="eFiltr_submit">[+btn_text+]</button><div class="eFiltr_found">[+form_result_cnt+]</div></form>';

// --- Кнопка сброса ---
$tplFilterReset = '<a href="[+reset_url+]" class="eFiltr_reset">Сбросить фильтры</a>';
