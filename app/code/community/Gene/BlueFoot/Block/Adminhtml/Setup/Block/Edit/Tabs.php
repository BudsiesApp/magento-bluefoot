<?php
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("type_tabs");
        $this->setDestElementId("edit_form");
    }

    protected function _beforeToHtml()
    {
        $currentType = Mage::registry('type_data');

        $this->addTab("form_section", array(
            "label" => Mage::helper("gene_bluefoot")->__("Type"),
            "title" => Mage::helper("gene_bluefoot")->__("Type"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_block_edit_tab_form")->toHtml(),
        ));
        
        $this->addTab("fields_section", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content Fields"),
            "title" => Mage::helper("gene_bluefoot")->__("Content Fields"),
            //"content" => (($currentType->getId()) ? $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_attribute_set_main')->toHtml() : 'Please save Block first before adding fields')
            "content" => $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_attribute_set_main')->setContentType('block')->toHtml()
        ));
        
        $this->addTab("design_section", array(
            "label" => Mage::helper("gene_bluefoot")->__("Design, Themes & Templates"),
            "title" => Mage::helper("gene_bluefoot")->__("Design, Themes & Templates"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_block_edit_tab_design")->toHtml(),
        ));
        
        
        return parent::_beforeToHtml();
    }

}
