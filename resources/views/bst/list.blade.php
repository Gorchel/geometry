@extends('layouts')

@section("content")
    <div class="container">
        <div class="row">
            <div class="col">
                <p>В работе: {{$pending}}</p>
                <p>Выполнено: {{$completed}}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center" style="margin-top: 20px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>property_id</th>
                            <th>bst_id</th>
                            <th>created_at</th>
                            <th>status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($models as $model)
                            <tr>
                                <td>{{$model->id}}</td>
                                <td>{{$model->property_id}}</td>
                                <td>{{$model->bst_id}}</td>
                                <td>{{$model->created_at}}</td>
                                <td>{{$model->status}}</td>
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

