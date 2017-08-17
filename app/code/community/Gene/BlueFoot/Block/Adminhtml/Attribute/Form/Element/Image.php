<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Image
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media') . 'gene-bluefoot/' . $this->getValue();
        }
        return $url;
    }
}