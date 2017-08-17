<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Meta
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_app_meta", array("legend" => Mage::helper("gene_bluefoot")->__("Meta"), 'class' => 'fieldset-wide'));

        $currentApp = Mage::registry("current_app");


        $fieldset->addField("meta_title", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Page Title"),
            "name" => "meta_title",
        ));

        $fieldset->addField("meta_keywords", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Meta Keywords"),
            "name" => "meta_keywords",
        ));

        $fieldset->addField("meta_description", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Meta Description"),
            "name" => "meta_description",
        ));


        if (Mage::getSingleton("adminhtml/session")->getAppData()) {
            $form->addValues(Mage::getSingleton("adminhtml/session")->getAppData());
            Mage::getSingleton("adminhtml/session")->setAppData(null);
        } elseif (Mage::registry("current_app")) {
            $form->addValues(Mage::registry("current_app")->getData());
        }
        return parent::_prepareForm();
    }

}
