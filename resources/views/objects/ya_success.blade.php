@extends('layouts')

@section("content")
    <div class="row text-center">
        <div class="col-lg-12">
            <b>Данные успешно обновлены!</b></span>
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
