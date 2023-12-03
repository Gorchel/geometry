<?php

namespace App;

use App\Helpers\CustomHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SenderLinks
 * @package App
 */
class UpdateTables extends Model
{
    /**
     * @var string
     */
    protected $table      = 'update_tables';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    const ORDERS_TYPE = 'orders';
    const PROPERTIES_TYPE = 'property';
    const COMPANY_TYPE = 'company';
    const CONTACT_TYPE = 'contact';

    /**
     * @var array
     */
    protected $fillable = [
        'type'
    ];

    /**
     * @return mixed
     */
    public static function getUpdates()
    {
        $records =  UpdateTables::select('type', 'updated_at')
            ->pluck('updated_at', 'type')
            ->toArray();

        $updates = [];

        foreach ($records as $type => $updated_at) {
            $updates[] = [
                'name' => static::getTypeName($type),
                'updated_at' => $updated_at
            ];
        }

        return $updates;
    }

    public static function getTypeName(string $type)
    {
        if (!in_array($type, [static::ORDERS_TYPE, static::PROPERTIES_TYPE, static::COMPANY_TYPE, static::CONTACT_TYPE])) {
            return '';
        }

        $names = [
            static::ORDERS_TYPE => 'Заказы',
            static::PROPERTIES_TYPE => 'Недвижимость',
            static::COMPANY_TYPE => 'Компании',
            static::CONTACT_TYPE => 'Контакты',
        ];

        return $names[$type];
    }

    /**
     * @param string $type
     * @return mixed
     */
    public static function updateTable(string $type)
    {
        if (!in_array($type, [static::ORDERS_TYPE, static::PROPERTIES_TYPE, static::COMPANY_TYPE, static::CONTACT_TYPE])) {
            return false;
        }

        $record = UpdateTables::where('type', $type)
            ->first();

        if (empty($record)) {
            $record = new UpdateTables();
            $record->type = $type;
        }

        $record->updated_at = time();

        return $record->save();
    }
}
