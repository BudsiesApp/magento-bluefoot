<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_General
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_app_general", array("legend" => Mage::helper("gene_bluefoot")->__("App Information"), 'class' => 'fieldset-wide'));

        $currentApp = Mage::registry("current_app");

        $menuPositionValues = Mage::getSingleton('gene_bluefoot/app_attribute_source_menuposition')->getAllOptions();


        if ($currentApp->getId()) {
            $fieldset->addField('app_id', 'hidden', array(
                'name' => 'app_id',
            ));
        }

        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "title",
        ));

        $fieldset->addField("menu_position", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Menu Position"),
            "name" => "menu_position",
            'values' => $menuPositionValues,
            'note' => 'Frontend menu position'
        ));

        $fieldset->addField("internal_description", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Internal Description"),
            "name" => "internal_description",
            'note' => 'Only used within Magento Admin'
        ));

        $wysiwygConf = Mage::getSingleton('cms/wysiwyg_config');

        $fieldset->addField("description", "editor", array(
            "label" => Mage::helper("gene_bluefoot")->__("Description"),
            'wysiwyg' => true,
            'config' => $wysiwygConf,
            "name" => "description",
        ));


        $fieldset->addField("url_prefix", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Base URL"),
            "name" => "url_prefix",
            "class" => "validate-identifier validate-length maximum-length-50",
            'note' => '<small>Must be all lower case. Must contain no spaces and have a maximum length of 50 characters </small>',
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
