<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Gallery
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Gallery extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        die('Coming soon');
        $block = Mage::getBlockSingleton('expertcms/adminhtml_attribute_form_renderer_gallery');
        /**
         * @var $block Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Renderer_Gallery
         */

        $block->setAttributeCode($this->getEntityAttribute()->getAttributeCode());

        return $block->render($this);
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getParentName()
    {
        return parent::getName();
    }
}