<?php

namespace OLTC\TimeSelector\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class TimeSelector extends AbstractModifier {

    /** @var LocatorInterface */
    protected $_locator;

    private $attributeList = [];

    public function __construct(
        LocatorInterface $locator
    )  {
        $this->_locator = $locator;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $model = $this->_locator->getProduct();
        $modelId = $model->getId();

        foreach ($this->attributeList as $attributeCode) {
            if (isset($data[$modelId][self::DATA_SOURCE_DEFAULT][$attributeCode]) &&
                !is_array($data[$modelId][self::DATA_SOURCE_DEFAULT][$attributeCode] &&
                !empty($data[$modelId][self::DATA_SOURCE_DEFAULT][$attributeCode]))) {
                $timeData = explode(
                    \OLTC\TimeSelector\Model\TimeSelector::TIME_SEPARATOR,
                    $data[$modelId][self::DATA_SOURCE_DEFAULT][$attributeCode]
                );
                $data[$modelId][self::DATA_SOURCE_DEFAULT][$attributeCode] = [
                    "hours" => array_shift($timeData),
                    "minutes" => array_shift($timeData),
                ];
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        foreach ($meta as $groupCode => $groupConfig) {
            $meta[$groupCode] = $this->modifyMetaConfig($groupConfig);
        }

        return $meta;
    }

    /**
     * Modification of group config
     *
     * @param array $metaConfig
     * @return array
     */
    protected function modifyMetaConfig(array $metaConfig)
    {
        if (isset($metaConfig['children'])) {
            foreach ($metaConfig['children'] as $attributeCode => $attributeConfig) {
                if ($this->startsWith($attributeCode, self::CONTAINER_PREFIX)) {
                    $metaConfig['children'][$attributeCode] = $this->modifyMetaConfig($attributeConfig);
                } elseif (!empty($attributeConfig['arguments']['data']['config']['formElement']) &&
                    $attributeConfig['arguments']['data']['config']['formElement'] === \OLTC\TimeSelector\Model\TimeSelector::FORM_ELEMENT_TIME_SELECTOR
                ) {
                    $this->attributeList[] = $attributeCode;
                    $metaConfig['children'][$attributeCode] =
                        $this->modifyAttributeConfig($attributeCode, $attributeConfig);
                }
            }
        }

        return $metaConfig;
    }

    /**
     * Modification of attribute config
     *
     * @param string $attributeCode
     * @param array $attributeConfig
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function modifyAttributeConfig($attributeCode, array $attributeConfig)
    {
        return array_replace_recursive($attributeConfig, [
            "arguments" => [
                "data" => [
                    "config" => [
                        'componentType' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'dataScope' => $attributeCode,
                        'visible' => true,
                        'additionalClasses' => 'admin__control-grouped-time',
                        'breakLine' => false,
                    ],
                ],
            ],
            "children" => [
                'hours' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => Text::NAME,
                                'formElement' => Select::NAME,
                                'componentType' => Field::NAME,
                                'dataScope' => 'hours',
                                'visible' => true,
                                'additionalClasses' => 'admin__field-time',
                                'options' => $this->generateOptions(0, 24),
                                'showLabel' => false,
                            ],
                        ],
                    ]
                ],
                'minutes' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => Text::NAME,
                                'formElement' => Select::NAME,
                                'componentType' => Field::NAME,
                                'dataScope' => 'minutes',
                                'visible' => true,
                                'additionalClasses' => 'admin__field-time admin__field-group-show-label',
                                'options' => $this->generateOptions(0, 60),
                                'label' => \OLTC\TimeSelector\Model\TimeSelector::TIME_SEPARATOR,
                                'showLabel' => true,
                            ],
                        ],
                    ]
                ]
            ]
        ]);
    }

    protected function generateOptions($start, $end) {
        $options = [];
        for($i = $start; $i < $end; $i++){
            $options[] = [
                'value' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'label' => str_pad($i, 2, '0', STR_PAD_LEFT),
            ];
        }
        return $options;
    }
}
