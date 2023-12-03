<?php

namespace App\Traits;

use App\UpdateTables as UpdateTablesModel;

trait UpdatedTablesTrait {

    public static function getUpdatedRecords() {
        return UpdateTablesModel::getUpdates();
    }
}
