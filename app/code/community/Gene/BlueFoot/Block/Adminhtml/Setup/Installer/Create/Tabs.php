<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tabs
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tabs extends Gene_BlueFoot_Block_Adminhtml_Common_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("new_installer_tabs");
        $this->setDestElementId("edit_form");
    }

    protected function _beforeToHtml()
    {
        $currentType = Mage::registry('type_data');


        $this->addTab("content_blocks", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content Blocks"),
            "title" => Mage::helper("gene_bluefoot")->__("Content Blocks"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_installer_create_tab_block")->toHtml(),
        ));

        $this->addTab("content_apps", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content Apps"),
            "title" => Mage::helper("gene_bluefoot")->__("Content Apps"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_installer_create_tab_app")->toHtml(),
        ));

//        $this->addTab("content_attributes", array(
//            "label" => Mage::helper("gene_bluefoot")->__("Content Attributes"),
//            "title" => Mage::helper("gene_bluefoot")->__("Content Attributes"),
//            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_installer_create_tab_attribute")->toHtml(),
//        ));

        $this->addTab("export_settings", array(
            "label" => Mage::helper("gene_bluefoot")->__("Export Settings"),
            "title" => Mage::helper("gene_bluefoot")->__("Export Settings"),
            "content" => $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_installer_create_tab_export")->toHtml(),
        ));


        $this->setActiveTab('content_apps');
        
        return parent::_beforeToHtml();
    }

}
