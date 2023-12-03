<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertyToAr
 * @package App
 */
class PropertyToAr extends Model
{
    /**
     * @var string
     */
    protected $table      = 'property_to_ar';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'property_id', 'ar_id', 'updated_at', 'response'
    ];

    /**
     * Get the phone associated with the user.
     */
    public function property()
    {
        return $this->hasOne(Properties::class, 'id', 'property_id');
    }

    /**
     * @param int $property_id
     * @param int $ar_id
     * @return PropertyToAr
     */
    public static function getModel(int $property_id, int $ar_id)
    {
        $record = self::where('property_id', $property_id)
            ->where('ar_id', $ar_id)
            ->first();

        if (empty($record)) {
            $record = new self();
            $record->property_id = $property_id;
            $record->ar_id = $ar_id;
        }

        return $record;
    }
}
