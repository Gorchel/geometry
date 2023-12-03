<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Properties
 *
 * @property int id
 * @property string attributes
 * @property string status
 * @property string link
 * @property string details
 * @property int property_id
 * @property string inventory
 * @property string json
 *
 * @package App
 *
 * @property int $type
 */
class ParsingQueue extends Model
{
    protected $table      = 'parsing_queue';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type', 'status', 'link', 'details', 'property_id', 'inventory', 'json', 'status_updated_at'
    ];

    const LOT_ONLINE = 1;
    const TORGI_GOV = 2;
    const CIAN = 3;
    const TORGI_RU = 4;

    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_DONE = 2;
    const STATUS_ERROR = 3;
    const STATUS_SHEET_UPDATE = 4;
    const STATUS_GONE = 5;

    /**
     * @param $status
     * @return string
     */
    public static function type2Label($status) {
        switch ($status) {
            case static::LOT_ONLINE:
                return 'Lot online';
            case static::TORGI_GOV:
                return 'Torgi gov';
            case static::CIAN:
                return 'ЦИАН';
            case static::TORGI_RU:
                return 'Torgi ru';
            default:
                return 'Тип не найден';
        }
    }

    /**
     * @param $status
     * @return string
     */
    public static function status2Label($status) {
        switch ($status) {
            case static::STATUS_PENDING:
                return 'В ожидании';
            case static::STATUS_PROCESSING:
                return 'Парсится';
            case static::STATUS_DONE:
                return 'Готов';
            case static::STATUS_ERROR:
                return 'Ошибка';
            case static::STATUS_GONE:
                return 'Завершен';
            default:
                return 'Статус не найден';
        }
    }
}
