<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "gene_bluefoot";
        $this->_controller = "adminhtml_setup_app";
        
        $this->_updateButton("save", "label", Mage::helper("gene_bluefoot")->__("Save"));
        $this->_updateButton("delete", "label", Mage::helper("gene_bluefoot")->__("Delete"));

        $this->_addButton("saveandcontinue", array(
            "label" => Mage::helper("gene_bluefoot")->__("Save and Continue Edit"),
            "onclick" => "saveAndContinueEdit()",
            "class" => "save",
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry("current_app") && Mage::registry("current_app")->getId()) {
            $app = Mage::registry("current_app");
            return Mage::helper("gene_bluefoot")->__("Edit Content App: '%s'", $this->htmlEscape($app->getTitle() . ''));
        } else {
            return Mage::helper("gene_bluefoot")->__("Add Content App");
        }
    }

}