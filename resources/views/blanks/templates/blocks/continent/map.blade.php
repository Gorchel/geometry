<div class="col-td__title text-center offer_text-red pb-24" style="text-align: center; padding-bottom: 24px; color: #ff0000; text-transform: uppercase;">
    <strong>Карта месторасположения</strong>
</div>
@if (isset($renderImgConfig['map']) && !empty($renderImgConfig['map']))
    <img style="width:100%; margin-bottom:15px;" src="{{$renderImgConfig['map']['value']}}"/>
@endif
<div class="group pt-4">
    @include('blanks.templates.blocks.continent.table_list',[
        'renderConfig' => $renderConfig,
    ])
</div>
