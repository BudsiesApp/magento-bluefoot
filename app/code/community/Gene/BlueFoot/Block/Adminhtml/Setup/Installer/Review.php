<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Review
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Review extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_headerText = 'Installer - Mock Import Report';

    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_installer';
        $this->_mode = 'review';

        parent::__construct();

        $this->_updateButton('save', 'label','Confirm');
        $this->removeButton('delete');
    }
}