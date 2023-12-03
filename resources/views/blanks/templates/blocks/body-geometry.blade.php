<body>
    <div class="container">
        @include('blanks.templates.blocks.geometry.logo')
        <div style="text-align: center;font-size: 22px;
            color: #162d64;
            padding-bottom: 14px;"><strong>Коммерческое предложение</strong></div>

        @if (isset($templateParams) && isset($templateParams['sale']))
            <div style="text-align: center;font-size: 22px;
            color: #ff0000;
            padding-bottom: 14px;"><strong>Снижение цены!</strong></div>
        @endif

        @include('blanks.templates.blocks.geometry.main_image', [
            'renderImgConfig' => $renderImgConfig
        ])
        @include('blanks.templates.blocks.geometry.info_table', [
            'renderConfig' => $renderConfig
        ])

        @include('blanks.templates.blocks.geometry.images', [
            'renderImgConfig' => $renderImgConfig
        ])

        @include('blanks.templates.blocks.geometry.footer', [
            'renderFooterConfig' => $renderFooterConfig
        ])
    </div>
    @include('blanks.templates.blocks.scripts')
</body>
