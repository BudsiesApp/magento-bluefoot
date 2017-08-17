<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit_Tabs
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('gene_cms_attribute_tabs');
        $this->setDestElementId('edit_form');
    }
    
    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();

        $jsBlock = $this->getLayout()->createBlock('adminhtml/template');
        $jsBlock->setTemplate('gene/bluefoot/setup/attribute/js.phtml');

        $this->addTab(
            'main_section', 
            array(
                'label' => 'Properties',
                'title' => 'Properties',
                'content' => $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_attribute_edit_tab_main')->toHtml(),
                'active' => true,
            )
        );

        $this->addTab(
            'Attributes', 
            array(
                'label' => 'Manage Label/Options',
                'title' => 'Manage Label/Options',
                'content' => $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_attribute_edit_tab_options')->toHtml() . $jsBlock->toHtml(),
                'active' => false,
            )
        );
        
        return $return;
    }
}
