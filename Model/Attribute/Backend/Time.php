<?php

namespace OLTC\TimeSelector\Model\Attribute\Backend;

use OLTC\TimeSelector\Model\TimeSelector;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

class Time extends AbstractBackend {

    public function beforeSave($object)
    {
        if($timeData = $object->getData($this->getAttribute()->getAttributeCode())) {
            if(is_array($timeData) && isset($timeData["hours"]) && isset($timeData["minutes"])){
                $time = $timeData["hours"] . TimeSelector::TIME_SEPARATOR . $timeData["minutes"];
                $object->setData($this->getAttribute()->getAttributeCode(), $time);
            }
        }
        return parent::beforeSave($object);
    }

}
