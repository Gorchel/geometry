<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'attributes', 'customs', 'relationships','created_at', 'updated_at'
    ];

    const RENT_TYPE = 4;

    /**
     * @return mixed
     */
    public function sending_entities()
    {
        return $this->hasMany(PropertiesSendingEntities::class, 'item_id')->where('item', PropertiesSendingEntities::ORDER_ITEM);
    }
}
