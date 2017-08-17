<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Block
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Block extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
    }


    protected function _prepareForm()
    {

        $this->setChild('form_after', $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_installer_create_tab_block_types'));

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_installer_block_settings", array("legend" => Mage::helper("gene_bluefoot")->__("Settings")));

        $fieldset->addField('content_block_settings', 'checkbox', array(
            'label'     => Mage::helper('gene_bluefoot')->__('Export associated attributes'),
            'name'      => 'settings[blocks][include_block_attributes]',
            'value'  => 1,
            'checked' => 1,
            'note' => 'Export all associated attributes with content block(s)',
            'tabindex' => 1
        ));

        return parent::_prepareForm();

    }



}