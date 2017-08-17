<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Importexport_Import
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Importexport_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_importexport';
        $this->_mode = 'import';

        parent::__construct();

        $this->_updateButton('save', 'label','Import');
        $this->removeButton('delete');
    }
}