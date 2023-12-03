@extends('layouts')

@section("content")
    <div class="row text-center">
        <div class="col-lg-12">
            @if (!empty($result['response']['id']))
                <b>Объект #{{$result['response']['id']}} скопирован!</b><br/>
                <a href="{{config('main.url_crm')}}estate/properties/{{$result['response']['id']}}" target="_blank">Ссылка на объект</a>
            @else
                <b>Ошибка! Объект #{{$result['response']['id']}}!</b><br/>
                <p>{{$result}}</p>
            @endif
        </div>
    </div>
    @parent
@overwrite
