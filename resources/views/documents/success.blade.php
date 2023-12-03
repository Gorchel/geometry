@extends('layouts')

@section("content")
    <div class="row">
        <div class="col-md-12">
            @if (isset($result['status']))
                <h2>Рассылка добавлена в очередь</h2>
            @endif
            <p><?php echo json_encode($result); ?></p>
        </div>
    </div>
    @parent
@overwrite
