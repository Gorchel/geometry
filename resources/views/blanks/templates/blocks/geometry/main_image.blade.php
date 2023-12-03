@if (isset($renderImgConfig['photo_one']) && !empty($renderImgConfig['photo_one']))
    <div class="location">
        <img style="width:100%; margin-bottom:15px;" src="{{$renderImgConfig['photo_one']['value']}}"/>
    </div>
@endif
