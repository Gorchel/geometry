@if ($scenario == \App\Http\Controllers\WebhookBlanksController::SCENARIO_DEFAULT)
    <div style="text-align: center; margin-top: 15px;">
        <div style="display: inline-block;">
            <form action="/webhook/blanks-downloads/pdf" method="POST" id="download-pdf-form" target="_blank">
                <input type="hidden" name="id" value="{{$id}}">
                <input type="hidden" name="template" id="download-pdf-template" value="{{$template}}">
                <input type="submit" class="btn btn-warning" value="PDF" style="color: #212529;
        background-color: #ffc107;
        border-color: #ffc107;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        cursor: pointer;
        border-radius: 10px;
        width: 100px;">
            </form>
        </div>
        <div style="display: inline-block;">
            <form action="/webhook/blanks-downloads/doc" method="POST" id="download-doc-form" target="_blank">
                <input type="hidden" name="id" id="download-doc-id" value="{{$id}}">
                <input type="hidden" name="template" id="download-doc-template" value="{{$template}}">
                <input type="button" id="doc-download" class="btn btn-info" value="DOC">
            </form>
        </div>
    </div>
    @include('blanks.templates.doc_scripts')
@endif
