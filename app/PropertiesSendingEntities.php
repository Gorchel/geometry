<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertiesSendingEntities
 *
 * @package App
 */
class PropertiesSendingEntities extends Model
{
    protected $table      = 'properties_sending_entities';
    protected $primaryKey = 'id';

    protected $fillable = [
        'property_id', 'item', 'item_id', 'id'
    ];

    const COMPANY_ITEM = 1;
    const CONTACT_ITEM = 2;
    const ORDER_ITEM = 3;


    /**
     * @param int $item
     * @param int $itemId
     * @param int $propertyId
     * @return bool
     */
    public static function setEntity(int $item, int $itemId, int $propertyId) {
        $model = new self();
        $model->item = $item;
        $model->item_id = $itemId;
        $model->property_id = $propertyId;

        return $model->save();
    }

    /**
     * @param int $propertyId
     */
    public static function delEntity(int $propertyId) {
        self::where('property_id', $propertyId)->delete();
    }
}
