<?php

namespace App;

use App\Classes\BST\ApiPlaceClass;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SameDeals
 * @package App
 */
class SameDeals extends Model
{
    /**
     * @var string
     */
    protected $table      = 'same_deals';
    /**
     * @var string
     */
    protected $primaryKey = 'id';
}
