@extends('layouts')

@section("content")
    <div class="row text-center">
        <div class="col-lg-12">
            <p>Данные на сервере обновлены!</p>
        </div>
    </div>
    @parent
@overwrite

@section('js')
    @parent
@overwrite

@section('css')
    @parent
@show
