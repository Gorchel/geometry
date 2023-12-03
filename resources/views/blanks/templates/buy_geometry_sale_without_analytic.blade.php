<html lang="ru" style="background: #f5f5f5;">
<div class="googoose-wrapper">
    @include('blanks.templates.blocks.header')
    @include('blanks.templates.blocks.styles')

    @if ($desktop == false)
        @include('blanks.templates.blocks.mobile-styles')
    @endif

    @include('blanks.templates.blocks.body-geometry', [
        'renderConfig' => $renderConfig,
        'renderFooterConfig' => $renderFooterConfig,
        'renderImgConfig' => $renderImgConfig,
    ])
</div>
@include('blanks.templates.pdfDoc', [
    'id' => $id,
    'template' => $template,
    'scenario' => $scenario,
])
</html>
