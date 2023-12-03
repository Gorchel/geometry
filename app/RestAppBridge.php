<?php

namespace App;

use App\Classes\BST\ApiPlaceClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BstBridge
 * @package App
 */
class RestAppBridge extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table      = 'rest_app_bridge';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'property_id', 'link', 'json', 'hash', 'type'
    ];

    public const HEADER_FILE = [
        'appointment' => 0,
        'link' => 1,
        'address' => 2,
        'something' => 3,
        'total' => 4,
        'price' => 5,
        'price_per_meter' => 6,
        'description' => 7,
        'landlord' => 8,
        'phone' => 9,
        'id' => 10,
        'name' => 11,
        'type' => 12,
        'lat' => 13,
        'lon' => 14,
    ];


}
