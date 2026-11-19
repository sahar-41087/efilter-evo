{{-- eFilter::filter — страница фильтра товаров (контроллер EFilter\Controllers\FilterController) --}}
<div class="efilter-page" id="eFiltr_wrapper">
    {{-- Форма фильтра (HTML генерируется движком из config.default.php) --}}
    <div class="efilter-form-wrapper">
        {!! $form !!}
    </div>

    {{-- Результаты — список товаров, отфильтрованный движком --}}
    <div class="efilter-results-wrapper">
        <div id="eFiltr_results">
            @include('efilter::results')
        </div>
    </div>
</div>
