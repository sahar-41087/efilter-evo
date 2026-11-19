<?php

/**
 * Роуты пакета eFilter (Evolution CMS 3.x).
 *
 * Подключается автоматически из core/custom/routes.php (RoutingServiceProvider,
 * только для frontend и CLI). Фильтр вызывается через контроллер, без сниппетов.
 *
 * Примеры:
 *   /filter/12         — форма фильтра + список товаров категории 12
 *   /filter/12/result  — только список товаров (AJAX/отдельная выдача)
 */

use EFilter\Controllers\FilterController;
use Illuminate\Support\Facades\Route;

Route::get('/filter/{docid?}', [FilterController::class, 'show'])->name('efilter.show');
Route::get('/filter/{docid?}/result', [FilterController::class, 'result'])->name('efilter.result');

// POST-обработчик фильтра (сабмит формы) отдаёт те же результаты
Route::post('/filter/{docid?}', [FilterController::class, 'show'])->name('efilter.show.post');
