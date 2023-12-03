<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SenderObject extends Model
{
    protected $table      = 'sender_object';
    protected $primaryKey = 'id';

    protected $fillable = [
        'property_id', 'type', 'email', 'created_at', 'updated_at','link'
    ];

    /**
     * @param int $propertyId
     * @param int $type
     * @param string $email
     * @return mixed
     */
    public static function getModel(int $propertyId, int $type, string $email)
    {
        return self::where('property_id', $propertyId)
            ->where('type', $type)
            ->where('email', $email)
            ->first();
    }

    /**
     * @param int $propertyId
     * @param int $type
     * @param array $emails
     * @return mixed
     */
    public static function getModelByEmails(int $propertyId, ?int $type, array $emails)
    {
        return self::select('email')
            ->where('property_id', $propertyId)
            ->where('type', $type)
            ->whereIn('email', array_keys($emails))
            ->pluck('email')
            ->toArray();
    }
}
