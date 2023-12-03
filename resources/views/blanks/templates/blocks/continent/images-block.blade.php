<div class="col-td__title text-center offer_text-red pb-24" style="text-align: center; padding-bottom: 24px; color: #ff0000; text-transform: uppercase"><strong>Фото объекта</strong></div>
@if (isset($renderImgConfig['photo_one']) && !empty($renderImgConfig['photo_one']))
    <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-bottom: 0px;
            table-layout: fixed;">
        <tbody>
            <tr style="flex-grow: 1;
                    object-fit: cover;
                    width: 100%;
                    object-position: 0;">
                <td class="col-td" style="border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top; text-align: center;">
                    <img style="max-width: 100%; max-height: 600px;" class="image image_object-photo mb-4" src="{{$renderImgConfig['photo_one']['value']}}">
                </td>
            </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['photo_two']) && !empty($renderImgConfig['photo_two']))
    <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            table-layout: fixed;
            margin-bottom: 0px;">
        <tbody>
        <tr style="flex-grow: 1;
                    object-fit: cover;
                    width: 100%;
                    object-position: 0;">
            <td class="col-td" style="border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top; text-align: center;">
                <img style="max-width: 100%; max-height: 600px; margin-bottom: 4px;" class="image image_object-photo mb-4" src="{{$renderImgConfig['photo_two']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['photo_three']) && !empty($renderImgConfig['photo_three']))
    <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            table-layout: fixed;
            margin-bottom: 0px;">
        <tbody>
        <tr style="flex-grow: 1;
                    object-fit: cover;
                    width: 100%;
                    object-position: 0;">
            <td class="col-td" style="border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top; text-align: center;">
                <img style="max-width: 100%; max-height: 600px; margin-bottom: 4px;" class="image image_object-photo mb-4" src="{{$renderImgConfig['photo_three']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['plan_one']) && !empty($renderImgConfig['plan_one']))
    <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-bottom: 0px;
            table-layout: fixed;">
        <tbody>
        <tr style="flex-grow: 1;
                    object-fit: cover;
                    width: 100%;
                    object-position: 0;">
            <td class="col-td" style="border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top; text-align: center;">
                    <img style="max-width: 100%; max-height: 600px;" class="image image_object-photo mb-4" src="{{$renderImgConfig['plan_one']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['plan_two']) && !empty($renderImgConfig['plan_two']))
    <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-bottom: 0px;
            table-layout: fixed;">
        <tbody>
        <tr style="flex-grow: 1;
                    object-fit: cover;
                    width: 100%;
                    object-position: 0;">
            <td class="col-td" style="border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top; text-align: center;">
                    <img style="max-width: 100%; max-height: 600px;" class="image image_object-photo mb-4" src="{{$renderImgConfig['plan_two']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['plan_three']) && !empty($renderImgConfig['plan_three']))
    <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-bottom: 0px;
            table-layout: fixed;">
        <tbody>
        <tr style="flex-grow: 1;
                    object-fit: cover;
                    width: 100%;
                    object-position: 0;">
            <td class="col-td" style="border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top; text-align: center;">
                   <img style="max-width: 100%; max-height: 600px;" class="image image_object-photo mb-4" src="{{$renderImgConfig['plan_three']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif






