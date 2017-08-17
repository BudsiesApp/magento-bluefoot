<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_attribute';
        $this->_headerText = Mage::helper('gene_bluefoot')->__('Manage Content Attributes');
        $this->_addButtonLabel = Mage::helper('gene_bluefoot')->__('Add Attribute');

        parent::__construct();
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/genecms_setup_attribute/new');
    }
}