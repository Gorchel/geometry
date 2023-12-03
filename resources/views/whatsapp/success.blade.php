@extends('layouts')

@section("content")
    <div class="row">
        <div class="col-md-12">
            <p>Результат: <?php echo json_encode($result); ?></p>
            <p>Ошибки: <?php echo json_encode($errors); ?></p>
        </div>
    </div>
    @parent
@overwrite
