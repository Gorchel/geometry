@extends('layouts')

@section("content")
    <div class="preloader-wrapper">
        <div class="preloader">
            Загрузка
        </div>
    </div>
    <div class="container">
        <div class="col text-center" style="margin-top: 30px;">
            <p>Генерация КП</p>
        </div>
        <form method="GET" action="/webhook/blanks" id="generate-form">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 form-group">
                    <label for="">ID объекта</label>
                    <input type="text" name="id" id="objId" class="form-control" value="{{isset($id) ? $id : ''}}">
                </div>
                <div class="col-lg-10 offset-lg-1 form-group">
                    <select name="template" id="listSelect" class="form-control">
                        @foreach($list as $key => $name)
                            <option value="{{$key}}">{{$name['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 offset-lg-1 form-group">
                    <input type="submit" class="btn btn-info"  value="Обновить">
                </div>
                <div class="col-lg-2 form-group">
                    <input type="button" class="btn btn-success" id="generateFile" value="Сформировать">
                </div>
                <div class="col-lg-2 form-group">
                    <input type="button" class="btn btn-info" id="attachFile" value="Закрепить">
                </div>
            </div>
        </form>
        @if (isset($links))
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Ссылки на КП
                            </button>
                        </h5>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td>Наименование</td>
                                    <td>Ссылка</td>
{{--                                    <td></td>--}}
                                    <td>Pdf</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($links as $name => $links)
                                    <tr>
                                        <td>{{$name}}</td>
                                        <td><input type="button" class="btn btn-success copy-text btn-sm" value="{{$name}}" data-copy="{{$links['link']}}"></td>
{{--                                        <td><a href="{{$links['link']}}" target="_blank">Открыть</a></td>--}}
                                        <td>
                                            @if(isset($id))
                                                <form action="/webhook/blanks-downloads/pdf" method="POST" class="download-pdf-form" target="_blank">
                                                    <input type="hidden" name="id" value="{{$id}}">
                                                    <input type="hidden" name="template" id="download-pdf-template" value="{{$links['template']}}">
                                                    <input type="button" class="btn btn-warning btn-sm submit-btn" value="Скачать">
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row" id="generateIframe"></div>
    </div>
    @parent
@overwrite

@section('js')
    @parent

    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.preloader-wrapper').addClass('preloader-site');

            $('body').on('click', '#generateFile', function(e) {
                var objId = $('#objId').val(),
                    template = $('select#listSelect').val();

                if (objId.length === 0 || template.length === 0) {
                    alert('Выберите id объекта и шаблон! ')
                }

                renderView(objId, template);
            });

            $('body').on('click', '#attachFile', function(e) {
                $(this).hide();
                $('.preloader-wrapper').removeClass('preloader-site');
                $.ajax({
                    type: 'POST',
                    url: "/webhook/blanks-attach/pdf",
                    data: $('#generate-form').serialize(),
                }).done(function(response) {
                    $('.preloader-wrapper').addClass('preloader-site');
                    console.log(response);
                    alert('Файл закреплен');
                });
            })

            $('body').on('click', '#sendFile', function(e) {
                $(this).hide();

                $.ajax({
                    type: 'POST',
                    url: "/webhook/blanks-send/pdf",
                    data: $('#generate-form').serialize(),
                }).done(function(response) {
                    console.log(response);
                    alert('Файл закреплен');
                });
            })

            $('body').on('click', '.copy-text', function() {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(this).data('copy')).select();
                document.execCommand("copy");
                $temp.remove();

                alert('Ссылка на КП скоирована!');
            })

            $('body').on('click', '.download-pdf-form .submit-btn', function(e) {
                e.preventDefault();
                $('.preloader-wrapper').removeClass('preloader-site');
                $.ajax({
                    type: 'POST',
                    url: "/webhook/blanks-downloads/pdf",
                    data: $(this).closest('.download-pdf-form').eq(0).serialize(),
                }).done(function(response) {
                    $('.preloader-wrapper').addClass('preloader-site');
                    console.log(response);
                    alert('Файл отправлен в телеграм');
                    // window.open(response['fileShortPath'] + '/' + response['fileName']);
                    // alert('Файл закреплен');
                });
            })
        });

        function renderView(id, template) {
            var url = '/webhook/blanks/' + id + '/' + template,
                iframe = '<iframe src="" frameborder="0" id="templateIframe"></iframe>';

            $('#generateIframe').html(iframe);

            $('#templateIframe').attr('src', url);

            // $('#download-pdf-template').val(template);
            // $('#download-pdf-id').val(id);
            //
            // $('#download-doc-template').val(template);
            // $('#download-doc-id').val(id);
        }
    </script>
@overwrite

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" integrity="sha512-3q8fi8M0VS+X/3n64Ndpp6Bit7oXSiyCnzmlx6IDBLGlY5euFySyJ46RUlqIVs0DPCGOypqP8IRk/EyPvU28mQ==" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .btm-color .slider-selection {
            background: #00e5ff;
        }

        .hidden {
            display: none;
        }

        #templateIframe {
            width: 100%;
            height: 600px;
        }

        .preloader-wrapper {
            height: 100%;
            width: 100%;
            background: #FFF;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999999;
        }

        .preloader-wrapper .preloader {
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            width: 120px;
        }

        .preloader-wrapper.preloader-site {
            display: none;
        }
    </style>
@overwrite
