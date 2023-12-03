@extends('layouts')

@section("content")
    <div class="row">
        <div class="col-md-12">
            @if ($response['status'])
                <p><b>Через несколько минут статистика будет обновлена.</b>  BST id {{$response['text']}}</p>
            @else
                <p><b>Ошибка. {{$response['text']}}.</b>  Данные по объекту {{$response['object']}}</p>
            @endif
        </div>
        <div class="col-md-6">
            <a class="btn btn-sm btn-info" href="/webhook_objects_get_bst_statistic?{{http_build_query($request)}}">Назад</a>
        </div>
    </div>

    @parent
@overwrite
