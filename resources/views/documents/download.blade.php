@extends('layouts')

@section("content")
    <div class="row">
        <div class="col">
            <form action="/weebhook_documents_attach" method="POST" class="text-center">
                <input type="hidden" name="filename" value="{{$filename}}">
                <input type="submit" class="btn btn-success" value="Download" style="margin-top: 20px;">
            </form>
        </div>
    </div>
@overwrite
