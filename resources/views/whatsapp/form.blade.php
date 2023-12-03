<?php
    use \App\Classes\Whatsapp\Sender;
?>
@extends('layouts')

@section("content")
    <form method="POST" action="/weebhook_whatsapp_send" id="submitForm">
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

                        if ($request->has('company_phone')) {
                            if (!empty($request->get('company_phone'))) {
                                $isChecked = true;
                            } else {
                                $isChecked = false;
                            }
                        }
                    ?>

                    <input type="checkbox" class="custom-control-input" id="company_phone" name="company_phone" {{!empty($isChecked) ? 'checked="checked"' : ''}} value="1">
                    <label class="custom-control-label" for="company_phone">Телефон компании</label>
                </div>
            @endif
            <div class="col-lg-10 offset-lg-1 form-group" style="padding-left: 40px;">
                <input type="checkbox" class="custom-control-input" id="contact_phone" name="contact_phone" {{!$request->has('contact_phone') || !empty($request->get('contact_phone')) ? 'checked="checked"' : ''}} value="1">
                <label class="custom-control-label" for="contact_phone">Телефон контактов</label>
            </div>
            <div class="col-lg-10 offset-lg-1 form-group">
                <input type="hidden" name="phones_json" value="{{json_encode($phones)}}">
                <select name="phones[]" id="phones" class="form-control" multiple="multiple"  required="required">
                    @foreach ($phones as $phone => $info)
                        <option value="{{$phone}}" selected="selected">{{$info['description']}}</option>
                    @endforeach
                </select>
            </div>
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
                <label for="">Тестовый телефон</label>
                <input name="test_phone" class="input-sm form-control" type="text" value="">
            </div>
        </div>
        @if (isset($links))
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="">КП</label>
                    <select name="kpType" id="kpType" class="form-control">
                        @foreach($links as $name => $link)
                            <option value="{{$link}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-10 offset-lg-1 form-group">
                    Ссылка: <a target="_blank" id="kpTypeLink" href=""></a>
                </div>
            </div>
        @endif
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
            $('#kpType').select2({
                placeholder: "",
                closeOnSelect: false,
                allowClear: true,
            }).on('change', function (e) {
                var link = $('#kpTypeLink');
                link.attr('href', this.value);
                link.html(this.value);
            });

            $('#kpType').change();

            $('#phones').select2({
                closeOnSelect: false,
                tags: true,
            });

            $('#phones').on('select2:unselect', function (e) {
                var param = e.params.data;
                var data = {
                    id: param.id,
                    text: param.text
                };

                // var newOption = new Option(data.text, data.id, true, true);
                // $('#exceptEmails').append(newOption).trigger('change');
            });

            // $('#exceptEmails').select2({
            //     closeOnSelect: false,
            //     tags: true,
            // });
            //
            // $('#exceptEmails').on('select2:unselect', function (e) {
            //     var param = e.params.data;
            //     var data = {
            //         id: param.id,
            //         text: param.text
            //     };
            //
            //     var newOption = new Option(data.text, data.id, true, true);
            //     $('#emails').append(newOption).trigger('change');
            // });

            $('body').on('click', '.custom-control-input', function() {
                updatePage();
            });

            $('body').on('click', '#submit', function(e) {
                $('#submitHidden').click();
            });
        });

        function updatePage()
        {
            var isCompanyPhone = $("#company_phone").prop('checked') ? 1 : 0;
            var isContactPhone = $("#contact_phone").prop('checked') ? 1 : 0;

            location.href = location.href + '&company_phone=' + isCompanyPhone + '&contact_phone=' + isContactPhone;
        }
    </script>
@overwrite

