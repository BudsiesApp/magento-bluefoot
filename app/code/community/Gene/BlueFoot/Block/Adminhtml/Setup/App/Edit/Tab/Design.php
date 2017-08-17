<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Design
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Gene_BlueFoot_Model_App
     */
    protected function _getCurrentApp()
    {
        return Mage::registry("current_app");
    }

    protected function _prepareForm()
    {
        $currentApp = $this->_getCurrentApp();
        $appViewOptions = $currentApp->getViewOptions();

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset("gene_cms_app_design_general", array("legend" => Mage::helper("gene_bluefoot")->__("Design Options"), 'class' => 'fieldset-wide'));

        //add custom multiselect
        $fieldset->addType('multiselect_extended','Gene_BlueFoot_Block_Adminhtml_Setup_Renderer_Multiselect');


        $displayModeOptions = Mage::helper('gene_bluefoot/app')->getDisplayModeArray();

        $columnOptions = array(
            'grid' => 'Grid',
            'list' => 'List'
        );

        $datetimeFields = Mage::helper('gene_bluefoot/app')->getDateTimeFields();
        $dateTimeOptions = array(
            'created_at' => 'Created at date/time'
        );
        foreach($datetimeFields as $dtField){
            $dateTimeOptions[$dtField->getAttributeCode()] = $dtField->getFrontend()->getLabel();
        }

        $layoutOptions = Mage::getSingleton('page/source_layout')->toOptionArray();


        $appContentTypes = $currentApp->getContentTypes();
        $contentTypeOptions[] = array(
            'label' => 'All',
            'value' => ''
        );
        foreach($appContentTypes as $cType){
            $contentTypeOptions[] = array(
                'value' => $cType->getIdentifier(),
                'label' => $cType->getName()
            );
            //$contentTypeOptions[$cType->getIdentifier()] = $cType->getName();
        }

        $fieldset->addField("page_layout", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Page Layout"),
            'values' => $layoutOptions,
            "name" => "page_layout",
        ));


        if (!$currentApp->getId() || !$currentApp->getPageLayout()) {
            $defaultLayout = Mage::getSingleton('page/source_layout')->getDefaultValue();
            foreach($layoutOptions as $lOption){
                if(is_array($lOption) && isset($lOption['value']) && $lOption['value'] == 'two_columns_right'){
                    $defaultLayout = 'two_columns_right';
                }
            }
            $currentApp->setPageLayout($defaultLayout);
        }

        $fieldset->addField("view_options_show_description", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Show Description"),
            "class" => "required-entry",
            'values' => $yesno,
            "required" => true,
            "name" => "view_options[show_description]",
            'value' => $appViewOptions->getData('show_description'),
        ));

        $fieldset->addField("display_mode", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Display Mode"),
            "class" => "required-entry",
            'values' => $displayModeOptions,
            "required" => true,
            "name" => "display_mode",
        ));

        $fieldset->addField("column_type", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Column Type"),
            "class" => "required-entry",
            'values' => $columnOptions,
            "required" => true,
            "name" => "view_options[column_type]",
            'value' => $appViewOptions->getData('column_type')
        ));

        $fieldset->addField("view_options_content_types", "multiselect", array(
            "label" => Mage::helper("gene_bluefoot")->__("Content Types"),
            'values' => $contentTypeOptions,
            "name" => "view_options[content_types]",
            'value' => $appViewOptions->getData('content_types'),
        ));

        $fieldset->addField("view_options_default_sort", "select", array(
            "label" => Mage::helper("gene_bluefoot")->__("Default sort"),
            "class" => "required-entry",
            'values' => $dateTimeOptions,
            "required" => true,
            'value' => $appViewOptions->getData('default_sort'),
            "name" => "view_options[default_sort]",
        ));

        $fieldset = $form->addFieldset("gene_cms_app_design_pagination", array("legend" => Mage::helper("gene_bluefoot")->__("Pagination")));


        $fieldset->addField("view_options_pagination_per_page", "text", array(
            "label" => Mage::helper("gene_bluefoot")->__("Pagination - number per page"),
            "class" => "required-entry",
            "required" => true,
            'value' => $appViewOptions->getData('pagination_per_page') ? $appViewOptions->getData('pagination_per_page') : 12,
            "name" => "view_options[pagination_per_page]",
        ));

//        $fieldset->addField("view_options_show_prev_next_links", "select", array(
//            "label" => Mage::helper("gene_bluefoot")->__("Show Previous/Next links"),
//            "class" => "required-entry",
//            "required" => true,
//            'values' => array(0 => 'no', 'above' => 'Above content', 'below' => 'Below content', 'above_below' => 'Above and below content'),
//            'value' => $appViewOptions->getData('show_prev_next_links') ? $appViewOptions->getData('show_prev_next_links') : 'below',
//            "name" => "view_options[show_prev_next_links]",
//        ));


        if (Mage::getSingleton("adminhtml/session")->getAppData()) {
            $form->addValues(Mage::getSingleton("adminhtml/session")->getAppData());
            Mage::getSingleton("adminhtml/session")->setAppData(null);
        } elseif ($currentApp) {
            $form->addValues($currentApp->getData());
        }
        return parent::_prepareForm();
    }

}
