<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        // Build up our groups
        $groups = Mage::getResourceModel('gene_bluefoot/type_group_collection');

        $fieldset = $form->addFieldset("gene_cms_form", array("legend" => Mage::helper("gene_bluefoot")->__("General Information")));

        //hidden field to store attribute data
        $fieldset->addField('sets_json', 'hidden', array(
            'name' => 'sets_json'
        ));

        $fieldset->addField("name", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "name",
        ));

        $fieldset->addField("identifier", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Identifier"),
            "class" => "validate-code validate-length maximum-length-30 required-entry",
            "required" => true,
            "name" => "identifier",
            "note" => 'Unique system identifier.<br/> Must be all lower case. Must contain no spaces and contain only letters (a-z), numbers (0-9) or underscore(_).',
        ));

        $fieldset->addType('bluefoot_group', 'Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form_Renderer_Group');
        $fieldset->addField("group_id", "bluefoot_group", array(
            "label" => Mage::helper("gene_bluefoot")->__("Group"),
            "name" => "group_id",
            "options" => $groups->toOptionHash(),
            "note" => Mage::helper("gene_bluefoot")->__("The group this content block will be displayed in within the page builder.")
        ));
        
        $fieldset->addField("description", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Description"),
            "class" => "required-entry",
            "required" => true,
            "name" => "description",
        ));

        $fieldset->addField("sort_order", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Sort Order"),
            "class" => "required-entry",
            "required" => true,
            "name" => "sort_order",
        ));


        $fieldset = $form->addFieldset("gene_cms_form_pagebuilder", array("legend" => Mage::helper("gene_bluefoot")->__("Page Builder")));

        $fieldset->addField('show_in_page_builder', 'select', array(
            'name' => 'show_in_page_builder',
            'label' => Mage::helper('gene_bluefoot')->__('Use In Page Builder'),
            'title' => Mage::helper('gene_bluefoot')->__('Use In Page Builder'),
            'note'  => Mage::helper('gene_bluefoot')->__('Should this block be available within the page builder?'),
            'values' => $yesno,
            'value' => 1
        ));

        $fieldset->addField("icon_class", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Icon Class"),
            "class" => "required-entry",
            "required" => true,
            "name" => "icon_class",
            'note'  => Mage::helper('gene_bluefoot')->__('The class name for the font awesome icon to be displayed within the page builder.<br /><a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Click here to view icons (opens new tab)</a>'),
        ));

        /*
         * Colour is no longer used in the system
         *
         * $fieldset->addField("color", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Color"),
            "class" => "required-entry",
            "required" => true,
            "name" => "color",
            'note'  => Mage::helper('gene_bluefoot')->__('The hex color code for this content type, this is used to display the content within the view. Must be darker then light grey.'),
        ));*/

        if (Mage::getSingleton("adminhtml/session")->getTypeData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getTypeData());
            Mage::getSingleton("adminhtml/session")->setTypeData(null);
        } elseif (Mage::registry("type_data")) {
            $form->addValues(Mage::registry("type_data")->getData());
        }
        return parent::_prepareForm();
    }

}
