<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table      = 'companies';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'attributes', 'customs', 'relationships','created_at', 'updated_at'
    ];

    const NOT_ACTIVE_STATUS = 113375;
    const ACTIVE_STATUS = 113375;
    const ACTIVE_OWNER_STATUS = 142500;
    const IN_DEV_STATUS = 142499;//В разработке
    const IN_ARCHIVE = 113376;//В архиве
    const TEMPORARY_NOT_DEV = 113374;//Временно не развивается

    const CUSTOM_NETWORK = 'custom-88185';//сетевой
    const CUSTOM_NETWORK_DEFAULT = 'Сетевой';//сетевой
    const CUSTOM_NETWORK_VALUES = [
        'Сетевой', 'Сетевой (до 5 точек)', 'НЕ Сетевой'
    ];

    /**
     * @return mixed
     */
    public function sending_entities()
    {
        return $this->hasMany(PropertiesSendingEntities::class, 'item_id')->where('item', PropertiesSendingEntities::COMPANY_ITEM);
    }

}
