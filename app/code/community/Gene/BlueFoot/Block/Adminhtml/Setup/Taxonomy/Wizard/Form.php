<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Wizard_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Wizard_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $currentTaxonomy = Mage::registry("current_taxonomy");

        $form = new Varien_Data_Form(array(
                "id" => "taxonomy_form",
                "action" => $this->getUrl("*/*/saveTaxonomy", array("id" => $this->getRequest()->getParam("id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $form->setUseContainer(true);

        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("expertcms_taxonomy_form", array("legend" => Mage::helper("gene_bluefoot")->__("General information")));


        if ($currentTaxonomy && $currentTaxonomy->getId()) {
            $fieldset->addField('taxonomy_id', 'hidden', array(
                'name' => 'taxonomy_id',
                'value' => $currentTaxonomy->getId()
            ));
        }


        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "title",
        ));


        $fieldset->addField("term_url_prefix", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Term URL prefix"),
            "name" => "term_url_prefix",
            "class" => "validate-code validate-length maximum-length-50",
            'note' => 'Must be all lower case. Must contain no spaces and have a maximum length of 50 characters ',
        ));


        $fieldset->addField("description", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Description"),
            "class" => "required-entry",
            "required" => true,
            "name" => "description",
        ));

        $fieldset->addField('is_active', 'select', array(
            'name' => 'is_active',
            'label' => Mage::helper('gene_bluefoot')->__('Is Active'),
            'title' => Mage::helper('gene_bluefoot')->__('Is Active'),
            'values' => $yesno,
            'value' => 1
        ));

        $fieldset->addField('is_nestable', 'select', array(
            'name' => 'is_nestable',
            'label' => Mage::helper('gene_bluefoot')->__('Terms can be nested'),
            'title' => Mage::helper('gene_bluefoot')->__('Terms can be nested'),
            'values' => $yesno,
            'value' => 1
        ));

        if (Mage::getSingleton("adminhtml/session")->getTypeData()) {
            $form->addValues(Mage::getSingleton("adminhtml/session")->getTypeData());
            Mage::getSingleton("adminhtml/session")->setTypeData(null);
        } elseif ($currentTaxonomy) {
            $form->addValues(Mage::registry("current_taxonomy")->getData());
        } else {
            $form->setValues(array(
                'is_nestable' => 1,
                'is_active' => 1
            ));
        }
        return parent::_prepareForm();
    }
}