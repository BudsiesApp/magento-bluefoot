<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Edit_Tab_Dynamic
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Edit_Tab_Dynamic extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_ignoreAttributes = array();

    protected $_groupId = null;

    /**
     * @var Mage_Eav_Model_Entity_Attribute_Group
     */
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

    public function setGroup(Varien_Object $group)
    {
        $this->_groupId = $group->getId();
        $this->_group = $group;
    }
    
    protected function _getAllAttributesCollection()
    {
        $collection = Mage::getResourceModel('gene_bluefoot/taxonomy_term_attribute_collection');
        return $collection;
    }

    protected function _getGroupId()
    {
        return $this->_groupId;
    }

    protected function _getAssociatedAttributes()
    {
        $term = Mage::registry('current_term');
        $taxonomy = $term->getTaxonomy();

        $currentAttributeSet = $taxonomy->getAttributeSet();
        $currentAttributeSetId = $taxonomy->getAttributeSetId();
        /*@var $currentAttributeSet Mage_Eav_Model_Entity_Attribute_Set */
        
        if(!$currentAttributeSet->getId()){
            return array();
        }
                
        $entity = Mage::getModel('gene_bluefoot/taxonomy_term');
        
        $collection = Mage::getResourceModel('gene_bluefoot/taxonomy_term_attribute_collection')
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
        $term = Mage::registry('current_term');
        /**
         * @var $term Gene_BlueFoot_Model_Taxonomy_Term
         */
        $taxonomy = $term->getTaxonomy();
        /**
         * @var $taxonomy Gene_BlueFoot_Model_Taxonomy
         */

        $attributes = $this->_getAssociatedAttributes();
        
        foreach($attributes as $key => $attribute){
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */                        
            $attribute->unsIsVisible();
            
            if(in_array($attribute->getCode(), $this->_ignoreAttributes)){
                unset($attributes[$key]);
            }
        }
        
        $form = new Varien_Data_Form();
        $form->setDataObject($term);
        $form->setHtmlIdPrefix('genecms_');
        
        $fieldset = $form->addFieldset('base_fieldset_' . $this->_groupId, array (
                'legend' => Mage::helper('gene_bluefoot')->__('Attributes'),
                'class' => 'fieldset-wide' ));
        
        if ($term->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', array (
                    'name' => 'entity_id' ));
        }                
        
        //$disableAutoGroupChangeAttributeName = 'disable_auto_group_change';
        $this->_setFieldset($attributes, $fieldset, array());

        if($this->getIsFirst()){

            $currentId = $term->getId();
            $taxonomyTerms = $taxonomy->getTerms('*', array($currentId));
            $parentOptions = array(
                0 => 'None'
            );
            foreach($taxonomyTerms as $taxonomyTerm){
                if($taxonomyTerm->getId() == $term->getId()){
                    continue;
                }

                $termPath = $taxonomyTerm->getPath();
                $pathParts = explode('/', $termPath);
                if(in_array($term->getId(), $pathParts)){
                    continue;
                }

                $parentOptions[$taxonomyTerm->getId()] = $taxonomyTerm->getPathTitle(' > ');
            }

            if($taxonomy->getIsNestable()) {
                $fieldset->addField('parent_id', 'select', array(
                    'name' => 'parent_id',
                    'label' => 'Parent',
                    'options' => $parentOptions
                ), 'title');
            }

            $fieldset->addField('position', 'text', array(
                'name' => 'position',
                'label' => 'Sort Order',
                'class' => 'validate-number',
                'value' => 0
            ), 'title');

        }


        $form->addValues($term->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_boolean'),
            'textarea' => Mage::getConfig()->getBlockClassName('gene_bluefoot/adminhtml_attribute_form_element_wysiwyg'),
        );
    }
}
