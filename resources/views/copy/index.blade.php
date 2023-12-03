@extends('layouts')

@section("content")
    <form action="/copy_property" id="submitForm" method="POST">
        <div class="row" style="margin-bottom: 40px;"></div>
        <input type="hidden" name="id" value="{{$property['id']}}">
        <input type="hidden" name="property_type" value="{{$propertyType}}">
        <h2 class="text-center">Копирование обьекта недвижимости #{{$property['id']}}</h2>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                @include('traits.updated_tables', [
                    'updatedTablesRecords' => $updatedTablesRecords
                ])
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group">
                <select name="property_copied_type" id="property_copied_type" class="form-control">
                    @foreach($propertyTypeList as $type)
                        <option value="{{$type}}" {{$type == $propertyType ? 'selected="selected"' : ''}}>{{$type}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 offset-lg-1 form-group text-center">
                <input type="submit" class="btn btn-success" id="submit" value="Копировать обьект">
            </div>
        </div>
    </form>
    @parent
@overwrite

@section('js')
    @parent

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" integrity="sha512-f0VlzJbcEB6KiW8ZVtL+5HWPDyW1+nJEjguZ5IVnSQkvZbwBt2RfCBY0CBO1PsMAqxxrG4Di6TfsCPP3ZRwKpA==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#property_copied_type').select2({
                closeOnSelect: false
            });
        });
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
    </style>
@show
