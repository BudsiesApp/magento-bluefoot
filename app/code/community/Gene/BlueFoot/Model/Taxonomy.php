<?php

/**
 * Class Gene_BlueFoot_Model_Taxonomy
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Taxonomy extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix      = 'gene_bluefoot_taxonomy';

    protected $_entityType;

    protected function _construct()
    {
        $this->_init("gene_bluefoot/taxonomy");
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $termOptions = $this->getTermDefaults();

        $this->setData('term_defaults_serialized', serialize($termOptions));
        return parent::_beforeSave();
    }

    protected function _afterLoad()
    {
        if($this->getTermDefaultsSerialized()){
            $termDefaults = @unserialize($this->getTermDefaultsSerialized());
            if(!is_array($termDefaults)){
                $termDefaults = array();
            }
            $this->setTermDefaults($termDefaults);
        }
        return parent::_afterLoad();
    }

    /**
     * @return array
     */
    public function getTermDefaults()
    {
        if(!is_array($this->getData('term_defaults'))){
            $this->setData('term_defaults', array());
        }

        return $this->getData('term_defaults');
    }

    /**
     * @return Varien_Object
     */
    public function getTermDefaultsAsObject()
    {
        $termDefaults = $this->getTermDefaults();
        $instance = new Varien_Object($termDefaults);

        return $instance;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Set|mixed
     */
    public function getAttributeSet()
    {
        if($this->hasData('attribute_set')){
            return $this->getData('attribute_set');
        }

        $setId = $this->getAttributeSetId();
        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSet->load($setId);

        $this->setData('attribute_set', $attributeSet);

        return $attributeSet;
    }

    /**
     * @return int
     */
    public function getAttributeSetId()
    {
        if($this->getData('attribute_set_id')){
            return $this->getData('attribute_set_id');
        }

        //get default term attribute set id
        return $this->getDefaultAttributeSetId();
    }

    /**
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSetId = $this->_getEntityType()->getDefaultAttributeSetId();

        return $attributeSetId;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function getDefaultAttributeSet()
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSetId = $this->_getEntityType()->getDefaultAttributeSetId();
        $attributeSet->load($attributeSetId);

        return $attributeSet;
    }

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    public function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY);
        }

        return $this->_entityType;
    }

    /**
     * @param array $ignoreIds
     * @return array
     */
    public function getTermIds(array $ignoreIds = array())
    {
        if($this->getId()) {
            $terms = Mage::getModel('gene_bluefoot/taxonomy_term')->getCollection();
            $terms->addFieldToFilter('taxonomy_id', $this->getId());

            if(count($ignoreIds)){
                //$terms->addFieldToFilter('term_id', array('nin' => $ignoreIds));
            }

            return  $terms->getAllIds();
        }

        return array();
    }

    /**
     * @param string $attributes
     * @param array $ignoreIds
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection
     * @throws Mage_Core_Exception
     */
    public function getTerms($attributes = "*", array $ignoreIds = array())
    {
        if(!$this->hasData('terms')) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : 0;

            if ($this->getId()) {
                $terms = Mage::getModel('gene_bluefoot/taxonomy_term')->getCollection();
                $terms->setStoreId($storeId);
                $terms->addFieldToFilter('taxonomy_id', $this->getId());
                $terms->setOrder('path', 'ASC');

                if (count($ignoreIds)) {
                    //$terms->addFieldToFilter('term_id', array('nin' => $ignoreIds));
                }

                $terms->addAttributeToSelect($attributes);

                $this->setData('terms', $terms);
            }
        }

        return $this->getData('terms');
    }

    public function getTermsAsTree()
    {

        $terms = $this->getTerms();

        $termTree = array();

        foreach($terms as $termId => $term){

            if($parentId = $term->getParentId()){
                if($parentItem = $terms->getItemById($parentId)){
                    $children = $parentItem->getChildren();
                    if(!$children || !is_array($children)){
                        $children = array();
                    }
                    $children[] = $term;
                    $parentItem->setData('children', $children);
                }

            }
        }

        return $terms;
    }

    /**
     * @return Gene_BlueFoot_Model_App
     */
    public function getContentApp()
    {
        if(!$this->getData('content_app')){

            $app = Mage::getModel('gene_bluefoot/app');
            $app->load($this->getAppId());

            $this->setData('content_app', $app);
        }

        return $this->getData('content_app');
    }


}