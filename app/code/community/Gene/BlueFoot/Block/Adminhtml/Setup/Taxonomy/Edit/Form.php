<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Edit_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $currentTaxonomy = Mage::registry("current_taxonomy");
        $currentApp = $currentTaxonomy->getContentApp();

        $form = new Varien_Data_Form(array(
                "id" => "edit_form",
                "action" => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $form->setUseContainer(true);

        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        if(!$currentApp->getId()){

            $appOptions = array();

            $apps = Mage::getModel('gene_bluefoot/app')->getCollection();
            foreach($apps as $app){
                $appOptions[$app->getId()] = $app->getTitle();
            }

            $fieldset = $form->addFieldset("gene_cms_content_type_app", array("legend" => Mage::helper("gene_bluefoot")->__("Content App")));

            $fieldset->addField('app_id', 'select', array(
                'name' => 'app_id',
                'label' => Mage::helper('gene_bluefoot')->__('App'),
                'title' => Mage::helper('gene_bluefoot')->__('App'),
                'note'  => Mage::helper('gene_bluefoot')->__('Defines the app associated with the content type'),
                'required' => true,
                'class' => 'required-entry',
                'values' => $appOptions,
            ));
        }


        $fieldset = $form->addFieldset("expertcms_taxonomy_form", array("legend" => Mage::helper("gene_bluefoot")->__("General information")));


        if ($currentTaxonomy && $currentTaxonomy->getId()) {
            $fieldset->addField('taxonomy_id', 'hidden', array(
                'name' => 'taxonomy_id',
                'value' => $currentTaxonomy->getId()
            ));
        }elseif($currentApp->getId()){
            $fieldset->addField('app_id', 'hidden', array(
                'name' => 'app_id',
                'value' => $currentApp->getId()
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
        ));

        $fieldset->addField('is_nestable', 'select', array(
            'name' => 'is_nestable',
            'label' => Mage::helper('gene_bluefoot')->__('Terms can be nested'),
            'title' => Mage::helper('gene_bluefoot')->__('Terms can be nested'),
            'values' => $yesno,
        ));

        if (!$currentTaxonomy->getId()) {
            $currentTaxonomy->setData('is_active', '1');
            $currentTaxonomy->setData('is_nestable', '1');
        }

        $termDefaults = $currentTaxonomy->getTermDefaultsAsObject();
        $fieldset = $form->addFieldset("expertcms_taxonomy_term_defaults_form", array("legend" => Mage::helper("gene_bluefoot")->__("Term Design")));

        $layoutOptions = Mage::getSingleton('gene_bluefoot/taxonomy_term_attribute_source_layout')->getAllOptions();
        $displayModeOptions = Mage::getSingleton('gene_bluefoot/taxonomy_term_attribute_source_displaymode')->getAllOptions();
        $columnTypeOptions = Mage::getSingleton('gene_bluefoot/taxonomy_term_attribute_source_columntype')->getAllOptions();
        $customDesignOptions = Mage::getSingleton('core/design_source_design')->getAllOptions();

        $fieldset->addField("default_page_layout", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Page Layout"),
            'values' => $layoutOptions,
            "name" => "term_defaults[page_layout]",
            'value' => $termDefaults->getPageLayout()
        ));

        $fieldset->addField("default_display_mode", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Display Mode"),
            'values' => $displayModeOptions,
            "name" => "term_defaults[display_mode]",
            'value' => $termDefaults->getDisplayMode()
        ));

        $fieldset->addField("default_column_type", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Column Type"),
            'values' => $columnTypeOptions,
            "name" => "term_defaults[column_type]",
            'value' => $termDefaults->getColumnType()
        ));

        $fieldset->addField("default_custom_design", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Custom Design"),
            'values' => $customDesignOptions,
            "name" => "term_defaults[custom_design]",
            'value' => $termDefaults->getCustomDesign()
        ));


        $fieldset->addField("default_custom_layout_update", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Custom Layout Update"),
            "name" => "term_defaults[custom_layout_update]",
            'value' => $termDefaults->getCustomLayoutUpdate()
        ));



        if (Mage::getSingleton("adminhtml/session")->getTypeData()) {
            $form->addValues(Mage::getSingleton("adminhtml/session")->getTypeData());
            Mage::getSingleton("adminhtml/session")->setTypeData(null);
        } elseif ($currentTaxonomy) {
            $form->addValues(Mage::registry("current_taxonomy")->getData());
        } else {
            $form->addValues(array(
                'is_nestable' => 1,
                'is_active' => 1
            ));
        }
        return parent::_prepareForm();
    }
}