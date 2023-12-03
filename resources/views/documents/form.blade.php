<?php
    use \App\Classes\Documents\Sender;
?>
@extends('layouts')

@section("content")
    <form method="POST" action="/weebhook_documents_send" id="submitForm">
        <div class="row" style="margin-bottom: 40px;"></div>
        <input type="hidden" name="token" value="{{$token}}">
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="type" value="{{$objectType}}">
        <input type="hidden" name="sendType" value="{{$sendType}}">
        <div class="row">
            @if ($sendType !== Sender::OS_TYPE)
                <div class="col-lg-10 offset-lg-1 form-group" style="padding-left: 40px;">
                    <?php
                        $isChecked = false;

                        if ($request->has('company_email')) {
                            if (!empty($request->get('company_email'))) {
                                $isChecked = true;
                            } else {
                                $isChecked = false;
                            }
                        } elseif (in_array($sendType, [Sender::KP_TYPE, Sender::CUSTOM_OS_TYPE])) {
                            $isChecked = true;
                        }
                    ?>

                    <input type="checkbox" class="custom-control-input" id="company_email" name="company_email" {{!empty($isChecked) ? 'checked="checked"' : ''}} value="1">
                    <label class="custom-control-label" for="company_email">Email компании</label>
                </div>
            @endif
            <div class="col-lg-10 offset-lg-1 form-group" style="padding-left: 40px;">
                <input type="checkbox" class="custom-control-input" id="contact_email" name="contact_email" {{!$request->has('contact_email') || !empty($request->get('contact_email')) ? 'checked="checked"' : ''}} value="1">
                <label class="custom-control-label" for="contact_email">Email контактов</label>
            </div>
            <div class="col-lg-10 offset-lg-1 form-group">
                <input type="hidden" name="email_json" value="{{json_encode($emails)}}">
                <select name="emails[]" id="emails" class="form-control" multiple="multiple"  required="required">
                    @foreach ($emails as $email => $info)
                        <option value="{{$email}}" selected="selected">{{$info['description']}}</option>
                    @endforeach
                </select>
            </div>
            @if (!empty($exceptEmails))
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="exceptEmails">Исключенные адреса</label>
                    <select name="exceptEmails[]" id="exceptEmails" class="form-control" multiple="multiple" readonly="readonly">
                        @foreach ($exceptEmails as $email)
                            <option value="{{$email}}" selected="selected">{{$email}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <label for="">Объект недвижимости</label>
                <input class="input-sm form-control" name="objectId" type="hidden" value="{{$objectId}}">
                <input class="input-sm form-control" type="text" value="{{$objectName}}">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <label for="">Отправитель</label>
                <select name="send_email" id="send_email" class="form-control" required="required">
                    <option value="info@geometry-invest.ru">info@geometry-invest.ru</option>
                    <option value="info@kn-kontinent.ru">info@kn-kontinent.ru</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <label for="">Тестовый email</label>
                <input name="test_email" class="input-sm form-control" type="text" value="">
            </div>
        </div>
        @if (isset($documentsArr))
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="">Документы S2</label>
                    <select name="documents" id="documents" class="form-control">
                        <option value=""></option>
                        @foreach($documentsArr as $document)
                            <option value="{{$document['params']}}">{{$document['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        @if (isset($links))
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="">Документы</label>
                    <select name="customDocuments" id="customDocuments" class="form-control">
                        @foreach($links as $name => $link)
                            <option value="{{$link}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-10 offset-lg-1 form-group">
                    Ссылка: <a target="_blank" class="customDocumentsLink" href=""></a>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <label for="">Тема письма</label>
                <input class="form-control" name="subject" id="subject" type="text" value="{{$subject}}" required="required"></input>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <label for="">Заголовок письма</label>
                <textarea class="form-control" name="header_body" id="header_body" required="required">{{$objectAddress}}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <label for="">Тело письма</label>
                <textarea id="body_body"></textarea>
                <input type="hidden" name="body_body" id="body_body_hidden" value="{{$objectDescription}}">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group text-center">
                <input type="button" class="btn btn-success" id="submit" value="Отправить">
                <input type="submit" class="d-none" id="submitHidden" value="Отправить">
            </div>
        </div>
    </form>
@overwrite

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" integrity="sha512-3q8fi8M0VS+X/3n64Ndpp6Bit7oXSiyCnzmlx6IDBLGlY5euFySyJ46RUlqIVs0DPCGOypqP8IRk/EyPvU28mQ==" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="/css/editor.css" rel="stylesheet" />

    <style>
        .btm-color .slider-selection {
            background: #00e5ff;
        }

        .hidden {
            display: none;
        }
    </style>
@show

@section('js')
    @parent
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="/js/editor.js"></script>

    <script>
        $(document).ready(function() {
            $('#documents').select2({
                closeOnSelect: false,
                placeholder: "",
                tags: true,
                allowClear: true,
            });

            $('#customDocuments').select2({
                placeholder: "",
                closeOnSelect: false,
                allowClear: true,
            }).on('change', function (e) {
                var links = $('.customDocumentsLink');
                var linksValue = this.value;
                
                links.each(function( index ) {
                    $(this).attr('href', linksValue);
                    $(this).html(linksValue);
                });
            });

            $('#customDocuments').change();

            $('#emails').select2({
                closeOnSelect: false,
                tags: true,
            });

            $('#emails').on('select2:unselect', function (e) {
                var param = e.params.data;
                var data = {
                    id: param.id,
                    text: param.text
                };

                var newOption = new Option(data.text, data.id, true, true);
                $('#exceptEmails').append(newOption).trigger('change');
            });

            $('#exceptEmails').select2({
                closeOnSelect: false,
                tags: true,
            });

            $('#exceptEmails').on('select2:unselect', function (e) {
                var param = e.params.data;
                var data = {
                    id: param.id,
                    text: param.text
                };

                var newOption = new Option(data.text, data.id, true, true);
                $('#emails').append(newOption).trigger('change');
            });


            $('body').on('click', '.delete-document', function() {
               $(this).closest('.row').remove();
            });

            $('body').on('click', '.custom-control-input', function() {
                updatePage();
            });

            $(document).ready(function() {
                $("#body_body").Editor();
                $("#body_body").Editor("setText", $('#body_body_hidden').val());
            });

            $('body').on('click', '#submit', function(e) {
                $("#body_body_hidden").val($("#body_body").Editor("getText"));
                $('#submitHidden').click();
            });
        });

        function updatePage()
        {
            var isCompanyEmail = $("#company_email").prop('checked') ? 1 : 0;
            var isContactEmail = $("#contact_email").prop('checked') ? 1 : 0;

            location.href = location.href + '&company_email=' + isCompanyEmail + '&contact_email=' + isContactEmail;
        }
    </script>
@overwrite

