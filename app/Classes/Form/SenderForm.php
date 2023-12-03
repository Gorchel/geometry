<?php

namespace App\Classes\Form;

use App\SenderObject;

/**
 * Class SenderForm
 * @package App\Classes\Form;
 */
class SenderForm
{
    /**
     * @var
     */
    protected $objectId;
    protected $type;
    protected $link = null;

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function store(string $email): bool
    {
        if (!empty(SenderObject::getModel($this->objectId, $this->type, $email))) {
            return true;
        }

        $senderModel = new SenderObject();
        $senderModel->property_id = $this->objectId;
        $senderModel->type = $this->type;
        $senderModel->email = $email;
        $senderModel->link = $this->link;
        $senderModel->save();

        return true;
    }
}
