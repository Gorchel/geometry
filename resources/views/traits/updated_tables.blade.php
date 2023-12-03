<p style="font-size: 13px; margin-bottom: 0px;">Обновление таблиц</p>
<ul class="updated_tables_ul" style="margin-bottom: 0px !important;">
    @foreach($updatedTablesRecords as $updateRecord)
        <li style="font-size: 10px;">{{$updateRecord['name']}} - {{$updateRecord['updated_at']}}</li>
    @endforeach
</ul>
