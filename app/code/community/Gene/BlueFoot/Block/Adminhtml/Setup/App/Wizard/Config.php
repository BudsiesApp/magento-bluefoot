<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Config
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Config extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                "id" => "edit_form",
                "action" => $this->getUrl("*/*/saveStep", array("id" => $this->getRequest()->getParam("app_id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_app_general", array("legend" => Mage::helper("gene_bluefoot")->__("General Information"), 'class' => 'fieldset-wide'));


        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Name"),
//            "class" => "required-entry",
//            "required" => true,
            "name" => "title",
        ));



        return parent::_prepareForm();
    }
}