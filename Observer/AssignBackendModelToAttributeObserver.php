<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace OLTC\TimeSelector\Observer;

use Magento\Framework\Event\Observer;
use OLTC\TimeSelector\Model\TimeSelector;
use Magento\Framework\Event\ObserverInterface;
use OLTC\TimeSelector\Model\Attribute\Backend\Time;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

class AssignBackendModelToAttributeObserver implements ObserverInterface
{
    /**
     * Automatically assign backend model to weee attributes
     *
     * @param   Observer $observer
     * @return  $this
     */
    public function execute(Observer $observer)
    {
        /** @var $object AbstractAttribute */
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == TimeSelector::FORM_ELEMENT_TIME_SELECTOR) {
            $object->setBackendType("varchar");
            $object->setBackendModel(Time::class);
        }

        return $this;
    }
}
