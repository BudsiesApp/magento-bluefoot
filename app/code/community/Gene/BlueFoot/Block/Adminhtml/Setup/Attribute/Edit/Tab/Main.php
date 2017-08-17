<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit_Tab_Main
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit_Tab_Main
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
        $return = parent::_prepareLayout();
        $renderer = $this->getLayout()->getBlock('fieldset_element_renderer');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            Varien_Data_Form::setFieldsetElementRenderer($renderer);
        }
        return $return;
    }
    
    protected function _prepareForm()
    {
        $helper = Mage::helper('gene_bluefoot');

        $attributeObject = $this->getAttributeObject();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>$helper->__('Attribute Properties'))
        );
        if ($attributeObject->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $validateClass = sprintf('validate-code validate-length maximum-length-%d',
            Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH);
        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => $helper->__('Attribute Code'),
            'title' => $helper->__('Attribute Code'),
            'note'  => $helper->__('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less then %s symbols', Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
        ));

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE => $helper->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => $helper->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => $helper->__('Global'),
        );

        $fieldset->addField('is_global', 'select', array(
            'name' => 'is_global',
            'label' => $helper->__('Scope'),
            'title' => $helper->__('Scope'),
            'note' => $helper->__('Declare attribute value saving scope'),
            'values' => $scopes
        ), 'attribute_code');

        $fieldset->addField('note', 'textarea', array(
            'name' => 'note',
            'label' => $helper->__('Note'),
            'title' => $helper->__('Note'),
            'note' => $helper->__('Text to appear below the input.'),
        ), 'position');

        $fieldset->addField('widget', 'select', array(
            'name' => 'widget',
            'label' => $helper->__('Widget'),
            'title' => $helper->__('Widget'),
            'value' => 'text',
            'values'=> Mage::getModel('gene_bluefoot/stage_widget_source')->toOptionArray(),
            'note' => $helper->__('A widget further enhances fields to provide further functionality.')
        ));

        $baseInputTypes = array(
            array('label' => 'Basic Fields', 'value' => Mage::getModel('eav/adminhtml_system_config_source_inputtype')->toOptionArray()),
            /*array('label' => 'Gene CMS', 'value' => array(
                array('label' => 'Select Content', 'value' => 'ecms_select'),
                array('label' => 'Mulitple Select Content', 'value' => 'ecms_select_multi'),
                array('label' => 'Content List', 'value' => 'ecms_entity_list'),
            ))*/
        );
        $additionalTypes = $this->_getAdditionalTypes();
        $inputTypes = array_merge($baseInputTypes, $additionalTypes);

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => $helper->__('Input Type'),
            'title' => $helper->__('Input Type'),
            'value' => 'text',
            'values'=> $inputTypes
        ));

        $contentTypes = array();
        $contentTypesCollection = Mage::getResourceModel('gene_bluefoot/type_collection');
        $contentTypesCollection->addContentTypeFilter('content');

        foreach ($contentTypesCollection as $contentType){
            $contentTypes[] = array('label' => $contentType->getName() . ' ['.$contentType->getIdentifier().']', 'value' => $contentType->getId());
        }

        $blockTypes = array();
        $blockTypesCollection = Mage::getResourceModel('gene_bluefoot/type_collection');
        $blockTypesCollection->addContentTypeFilter('block');

        foreach ($blockTypesCollection as $blockType){
            $blockTypes[] = array('label' => $blockType->getName() . ' ['.$blockType->getIdentifier().']', 'value' => $blockType->getId());
        }

        $additionalData = $attributeObject->getAdditional();

        $allowedCmsTypes = (isset($additionalData['entity_list_type']) ? $additionalData['entity_list_type'] : array());
        $allowedBlockType = (isset($additionalData['entity_allowed_block_type']) ? $additionalData['entity_allowed_block_type'] : false);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();


        $fieldset->addField('entity_allowed_content_types', 'multiselect', array(
            'name' =>  'additional[entity_allowed_content_types]',
            'label' => 'Content Types',
            'note' => $helper->__('Allowed Content types used for input. Leave empty for all types.'),
            'values' => $contentTypes,
            'value' => $allowedCmsTypes
        ));

        $fieldset->addField('entity_allowed_block_type', 'select', array(
            'name' =>  'additional[entity_allowed_block_type]',
            'label' => 'Block Types',
            'note' => $helper->__('Allowed Content Blocks used for input. Leave empty for all types.'),
            'values' => $blockTypes,
            'value' => $allowedBlockType
        ));


        $fieldset->addField('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => $helper->__('Default Value'),
            'title' => $helper->__('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

        $fieldset->addField('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => $helper->__('Default Value'),
            'title' => $helper->__('Default Value'),
            'values' => $yesno,
            'value' => $attributeObject->getDefaultValue(),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('default_value_date', 'date', array(
            'name'   => 'default_value_date',
            'label'  => $helper->__('Default Value'),
            'title'  => $helper->__('Default Value'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'value'  => $attributeObject->getDefaultValue(),
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => $helper->__('Default Value'),
            'title' => $helper->__('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'title' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => $helper->__('Unique Value'),
            'title' => $helper->__('Unique Value (not shared with other products)'),
            'note'  => $helper->__('Not shared with other products'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => $helper->__('Values Required'),
            'title' => $helper->__('Values Required'),
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => $helper->__('Input Validation for Store Owner'),
            'title' => $helper->__('Input Validation for Store Owner'),
            'values'=> Mage::helper('eav')->getFrontendClasses($attributeObject->getEntityType()->getEntityTypeCode())
        ));

        /*$fieldset->addField('display_in_grid', 'select', array(
            'name' => 'display_in_grid',
            'label' => $helper->__('Display in Grid'),
            'title' => $helper->__('Display in Grid'),
            'note'  => $helper->__('Should this attribute be shown within the grid of entities?'),
            'values' => $yesno,
        ));*/

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $this->setForm($form);
        
        return $this;
        
    }

    protected function _getAdditionalTypes()
    {
        $helper = Mage::helper('gene_bluefoot');

        $additionalTypes = array(

            /*array('label' => 'Gene CMS', 'value' => array(
                array('label' => 'Select Content', 'value' => 'ecms_select'),
                array('label' => 'Mulitple Select Content', 'value' => 'ecms_select_multi'),
                array('label' => 'Content List', 'value' => 'ecms_entity_list'),
            )),*/

            array(
            'label' => 'Media Types',
            'value' => array(
                array('value' => 'image', 'label' => $helper->__('Image')),
                //array('value' => 'media_gallery', 'label' => $helper->__('Media Gallery'))
            )),
            array(
                'label' => 'Entities',
                'value' => array(
                    array('value' => 'catalog_product', 'label' => $helper->__('Catalog Product')),
                    array('value' => 'catalog_category', 'label' => $helper->__('Catalog Category')),
                )
            ),
            array(
                'label' => 'BlueFoot',
                'value' => array(
                    array('value' => 'entity_list', 'label' => $helper->__('Entity Select')),
                    array('value' => 'child_entity', 'label' => $helper->__('Child Entity')),
                )
            ),
            array(
                'label' => 'Other',
                'value' => array(
                    array('value' => 'file', 'label' => $helper->__('File'))
                )
            )
        );

        return $additionalTypes;
    }

    protected function _initFormValues()
    {
        parent::_initFormValues();
        $attribute = $this->getAttributeObject();
        if ($attribute->getId() && $attribute->getValidateRules()) {
            $this->getForm()->addValues($attribute->getValidateRules());
        }
        $result = parent::_initFormValues();

        // get data using methods to apply scope
        $formValues = $this->getAttributeObject()->getData();
        foreach (array_keys($formValues) as $idx) {
            $formValues[$idx] = $this->getAttributeObject()->getDataUsingMethod($idx);
        }
        $this->getForm()->addValues($formValues);

        return $result;
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('gene_bluefoot')->__('Properties');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('gene_bluefoot')->__('Properties');
    }
    
    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
 
}
