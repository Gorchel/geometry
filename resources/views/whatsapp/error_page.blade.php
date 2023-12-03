@extends('layouts')

@section("content")
    <div class="row">
        <div class="col-md-12">
            <p>{{$msg}}</p>
        </div>
        <div class="col-md-6">
            <a class="btn btn-sm btn-info" href="/weebhook_documents?{{http_build_query($request)}}">Назад</a>
        </div>
    </div>

    @parent
@overwrite
