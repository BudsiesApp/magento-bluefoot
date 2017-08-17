<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Export
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Export extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_installer_export_settings", array("legend" => Mage::helper("gene_bluefoot")->__("Export Settings")));


        $fieldset->addField('json_file', 'text', array(
            'label'  => 'Export File Name',
            'name'  => 'json_file_name',
            'value'  => 'bluefoot_installer',
            'class' => 'validate-data',
            'note' => 'file extension (.json) will be added automatically',
        ));

        return parent::_prepareForm();
    }



}