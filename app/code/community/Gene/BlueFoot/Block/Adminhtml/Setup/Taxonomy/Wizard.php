<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Wizard
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Wizard extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "type_id";
        $this->_blockGroup = "gene_bluefoot";
        $this->_controller = "adminhtml_setup_taxonomy";
        $this->_mode = 'wizard';
        
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('back');

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Cancel'),
            'onclick'   => 'appWizard.reloadStep();',
            'class'     => 'back',
        ), -1);


        $this->_addButton("saveandcontinue", array(
            "label" => Mage::helper("gene_bluefoot")->__("Save Taxonomy"),
            "onclick" => "appWizard.saveAdditional('taxonomy_form');",
            "class" => "save",
        ), -100);

    }

    public function getHeaderText()
    {
        return Mage::helper("gene_bluefoot")->__("Create Taxonomy");
    }

}