<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit_Tab_Options
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit_Tab_Options
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('gene_bluefoot')->__('Manage Label / Options');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('gene_bluefoot')->__('Properties');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
