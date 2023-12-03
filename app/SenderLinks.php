<?php

namespace App;

use App\Helpers\CustomHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SenderLinks
 * @package App
 */
class SenderLinks extends Model
{
    /**
     * @var string
     */
    protected $table      = 'sender_links';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'property_id', 'link', 'type'
    ];

    /**
     * @param string $name
     * @return mixed
     */
    public static function findLinkByName(string $link)
    {
        $linkModel = self::where('link', $link)
            ->first();

        return $linkModel;
    }

    /**
     * @param int $propertyId
     * @param string $type
     * @param $object
     * @return SenderLinks
     */
    public static function generateLink(int $propertyId, string $type, $object, $postfix = '')
    {
        $linkModel = self::where('property_id', $propertyId)
            ->where('type', $type)
            ->first();

        if (!empty($linkModel)) {
            return $linkModel;
        }

        $linkModel = new self;
        $linkModel->property_id = $propertyId;
        $linkModel->type = $type;
        $linkModel->link = static::makeLink($object, $postfix);

        $linkModel->save();

        return $linkModel;
    }

    /**
     * @param $object
     * @return string|string[]
     */
    protected static function makeLink($object, $postfix = '')
    {
        $attribute = $object['attributes'];

        $name = '';

        if (!empty($postfix)) {
            $name .= $postfix;
        }

        $type = CustomHelper::issetField($attribute['customs'], Properties::CUSTOM_TYPE, []);
        if (!empty($type)) {
            $name .= $type[0];
        }

        $name .= $attribute['address'].' ('.$attribute['total-area'].' кв.)';
        
        $name = str_replace(' ','', trim($name));
        $name = str_replace(' ','', trim($name));
        $name = str_replace('/','', trim($name));
        $name = str_replace('.','', trim($name));
        $name = str_replace(',','', trim($name));
        $name = str_replace('__','', trim($name));


        return $name;
    }
}
