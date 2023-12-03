<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table      = 'contacts';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'attributes', 'customs', 'relationships','created_at', 'updated_at','double', 'is_update'
    ];

    public const ACTIVE_STATUS = 150254;
    public const HYPERACTIVE_STATUS = 150255;
    public const ARCHIVE_STATUS = 148448;
    public const WARM_STATUS = 148446;
    public const HOT_STATUS = 148445;

    public const STATUS_FIELD = 'custom-63791';
    public const COMPANY_FIELD = 'custom-88118';
    public const DISTRICT_FIELD = 'custom-65519';
    public const TYP_OF_ACTIVITY = 'custom-87412';

    public const CONTACT_TYPE = 'custom-66380';

    public const CHAT_TYPE_FIELD = 'custom-66821';//Общаемся через
    public const SENDING_MESSAGE_FIELD = 'custom-88172';//Было рассылка (Почта/Whatsapp)

    public const SENDING_EMAIL = 'Почта';
    public const SENDING_WHATSAPP = 'Whatsapp';

    public const FORBIDDEN_STATUSES = ['Интересно (дата, перезвонить)','Просмотр','Да, готов к сделке','Думает, перезвонить (дата)',
            'Нет (причина отмены)', 'Дог. ар/субар. Наш','Дог. ар/субар. От клиента','Дог. ар. Наш','Дог. ар. От собственника','Клиент согласован',
            'Клиент предварительно одобрен', 'Клиент одобрен собственником','Сделка успешна','Сделка завершена','Предварительно одобрен',
            'Отказ (причина отмены)', 'Отказ по цене', 'Отказ по месту', 'Отказ по площади', 'Отказ по планировке',
            'Отказ: Рядом есть точки', 'Отказ: Компания не развивается', 'Отказ по проходимости', 'Отказ - Не формат',
            'Отказ руководства'
        ];

    public const FORBIDDEN_OS_STATUSES = ['Интересно (дата, перезвонить)','Просмотр','Да, готов к сделке','Думает, перезвонить (дата)',
        'Дог. ар/субар. Наш','Дог. ар/субар. От клиента','Дог. ар. Наш','Дог. ар. От собственника','Клиент согласован',
        'Клиент предварительно одобрен', 'Клиент одобрен собственником','Сделка успешна','Сделка завершена','Предварительно одобрен',
        'Отказ (причина отмены)', 'Отказ по цене', 'Отказ по месту', 'Отказ по площади', 'Отказ по планировке',
        'Отказ: Рядом есть точки', 'Отказ: Компания не развивается', 'Отказ по проходимости', 'Отказ - Не формат',
        'Отказ руководства'
    ];

    public const FORBIDDEN_PASSED_STATUSES = ['Отказ по цене', 'Отказ по месту', 'Отказ по площади', 'Отказ по планировке',
        'Отказ по проходке', 'Рядом есть точки', 'Не формат', 'Помещение не интересует', 'Отказ (причина отмены)',
        'Отказ: Рядом есть точки', 'Отказ: Компания не развивается', 'Отказ по проходимости', 'Отказ - Не формат',
        'Отказ руководства'
    ];

    /**
     * @return mixed
     */
    public function sending_entities()
    {
        return $this->hasMany(PropertiesSendingEntities::class, 'item_id')->where('item', PropertiesSendingEntities::CONTACT_ITEM);
    }
}
