{{-- eFilter::results — список отфильтрованных товаров --}}
@php
    $ids = $ids ?? ($resultData['ids'] ?? $modx->getPlaceholder('eFilter_ids') ?? '');
    $tvList  = $tvList  ?? ($resultData['tv_list']  ?? $modx->getPlaceholder('eFilter_tv_list') ?: []);
    $tvNames = $tvNames ?? ($resultData['tv_names'] ?? $modx->getPlaceholder('eFilter_tv_names') ?: []);
    $tpl = $tpl ?? ($params['tpl'] ?? $modx->getConfig('efilter.tpl') ?: '');

    $docs = $docs ?? [];
    if (empty($docs) && !empty($ids)) {
        $idsClean = implode(',', array_filter(array_map('intval', explode(',', $ids))));
        if ($idsClean !== '') {
            $docs = $engine->getDocsByIds($idsClean, 'c.*', 'menuindex ASC');
        }
    }
    $total = $total ?? count($docs);
@endphp

<div class="efilter-results">
    <div class="efilter-count">Найдено: {{ $total }}</div>

    @if(empty($docs))
        <div class="efilter-empty">Товары не найдены.</div>
    @else
        <div class="efilter-items">
            @foreach($docs as $doc)
                @php
                    $docId = (int)$doc['id'];
                    $tvValues = [];
                    if (!empty($tvList) && is_array($tvList)) {
                        $tvValues = $modx->getTemplateVarOutput(array_values($tvList), $docId, 1);
                    }
                @endphp
                <div class="efilter-item" data-id="{{ $docId }}">
                    @if(!empty($tpl))
                        @php
                            $tplBody = $modx->getTpl($tpl);
                            if ($tplBody === false || $tplBody === null) {
                                $tplBody = (string)$tpl;
                            }
                            $ph = $doc;
                            foreach ((array)$tvValues as $tvName => $tvValue) {
                                $ph['tv.' . $tvName] = $tvValue;
                            }
                        @endphp
                        {!! $modx->parseText($tplBody, $ph, '[+', '+]') !!}
                    @else
                        <h3><a href="@makeUrl($docId)">{{ $doc['pagetitle'] }}</a></h3>
                        @if(!empty($doc['introtext']))<p>{{ $doc['introtext'] }}</p>@endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
