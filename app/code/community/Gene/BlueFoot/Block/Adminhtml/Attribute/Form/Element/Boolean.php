<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Boolean
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Boolean extends Varien_Data_Form_Element_Select
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setValues(array(
            array(
                'label' => Mage::helper('catalog')->__('No'),
                'value' => 0,
            ),
            array(
                'label' => Mage::helper('catalog')->__('Yes'),
                'value' => 1,
            ),
        ));
    }
}