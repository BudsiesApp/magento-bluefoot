<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Tab_Dynamic
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Tab_Dynamic extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_ignoreAttributes = array();

    protected $_groupId = null;
    protected $_group = null;
    
    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_renderer_fieldset_element')
        );

        return $return;
    }

    public function setGroup($group)
    {
        $this->_groupId = $group->getId();
        $this->_group = $group;
    }
    
    protected function _getAllAttributesCollection()
    {
        $attributes = array();
        $entity = Mage::getModel('gene_bluefoot/entity');

        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection');
                                     
        return $collection;
    }

    protected function _getGroupId()
    {
        return $this->_groupId;
    }

    protected function _getAssociatedAttributes()
    {
        $currentAttributeSet = Mage::registry('attribute_set');
        $currentAttributeSetId = $currentAttributeSet->getId();
        /*@var $currentAttributeSet Mage_Eav_Model_Entity_Attribute_Set */
        
        if(!$currentAttributeSet->getId()){
            return array();
        }
                
        $entity = Mage::getModel('gene_bluefoot/entity');
        
        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter()
                ->setAttributeGroupFilter($this->_getGroupId())
                ->setAttributeSetFilter($currentAttributeSetId)
                ->addSetInfo();        
        
        $attributes = array();

        foreach($collection as $key => $attribute){
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }
        return $attributes;
    }
    
    protected function _prepareForm()
    {
        $attributes = $this->_getAssociatedAttributes();

        $mediaGalleries = array();
        
        foreach($attributes as $key => $attribute){
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */                        
            $attribute->unsIsVisible();
            
            if(in_array($attribute->getCode(), $this->_ignoreAttributes)){
                unset($attributes[$key]);
            }
            
        }

        $model = Mage::registry('entity');
        
        $form = new Varien_Data_Form();
        $form->setDataObject($model);
        $form->setHtmlIdPrefix('genecms_');
        
        $fieldset = $form->addFieldset('base_fieldset', array (
                'legend' => Mage::helper('gene_bluefoot')->__('Attributes'),
                'class' => 'fieldset-wide' ));
        
        if ($model->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', array (
                    'name' => 'entity_id' ));
        }                
        
        //$disableAutoGroupChangeAttributeName = 'disable_auto_group_change';
        $this->_setFieldset($attributes, $fieldset, array());

        foreach($mediaGalleries as $gallery){
            $fieldset = $form->addFieldset('gallery_fieldset_' . $gallery->getAttributeCode(), array (
                'legend' => Mage::helper('gene_bluefoot')->__('Gallery: ' . $gallery->getFrontendLabel()),
                'class' => 'fieldset-wide' ));

            $this->_setFieldset(array($gallery), $fieldset);
        }
        
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_image'),
            'gallery'     => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_gallery'),
            'media_image'     => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_image'),
            'media_gallery'     => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_gallery'),
            'boolean'   => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_boolean'),
            'textarea' => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_wysiwyg'),
            'entity_list' => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_entity_list'),
            'child_entity' => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_entity_child'),
        );
    }
}
