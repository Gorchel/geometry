<html lang="ru">
    <div class="googoose-wrapper">
        @include('blanks.templates.blocks.header')
        @include('blanks.templates.blocks.styles')

        @if ($desktop == false)
            @include('blanks.templates.blocks.mobile-styles')
        @endif

        @include('blanks.templates.blocks.body-whitelist', [
            'renderConfig' => $renderConfig,
            'renderImgConfig' => $renderImgConfig,
        ])  
    </div>
    @include('blanks.templates.pdfDoc', [
        'id' => $id,
        'template' => $template,
        'scenario' => $scenario,
    ])
</html>
