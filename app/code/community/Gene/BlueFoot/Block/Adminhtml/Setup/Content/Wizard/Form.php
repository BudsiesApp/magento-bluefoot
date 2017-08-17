<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Wizard_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Wizard_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                "id" => "content_type_form",
                "action" => $this->getUrl("*/genecms_setup_appwizard/saveTypeData", array("id" => $this->getRequest()->getParam("id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

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
            "onchange" => 'typeNameChange(this.value); ',
            'after_element_html' => '<script type="text/javascript">
                function typeNameChange(value){
                    identifier = value.split(" ").join("_");
                    identifier = identifier.replace(/[^a-zA-Z0-9_]/g, "").split("__").join("").toLowerCase();
                    if($("identifier").getValue() == ""){$("identifier").setValue(identifier);}

                    if($("singular_name").getValue() == ""){
                        if(value.slice(-1) != "s"){
                            $("singular_name").setValue(value);
                        }else{
                            $("singular_name").setValue(value.substring(0, value.length -1));
                        }


                    }
                    if($("plural_name").getValue() == ""){
                        if(value.slice(-1) != "s"){
                            $("plural_name").setValue(value+ "s");
                        }else{
                            $("plural_name").setValue(value);
                        }
                    }
                }
            </script>'
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

        //todo: missing functionality
//        $fieldset->addField('searchable', 'select', array(
//            'name' => 'searchable',
//            'label' => Mage::helper('gene_bluefoot')->__('Searchable'),
//            'title' => Mage::helper('gene_bluefoot')->__('Searchable'),
//            'note'  => Mage::helper('gene_bluefoot')->__('Defines if the content is visible in the site search'),
//            'values' => $yesno,
//            'value' => 1
//        ));
//
//        $fieldset->addField('include_in_sitemap', 'select', array(
//            'name' => 'include_in_sitemap',
//            'label' => Mage::helper('gene_bluefoot')->__('Include in sitemap'),
//            'title' => Mage::helper('gene_bluefoot')->__('Include in sitemap'),
//            'note'  => Mage::helper('gene_bluefoot')->__('Defines if the content shows in the search engine sitemap'),
//            'values' => $yesno,
//            'value' => 1
//        ));


        $currentType = Mage::registry("type_data");


        if (Mage::getSingleton("adminhtml/session")->getTypeData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getTypeData());
            Mage::getSingleton("adminhtml/session")->setTypeData(null);
        } elseif ($currentType) {
            $form->setValues($currentType->getData());
        }
        return parent::_prepareForm();
    }

}
