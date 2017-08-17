<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Info
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Info extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Gene_BlueFoot_Model_App_Wizard
     */
    protected function _getCurrentWizard()
    {
        return Mage::registry('app_wizard');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                "id" => "edit_form",
                "action" => $this->getUrl("*/*/saveStep", array("id" => $this->getRequest()->getParam("app_id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $wizard = $this->_getCurrentWizard();
        $stepData = $wizard->getStepData('app_info');

        $form->setUseContainer(true);

        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_app_general", array("legend" => Mage::helper("gene_bluefoot")->__("App Information"), 'class' => 'fieldset-wide'));

        $fieldset->addField('step_id','hidden', array(
            'name' => 'step_id',
            'value' => 'app_info'
        ));


        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "title",
        ));

        $fieldset->addField("url_prefix", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Base URL"),
            "required" => true,
            "name" => "url_prefix",
            "class" => "required-entry validate-identifier validate-length maximum-length-50",
            'note' => 'Must be all lower case. Must contain no spaces and have a maximum length of 50 characters',
        ));

        $fieldset->addField("description", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Description"),
            "class" => "required-entry",
            "required" => true,
            "name" => "description",
        ));

        $form->addValues($stepData);

        return parent::_prepareForm();
    }
}