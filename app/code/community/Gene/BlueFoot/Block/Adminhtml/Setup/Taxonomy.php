<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_taxonomy';
        $this->_headerText = Mage::helper('gene_bluefoot')->__('Manage Taxonomies');
        $this->_addButtonLabel = Mage::helper('gene_bluefoot')->__('Add New Taxonomy');

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
        return $this->getUrl('*/genecms_setup_taxonomy/new');
    }
}