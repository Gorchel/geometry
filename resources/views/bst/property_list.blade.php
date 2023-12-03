@extends('layouts')

@section("content")
    <div class="container">
        <div class="row">
            <form action="/bst/property_list">
                <input type="text" class="form-control input-sm" name="property_id">
                <input type="submit" value="Поиск" сlass="btn btn-info">
            </form>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center" style="margin-top: 20px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>statistic</th>
                            <th>text</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($models as $model)
                            <tr>
                                <td>{{$model->id}}</td>
                                <td>{{$model->is_statistic}}</td>
                                <td>{{$model->statistic_text}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$models->links()}}
            </div>
        </div>
    </div>
    @parent
@overwrite

@section('js')
    @parent
@overwrite

@section('css')
    @parent
@overwrite

