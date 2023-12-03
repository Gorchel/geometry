@if (isset($renderImgConfig['map']) && !empty($renderImgConfig['map']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
            <tr>
                <td style="border: 1px solid #000;
                            padding: 4px; text-align: center;" colspan="2">
                    <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['map']['value']}}"/>
                </td>
            </tr>
        </tbody>
    </table>
@endif

@if (isset($renderImgConfig['photo_one']) && !empty($renderImgConfig['photo_one']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
            <tr>
                <td style="border: 1px solid #000;
                            padding: 4px;
                        text-align: center;" colspan="2">
                    <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['photo_one']['value']}}">
                </td>
            </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['photo_two']) && !empty($renderImgConfig['photo_two']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
            <tr>
                <td style="border: 1px solid #000;
                            padding: 4px; text-align: center;" colspan="2">
                    <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['photo_two']['value']}}">
                </td>
            </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['photo_three']) && !empty($renderImgConfig['photo_three']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
        <tr>
            <td style="border: 1px solid #000;
                            padding: 4px; text-align: center;" colspan="2">
                <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['photo_three']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['plan_one']) && !empty($renderImgConfig['plan_one']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
            <tr>
                <td style="border: 1px solid #000;
                            padding: 4px; text-align: center;" colspan="2">
                    <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['plan_one']['value']}}">
                </td>
            </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['plan_two']) && !empty($renderImgConfig['plan_two']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
        <tr>
            <td style="border: 1px solid #000;
                            padding: 4px; text-align: center;" colspan="2">
                <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['plan_two']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
@if (isset($renderImgConfig['plan_three']) && !empty($renderImgConfig['plan_three']))
    <table style="border-collapse: collapse; margin-top: 15px; width: 100%;">
        <tbody>
        <tr>
            <td style="border: 1px solid #000;
                            padding: 4px; text-align: center;" colspan="2">
                <img style="max-width: 100%; max-height: 600px;" src="{{$renderImgConfig['plan_three']['value']}}">
            </td>
        </tr>
        </tbody>
    </table>
@endif
