<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_addButtonLabel;

    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_installer';
        $this->_headerText = Mage::helper('gene_bluefoot')->__('Manage Installers');

        parent::__construct();

        $this->_updateButton('add', 'label', Mage::helper('gene_bluefoot')->__('Create Custom Installer'));
        $this->_addButton('import', array(
            'label'     => Mage::helper('gene_bluefoot')->__('Import installer from file'),
            'onclick'   => 'setLocation(\'' . $this->getImportUrl() .'\')',
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

    public function getImportUrl()
    {
        return $this->getUrl('*/genecms_setup_installer/importfile');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/genecms_setup_installer_create/start');
    }

}