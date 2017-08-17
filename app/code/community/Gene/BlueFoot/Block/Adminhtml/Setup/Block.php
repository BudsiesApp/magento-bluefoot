<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_block';
        $this->_headerText = Mage::helper('gene_bluefoot')->__('Manage Page Builder Blocks');
        $this->_addButtonLabel = Mage::helper('gene_bluefoot')->__('Add Page Builder Block');

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
        return $this->getUrl('*/genecms_setup_block/new');
    }
}