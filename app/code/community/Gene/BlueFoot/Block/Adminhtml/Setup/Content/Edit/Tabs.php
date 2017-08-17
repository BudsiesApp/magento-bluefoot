<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit_Tabs
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
            "label" => Mage::helper("gene_bluefoot")->__("Type &amp; Entity Setup"),
            "title" => Mage::helper("gene_bluefoot")->__("Type &amp; Entity Setup"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_content_edit_tab_main")->toHtml(),
        ));
        
        $this->addTab("fields_section", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content Fields"),
            "title" => Mage::helper("gene_bluefoot")->__("Content Fields"),
            "content" => $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_attribute_set_main')->setContentType('content')->toHtml()
        ));
        
        /*$this->addTab("design_section", array(
            "label" => Mage::helper("gene_bluefoot")->__("Design, Themes & Templates"),
            "title" => Mage::helper("gene_bluefoot")->__("Design, Themes & Templates"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_block_edit_tab_design")->toHtml(),
        ));*/
        
        
        return parent::_beforeToHtml();
    }

}
