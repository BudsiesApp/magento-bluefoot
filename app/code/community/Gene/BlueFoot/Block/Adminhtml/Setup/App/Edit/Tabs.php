<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tabs
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("app_tabs");
        $this->setDestElementId("edit_form");
    }

    protected function _beforeToHtml()
    {
        $currentApp = Mage::registry('current_app');
        $activeTab = 'form_'.Mage::registry('active_tab');

        $this->addTab("form_general", array(
            "label" => Mage::helper("gene_bluefoot")->__("General"),
            "title" => Mage::helper("gene_bluefoot")->__("General"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_app_edit_tab_general")->toHtml(),

        ));

        $this->addTab("form_design", array(
            "label" => Mage::helper("gene_bluefoot")->__("Design"),
            "title" => Mage::helper("gene_bluefoot")->__("Design"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_app_edit_tab_design")->toHtml(),
        ));

        $this->addTab("form_meta", array(
            "label" => Mage::helper("gene_bluefoot")->__("Meta"),
            "title" => Mage::helper("gene_bluefoot")->__("Meta"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_app_edit_tab_meta")->toHtml(),

        ));

        $this->addTab("form_content_types", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content Types"),
            "title" => Mage::helper("gene_bluefoot")->__("Content Types"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_app_edit_tab_content")->toHtml(),
        ));

        $this->addTab("form_taxonomies", array(
            "label" => Mage::helper("gene_bluefoot")->__("Categorisation/Taxonomies"),
            "title" => Mage::helper("gene_bluefoot")->__("Categorisation/Taxonomies"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_app_edit_tab_taxonomy")->toHtml(),
        ));

        $this->setActiveTab($activeTab);

        return parent::_beforeToHtml();
    }

}
