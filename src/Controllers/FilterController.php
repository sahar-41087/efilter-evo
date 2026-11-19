<?php

namespace Sahar41087\EFilterEvo\Controllers;

use Sahar41087\EFilterEvo\Support\Engine;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FilterController extends Controller
{
    /**
     * @var \DocumentParser
     */
    protected $modx;

    /**
     * @var array Параметры eFilter (по умолчанию).
     * Переопределяются через evo()->getConfig('efilter.*') либо аргументы роута.
     */
    protected $defaultParams = [
        'param_tv_id'          => '',
        'product_templates_id' => '',
        'depth'                => '3',
        'addWhereList'         => '',
        'removeDisabled'       => '0',
        'btnText'              => 'Найти',
        'ajax'                 => '0',
        'endings'              => 'товар,товара,товаров',
        'pattern_folder'       => 'assets/images/pattern/',
    ];

    public function __construct()
    {
        $this->modx = evo();
    }

    /**
     * Регистрация CSS/JS (замена regClientCSS/regClientScript из старого сниппета).
     */
    protected function registerAssets(): void
    {
        $base = $this->modx->getConfig('efilter.assets_path', 'assets/efilter');
        $this->modx->regClientCSS($base . '/css/eFilter.css');
        $this->modx->regClientCSS($base . '/css/slider.css');
        $this->modx->regClientScript($base . '/js/jquery-ui.min.js');
        $this->modx->regClientScript($base . '/js/jquery.ui.touch-punch.min.js');
        // Конфиг фильтрации передаётся через data-атрибуты формы (см. config.default.php)
        // и читается в assets/efilter/js/eFilter.js — никаких inline-скриптов.
        $this->modx->regClientScript($base . '/js/eFilter.js');
    }

    /**
     * Параметры режима работы фильтра для data-атрибутов формы.
     */
    protected function getJsConfig(array $params): array
    {
        return [
            'ajax'       => !empty($params['ajax']) ? '1' : '0',
            'autoSubmit' => isset($params['autoSubmit']) ? (string)$params['autoSubmit'] : '1',
            'ajaxMode'   => isset($params['ajaxMode']) ? (string)$params['ajaxMode'] : '1',
            'changeState'=> isset($params['changeState']) ? (string)$params['changeState'] : '1',
        ];
    }

    /**
     * Собрать параметры из конфига Evo (efilter.*) и переданных аргументов.
     */
    protected function buildParams(array $overrides = []): array
    {
        $params = $this->defaultParams;
        foreach ($params as $key => $value) {
            $fromConfig = $this->modx->getConfig('efilter.' . $key);
            if ($fromConfig !== null && $fromConfig !== '') {
                $params[$key] = $fromConfig;
            }
        }
        return array_merge($params, $overrides);
    }

    /**
     * Вывод формы фильтра + списка товаров для категории $docid.
     *
     * GET /filter/{docid}
     */
    public function show(Request $request, $docid = null)
    {
        $this->registerAssets();

        $docid = (int)($docid ?: ($request->input('docid', $this->modx->documentIdentifier)));

        $params = $this->buildParams([
            'docid' => $docid,
        ]);

        $engine = new Engine($this->modx, $params);

        // Формируем конфиг фильтра из TV категории (или родителей)
        $engine->filter_param = $engine->getFilterParam($engine->param_tv_name);

        if (!empty($engine->filter_param['fieldValue'])) {
            $engine->makeFilterArrays();
        }
        if (!empty($engine->filter_tvs)) {
            $engine->filter_tv_ids = implode(',', $engine->filter_tvs);
        }
        $engine->filter_tv_names = $engine->getTVNames($engine->filter_tv_ids);

        if (!empty($engine->list_tv_ids)) {
            $engine->list_tv_names    = $engine->getTVNames(implode(',', $engine->list_tv_ids));
            $engine->list_tv_captions = $engine->getTVNames(implode(',', $engine->list_tv_ids), 'caption');
            $engine->list_tv_elements = $engine->getTVNames(implode(',', $engine->list_tv_ids), 'elements');
        }

        // Параметры выборки (замена DocLister)
        $dlParams = [
            'parents' => $engine->docid,
            'depth'   => $engine->params['depth'] ?? '3',
            'addWhereList' => (
                (!empty($engine->params['addWhereList']) ? $engine->params['addWhereList'] . ' AND ' : '')
                . 'c.template IN(' . $engine->params['product_templates_id'] . ')'
            ),
        ];

        $allProducts = $engine->getCategoryAllProducts($engine->docid, $engine->tv_category_tag);
        if (empty($allProducts)) {
            // Нет товаров — пустая форма и пустой список
            return $this->render($request, $engine, [], '');
        }

        unset($dlParams['parents'], $dlParams['depth']);
        $dlParams['documents'] = $allProducts;

        $engine->content_ids_full = $allProducts;
        $engine->makeAllContentIDs($dlParams);
        $engine->makeCurrFilterValuesContentIDs($dlParams);

        $engine->filter_values_full = $engine->getFilterValues($engine->content_ids_full, $engine->filter_tv_ids);
        $engine->filter_values      = $engine->getFilterFutureValues($engine->curr_filter_values, $engine->filter_tv_ids);

        $formHtml = $engine->renderFilterBlock(
            $engine->filter_cats,
            $engine->filter_values_full,
            $engine->filter_values,
            $engine->filters,
            $engine->cfg
        );

        $resultData = [
            'ids'       => $engine->content_ids,
            'ids_cnt'   => $engine->content_ids_cnt,
            'tv_list'   => $engine->list_tv_names,
            'tv_names'  => $engine->list_tv_captions,
            'tv_elements' => $engine->list_tv_elements,
        ];

        return $this->render($request, $engine, $resultData, $formHtml);
    }

    /**
     * Отдельный вывод результатов (аналог eFilterResult).
     *
     * GET /filter/{docid}/result
     */
    public function result(Request $request, $docid = null)
    {
        $this->registerAssets();

        $docid = (int)($docid ?: ($request->input('docid', $this->modx->documentIdentifier)));
        $params = $this->buildParams(['docid' => $docid]);
        $engine = new Engine($this->modx, $params);

        $ids = $this->modx->getPlaceholder('eFilter_ids');
        if ($ids === '' || $ids === null) {
            $ids = $request->input('eFilter_ids', '');
        }

        $docs = [];
        $total = 0;
        if (!empty($ids)) {
            $ids = implode(',', array_filter(array_map('intval', explode(',', $ids))));
            if ($ids !== '') {
                $sortBy = $request->input('sortBy', 'menuindex');
                $sortOrder = strtoupper($request->input('sortOrder', 'ASC'));
                if (!in_array($sortOrder, ['ASC', 'DESC'])) {
                    $sortOrder = 'ASC';
                }
                $docs = $engine->getDocsByIds($ids, 'c.*', preg_replace('/[^a-zA-Z0-9_.]/', '', $sortBy) . ' ' . $sortOrder);
                $total = count($docs);
            }
        }

        return view('efilter::results', [
            'modx'   => $this->modx,
            'engine' => $engine,
            'docs'   => $docs,
            'total'  => $total,
            'params' => $params,
        ])->render();
    }

    /**
     * Рендер основной страницы фильтра.
     */
    protected function render(Request $request, Engine $engine, array $resultData, string $formHtml)
    {
        $this->modx->setPlaceholder('eFilter_form', $formHtml);
        $this->modx->setPlaceholder('eFilter_ids', $resultData['ids'] ?? '');
        $this->modx->setPlaceholder('eFilter_ids_cnt', $resultData['ids_cnt'] ?? '');
        $this->modx->setPlaceholder('eFilter_tv_list', $resultData['tv_list'] ?? []);
        $this->modx->setPlaceholder('eFilter_tv_names', $resultData['tv_names'] ?? []);
        $this->modx->setPlaceholder('eFilter_tv_elements', $resultData['tv_elements'] ?? []);

        return view('efilter::filter', [
            'modx'       => $this->modx,
            'engine'     => $engine,
            'form'       => $formHtml,
            'resultData' => $resultData,
            'params'     => $engine->params,
        ])->render();
    }
}
