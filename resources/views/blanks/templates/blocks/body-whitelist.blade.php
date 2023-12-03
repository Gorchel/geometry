<body style='box-sizing: border-box;
position: relative;'>
    <div class="container">
        @include('blanks.templates.blocks.whitelist.table_list',[
            'renderConfig' => $renderConfig,
        ])
        @include('blanks.templates.blocks.whitelist.images', [
            'renderImgConfig' => $renderImgConfig
        ])
        @include('blanks.templates.blocks.scripts')
    </div>
</body>
