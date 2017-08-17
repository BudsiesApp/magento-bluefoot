<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit_Tab_Main
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Gene_BlueFoot_Model_Type
     */
    protected function _getCurrentType()
    {
        return Mage::registry("type_data");;
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $currentType = $this->_getCurrentType();
        $currentApp = $currentType->getContentApp();

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

        $fieldset = $form->addFieldset("gene_cms_form", array("legend" => Mage::helper("gene_bluefoot")->__("General Information")));

        if(!$currentType->getId() && $currentApp->getId()){
            $fieldset->addField('app_id', 'hidden', array(
                'name' => 'app_id'
            ));
        }


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

        
        $fieldset->addField("description", "textarea", array(
            "label" => Mage::helper("gene_bluefoot")->__("Description"),
            "class" => "required-entry",
            "required" => true,
            "name" => "description",
        ));




        //Entity Setup
        $fieldset = $form->addFieldset("gene_cms_form_entity_setup", array("legend" => Mage::helper("gene_bluefoot")->__("Entity Setup")));
        $fieldset->addField("singular_name", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Singular Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "singular_name",
        ));

        $fieldset->addField("plural_name", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Plural Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "plural_name",
        ));

        $fieldset->addField("entity_url_prefix", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content URL prefix"),
            "name" => "entity_url_prefix",
            "class" => "validate-identifier validate-length maximum-length-50",
            'note' => 'Must be all lower case. Must contain no spaces and have a maximum length of 50 characters',
        ));

        //TODO - add functionality
//        $fieldset->addField('searchable', 'select', array(
//            'name' => 'searchable',
//            'label' => Mage::helper('gene_bluefoot')->__('Searchable'),
//            'title' => Mage::helper('gene_bluefoot')->__('Searchable'),
//            'note'  => Mage::helper('gene_bluefoot')->__('Defines if the content is visible in the site search'),
//            'values' => $yesno,
//        ));
//
//        $fieldset->addField('include_in_sitemap', 'select', array(
//            'name' => 'include_in_sitemap',
//            'label' => Mage::helper('gene_bluefoot')->__('Include in sitemap'),
//            'title' => Mage::helper('gene_bluefoot')->__('Include in sitemap'),
//            'note'  => Mage::helper('gene_bluefoot')->__('Defines if the content shows in the XML sitemap'),
//            'values' => $yesno,
//        ));

        //set some default values
        if (!$currentType->getId()) {
            $currentType->setData('searchable', '1');
            $currentType->setData('include_in_sitemap', '1');
        }

        if (Mage::getSingleton("adminhtml/session")->getTypeData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getTypeData());
            Mage::getSingleton("adminhtml/session")->setTypeData(null);
        } elseif ($currentType) {
            $form->setValues($currentType->getData());
        }
        return parent::_prepareForm();
    }

}
