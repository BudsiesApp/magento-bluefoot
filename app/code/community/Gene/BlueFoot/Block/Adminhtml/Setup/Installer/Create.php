<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_installer';
        $this->_mode = 'create';

        parent::__construct();

        $this->_updateButton('save', 'label','Create Installer');
        $this->removeButton('delete');
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/genecms_setup_installer/');
    }
}