<?php

namespace Magehit\Storelocator\Block\Adminhtml\Storelocator\Edit\Renderer;
use Magento\Framework\Escaper;
class Time extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
         \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('magehittime');
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        $name = parent::getName();
        if (strpos($name, '[]') === false) {
            $name .= '[]';
        }
        return $name;
    }

    public function getElementHtml()
    {
        $this->addClass('select admin__control-select');
        $value_hrs = 0;
        $value_min = 0;


        if ($values = $this->getTime()) {
            //$values = explode(',', $value);
            
            if (is_array($values) && count($values) == 2) {
                $value_hrs = $values[0];
                $value_min = $values[1];

            }
        }
        $html = '<input type="hidden" id="' . $this->getHtmlId() . '" ' . $this->_getUiId() . '/>';
        $html .= '<select name="' . $this->getName() . '" style="width:80px" ' . $this->serialize(
            $this->getHtmlAttributes()
        ) . $this->_getUiId(
            'hour'
        ) . '>' . "\n";
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . ($value_hrs ==
                $i ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }
        $html .= '</select>' . "\n";
        $html .= ':&nbsp;<select name="' . $this->getName() . '" style="width:80px" ' . $this->serialize(
            $this->getHtmlAttributes()
        ) . $this->_getUiId(
            'minute'
        ) . '>' . "\n";
        for ($i = 0; $i < 60; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . ($value_min ==
                $i ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }
        $html .= '</select>' . "\n";

        $html .= $this->getAfterElementHtml();
        return $html;
    }
}