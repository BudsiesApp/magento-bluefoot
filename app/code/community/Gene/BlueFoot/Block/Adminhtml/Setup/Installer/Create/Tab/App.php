<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_App
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_App extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $this->setChild('form_after', $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_installer_create_tab_app_apps'));

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_installer_app_settings", array("legend" => Mage::helper("gene_bluefoot")->__("Settings")));


        $fieldset->addField('app_settings_types', 'checkbox', array(
            'label'     => Mage::helper('gene_bluefoot')->__('Export associated content types'),
            'name'      => 'settings[apps][include_content_types]',
            'value'  => 1,
            'checked' => 1,
            'after_element_html' => '<small>Export all associated content types with content app(s)</small>',
        ));

        $fieldset->addField('app_settings_attributes', 'checkbox', array(
            'label'     => Mage::helper('gene_bluefoot')->__('Export associated attributes'),
            'name'      => 'settings[apps][include_content_attributes]',
            'value'  => 1,
            'checked' => 1,
            'after_element_html' => '<small>Export all associated attributes with app content types</small>',
        ));

        $fieldset->addField('app_settings_taxonomies', 'checkbox', array(
            'label'     => Mage::helper('gene_bluefoot')->__('Export associated taxonomies'),
            'name'      => 'settings[apps][include_taxonomies]',
            'value'  => 1,
            'checked' => 1,
            'after_element_html' => '<small>Export all associated taxonomies with content app(s)</small>',
        ));



        return parent::_prepareForm();
    }

}