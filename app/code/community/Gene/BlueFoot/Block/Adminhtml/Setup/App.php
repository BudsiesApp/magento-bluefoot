<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_app';
        $this->_headerText = Mage::helper('gene_bluefoot')->__('Manage Content Apps');
        $this->_addButtonLabel = Mage::helper('gene_bluefoot')->__('Add New Content App');

        parent::__construct();

        $this->_addButton('addUsingWizard', array(
            'label'     => 'App Wizard',
            'onclick'   => 'setLocation(\'' . $this->getWizardUrl() .'\')',
            'class'     => 'add',
        ));
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
        return $this->getUrl('*/genecms_setup_app/new');
    }

    public function getWizardUrl()
    {
        return $this->getUrl('*/genecms_setup_appwizard/start');
    }
}