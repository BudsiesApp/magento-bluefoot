<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "taxonomy_id";
        $this->_blockGroup = "gene_bluefoot";
        $this->_controller = "adminhtml_setup_taxonomy";

        $this->_updateButton("save", "label", Mage::helper("gene_bluefoot")->__("Save"));
        $this->_updateButton("delete", "label", Mage::helper("gene_bluefoot")->__("Delete"));

        $appId = Mage::registry("current_taxonomy")->getAppId();
    }

    public function getHeaderText()
    {
        if (Mage::registry("current_taxonomy") && Mage::registry("current_taxonomy")->getId()) {
            return Mage::helper("gene_bluefoot")->__("Edit Taxonomy: '%s'", $this->htmlEscape(Mage::registry("current_taxonomy")->getTitle()));
        } else {
            return Mage::helper("gene_bluefoot")->__("Add Taxonomy");
        }
    }

    public function getBackUrl()
    {
        $appId = Mage::registry("current_taxonomy")->getAppId();
        if($appId){
            return $this->getUrl('*/genecms_setup_app/edit', array('id' => $appId, 'tab' => 'taxonomies'));
        }
        return $this->getUrl('*');
    }

}