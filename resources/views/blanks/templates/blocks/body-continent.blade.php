<body style='font-size: 16px;
box-sizing: border-box;
position: relative;'>
<div>
    <div class="container">

        @if (isset($templateParams) && isset($templateParams['sale']))
            <div style="text-align: center;font-size: 22px;
            color: #ff0000;
            padding-bottom: 14px;"><strong>Снижение цены!</strong></div>
        @endif

        <table class="table" style=" width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            table-layout: fixed;">
            <tbody>
                <tr style="flex-grow: 1;
            object-fit: cover;
            width: 100%;

            object-position: 0;">
                    <td class="col-td" style="border: 1px solid #000;
            padding: 4px;
            vertical-align: top;">
                        @include('blanks.templates.blocks.continent.logo')
                        @include('blanks.templates.blocks.continent.table_list',[
                            'renderConfig' => $renderConfig['first'],
                        ])
                    </td>
                </tr>
                <tr style="flex-grow: 1;
            object-fit: cover;
            width: 100%;
            object-position: 0;">
                    <td class="col-td padding-0" style="border: 1px solid #000;
            padding: 4px;
            vertical-align: top;">
                        @include('blanks.templates.blocks.continent.map',[
                            'renderConfig' => $renderConfig['second'],
                            'renderImgConfig' => $renderImgConfig,
                        ])
                    </td>
                </tr>
                <tr style="flex-grow: 1;
            object-fit: cover;
            width: 100%;
            object-position: 0;">
                    <td class="col-td" style="border: 1px solid #000;
            padding: 4px;
            vertical-align: top;">
                        @include('blanks.templates.blocks.continent.table_list',[
                            'renderConfig' => $renderConfig['thirty'],
                        ])
                    </td>
                </tr>
            </tbody>
        </table>
        @include('blanks.templates.blocks.continent.images-block',[
            'renderImgConfig' => $renderImgConfig,
        ])
        <table class="table" style=" width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        table-layout: fixed;">
            <tbody>
                <tr style="flex-grow: 1;
            object-fit: cover;
            width: 100%;
            object-position: 0;">
                    <td class="col-td" style="border: 1px solid #000;
            padding: 4px;
            vertical-align: top;">
                        @include('blanks.templates.blocks.continent.table_list',[
                            'renderConfig' => $renderFooterConfig,
                        ])
                    </td>
                </tr>
{{--                <tr style="flex-grow: 1;--}}
{{--            object-fit: cover;--}}
{{--            width: 100%;--}}
{{--            object-position: 0;">--}}
{{--                    <td class="col-td" style="border: 1px solid #000;--}}
{{--            padding: 4px;--}}
{{--            vertical-align: top;">--}}
{{--                        <div class="col-td__text-plan" style="padding-top: 16px;">Планировку предоставим по запросу</div>--}}
{{--                    </td>--}}
{{--                </tr>--}}
            </tbody>
        </table>
    </div>
</div>
@include('blanks.templates.blocks.scripts')
</body>
