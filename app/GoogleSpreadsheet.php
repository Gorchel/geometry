<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GooglePropertySheet
 *
 * @property int id
 * @property string spreadsheet_id
 * @property string spreadsheet_name
 *
 * @package App
 */
class GoogleSpreadsheet extends Model
{
    protected $table      = 'google_spread_sheet';
    protected $primaryKey = 'id';

    protected $fillable = [
        'spreadsheet_id', 'spreadsheet_name'
    ];

    /**
     * @return mixed
     */
    public function getSheetCountAttribute()
    {
        return $this->properties_sheets()->count();
    }


    /**
     * @return mixed
     */
    public function properties_sheets()
    {
        return $this->hasMany(GooglePropertySheet::class, 'spreadsheet_id');
    }
}
