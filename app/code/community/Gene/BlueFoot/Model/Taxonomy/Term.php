<?php

/**
 * Class Gene_BlueFoot_Model_Taxonomy_Term
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Taxonomy_Term extends Gene_BlueFoot_Model_Entity_Abstract
{
    const ENTITY                 = 'gene_bluefoot_taxonomy_term';

    protected $_eventPrefix      = 'gene_bluefoot_taxonomy_term';

    protected $_frontend;

    protected $_entityType = null;

    protected $_urlModel;

    protected $_contentApp;

    protected function _construct()
    {
        $this->_init('gene_bluefoot/taxonomy_term');
        $this->setAttributeSetId($this->getDefaultAttributeSet()->getId());
    }

    /**
     * Ensure the attribute is set to default if not set
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if(!$this->hasData('attribute_set_id')){
            $this->setAttributeSetId($this->getDefaultAttributeSet()->getId());
        }

        $parentId = $this->getParentId();
        if($parentId) {
            $parentTerm = Mage::getModel('gene_bluefoot/taxonomy_term')->load($parentId);

            $this->setPath($parentTerm->getPath().'/');
        }

        if($this->getId() && $this->dataHasChangedFor('parent_id')){
            $this->setFlagParentChange(true);
        }

        return parent::_beforeSave();
    }

    protected function _afterSave()
    {
        if($this->getFlagParentChange()){
            $parentId = $this->getParentId();
            $parent = Mage::getModel('gene_bluefoot/taxonomy_term')->load($parentId);
            $this->getResource()->changeParent($this, $parent);
        }
        return parent::_afterSave();
    }

    public function setTaxonomy(Varien_Object $taxonomy)
    {
        $this->setData('taxonomy', $taxonomy);
        $this->setData('taxonomy_id', $taxonomy->getId());

        return $this;
    }

    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {

        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType(self::ENTITY);
        }

        return $this->_entityType;
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy_Term|null
     */
    public function getParentTerm()
    {
        if (!$this->hasData('parent_term')) {
            if($this->getData('parent_id')){
                $this->setData('parent_term', Mage::getModel('gene_bluefoot/taxonomy_term')->load($this->getParentId()));
            }

        }
        return $this->_getData('parent_term');
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy
     */
    public function getTaxonomy()
    {
        if(!$this->getData('taxonomy')){
            $taxonomy = Mage::getModel('gene_bluefoot/taxonomy')->load($this->getTaxonomyId());
            $this->setData('taxonomy', $taxonomy);
        }

        return $this->getData('taxonomy');
    }

    public function getPathTitle($seperator = ' - ', $reversed = false)
    {
        $titleElements = $this->_buildPathTitle($this);
        if(!$reversed){
            $titleElements = array_reverse($titleElements);
        }

        $title = implode($seperator, $titleElements);
        return $title;
    }

    /**
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @return array
     */
    public function getPathElements(Gene_BlueFoot_Model_Taxonomy_Term $term = null)
    {
        if(!is_null($term)){
            $term = $this;
        }
        $elements = $this->_buildPathElements($term);

        return array_reverse($elements);
    }

    /**
     * @param $term
     * @param array $elements
     * @return array
     */
    protected function _buildPathElements($term, &$elements = array())
    {
        $elements[] = $term;
        if($parent = $term->getParentTerm()){
            $this->_buildPathTitle($parent, $elements);
        }

        return $elements;
    }

    /**
     * @param $term
     * @param array $titleElements
     * @return array
     */
    protected function _buildPathTitle($term, &$titleElements = array())
    {
        $titleElements[] = $term->getTitle();
        if($parent = $term->getParentTerm()){
            $this->_buildPathTitle($parent, $titleElements);
        }

        return $titleElements;
    }

    /**
     * Builds the URL from term and terms parents
     * @return string
     */
    public function getPathUrl()
    {
        $urlElements = $this->_buildPathUrl($this);
        $urlElements = array_reverse($urlElements);
        $url = implode('/', $urlElements);
        return $url;
    }


    /**
     * Recursive function to build up the URL
     * @param $term
     * @param array $urlElementsElements
     * @return array
     */
    protected function _buildPathUrl($term, &$urlElementsElements = array())
    {
        if($term->getUrlKey()) {
            $urlElementsElements[] = $term->getUrlKey();
        }
        if($parent = $term->getParentTerm()){
            $this->_buildPathUrl($parent, $urlElementsElements);
        }

        return $urlElementsElements;
    }

    /**
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * @return Gene_BlueFoot_Model_Url
     */
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getModel('gene_bluefoot/url');
        }
        return $this->_urlModel;
    }

    public function getTermUrl()
    {
        return $this->getUrlModel()->getTermUrl($this);
    }

    public function getRequestPath()
    {
        if (!$this->_getData('request_path')) {
            $this->getEntityUrl();
        }
        return $this->_getData('request_path');
    }

    public function getContentCollection()
    {
        $collection = Mage::getModel('gene_bluefoot/entity')->getCollection();
        $collection->addTermFilter($this);

        return $collection;
    }

    public function getContentIds()
    {
        $collection = $this->getContentCollection();
        return $collection->getAllIds();
    }

    /**
     * Prefix the taxonomy base url onto the term url
     * @return string
     */
    public function getFullUrlKey()
    {
        if(!$this->getUrlKey()){
            return;
        }
        $urlPrefix = '';
        if($this->getTaxonomy()->getTermUrlPrefix()){
            $urlPrefix = $this->getTaxonomy()->getTermUrlPrefix() . '/';
        }
        return $urlPrefix . $this->getPathUrl();
    }

    /**
     * @return Gene_BlueFoot_Model_App
     */
    public function getContentApp()
    {
        if(is_null($this->_contentApp)){
            $this->_contentApp = $this->getTaxonomy()->getContentApp();
        }
        return $this->_contentApp;
    }

    /**
     * @param null $parentId
     * @return array
     */
    public function getChildIds($parentId = null)
    {
        if(is_null($parentId)){
            $parentId = $this->getId();
            if(!$this->getData('child_ids')){
                $this->setData('child_ids', $this->getResource()->getChildIds($parentId));
            }
            return $this->getData('child_ids');

        }

        return $this->getResource()->getChildIds($parentId);
    }

    /**
     * @param null $parentId
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection
     */
    public function getChildTerms($parentId = null)
    {
        if(is_null($parentId)) {
            $parentId = $this->getId();
        }
        $childIds = $this->getChildIds($parentId);
        $collection = $this->getCollection()->addFieldToFilter('entity_id', array('in' => $childIds));

        return $collection;
    }

    public function getChildCount()
    {
        if(!$this->getData('children_count')){
            $childCount = $this->getResource()->calculateChildrenCount($this);
            $this->setData('children_count', $childCount);
        }

        return $this->getData('children_count');
    }

}