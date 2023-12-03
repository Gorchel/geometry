<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GooglePropertySheet
 * @package App
 *
 * @property integer id
 * @property integer sheet_id
 * @property string sheet_name
 * @property string spreadsheet_id
 * @property integer property_id
 *
 * @property GoogleSpreadsheet $sheet
 */
class GooglePropertySheet extends Model
{
    protected $table      = 'google_property_sheet';
    protected $primaryKey = 'id';

    protected $fillable = [
        'property_id', 'spreadsheet_id', 'sheet_id', 'sheet_name'
    ];

    /**
     * @return mixed
     */
    public function property()
    {
        return $this->belongsTo(Properties::class, 'id', 'property_id');
    }

    /**
     * Get the phone associated with the user.
     */
    public function sheet()
    {
        return $this->hasOne(GoogleSpreadsheet::class, 'id', 'spreadsheet_id');
    }
}
