<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form_Group_Form
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form_Group_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset("gene_cms_group_form", array("legend" => Mage::helper("gene_bluefoot")->__("Add New Group")));

        $fieldset->addField("name", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "name",
        ));

        $fieldset->addField("code", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Code"),
            "class" => "required-entry",
            "required" => true,
            "name" => "code",
            "note" => Mage::helper('gene_bluefoot')->__('Lowercase version of the name')
        ));

        $fieldset->addField("icon", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Icon"),
            "class" => "required-entry",
            "required" => true,
            "name" => "icon",
            'note'  => Mage::helper('gene_bluefoot')->__('The class name for the font awesome icon to be displayed within the page builder.<br /><a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Click here to view icons (opens new tab)</a>'),
        ));

        $fieldset->addField("sort_order", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Sort Order"),
            "name" => "sort_order"
        ));

        $fieldset->addField("group_submit", "button", array(
            "value" => Mage::helper("gene_bluefoot")->__("Submit"),
            "name" => "group_submit"
        ));

        return parent::_prepareForm();
    }

}
