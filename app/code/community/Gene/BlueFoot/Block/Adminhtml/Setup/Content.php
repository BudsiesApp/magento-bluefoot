<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Content
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_content';
        $this->_headerText = Mage::helper('gene_bluefoot')->__('Manage Content Types');
        $this->_addButtonLabel = Mage::helper('gene_bluefoot')->__('Add Content Type');

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
        return $this->getUrl('*/genecms_setup_content/new');
    }
}