<?php

namespace OLTC\TimeSelector\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use OLTC\TimeSelector\Model\TimeSelector;

class ProductAttributeTypesObserver implements ObserverInterface
{
    /**
     * Add new attribute type to manage attributes interface
     *
     * @param   Observer $observer
     * @return  $this
     */
    public function execute(Observer $observer)
    {
        // adminhtml_product_attribute_types

        /** @var DataObject $response */
        $response = $observer->getEvent()->getResponse();

        $types = $response->getTypes();
        $types[] = [
            'value' => TimeSelector::FORM_ELEMENT_TIME_SELECTOR,
            'label' => __('Time selector'),
        ];

        $response->setTypes($types);
    }
}
