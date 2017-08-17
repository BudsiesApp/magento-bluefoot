<?php

/**
 * Class Gene_BlueFoot_Model_Entity
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Entity extends Gene_BlueFoot_Model_Entity_Abstract
{
    const ENTITY                 = 'gene_bluefoot_entity';

    protected $_eventPrefix      = 'gene_bluefoot_entity';

    protected $_frontend;

    protected $_urlModel;

    protected $_contentApp;

    protected function _construct()
    {
        $this->_init('gene_bluefoot/entity');
    }

    /**
     * @return Gene_BlueFoot_Model_App
     * @throws Exception
     */
    public function getContentApp()
    {
        if(is_null($this->_contentApp)){
            $this->_contentApp = $this->getContentType()->getContentApp();
        }
        return $this->_contentApp;
    }

    /**
     * @return Gene_BlueFoot_Model_Type
     */
    public function getContentType()
    {
        if($attrSetId = $this->getAttributeSetId()){
            //we load the type by the entity attribute set id. 1to1 relationship
            $type = Mage::getModel('gene_bluefoot/type')->load($attrSetId, 'attribute_set_id');
            return $type;
        }

        throw new Exception('No known entity type exists for this entity');
    }

    public function getTaxonomyTermIds()
    {
        if (! $this->hasData('taxonomy_term_ids')) {
            $ids = $this->_getResource()->getTaxonomyTermIds($this);
            $this->setData('taxonomy_term_ids', $ids);
        }

        return (array) $this->_getData('taxonomy_term_ids');
    }

    /**
     * @return Gene_BlueFoot_Model_Entity_Frontend_Abstract
     */
    public function getFrontend()
    {
        $type = $this->getContentType();
        $contentType = $type->getContentType() ? $type->getContentType() : 'block';
        if(!$this->_frontend){
            $this->_frontend = Mage::getModel('gene_bluefoot/entity_frontend_'. $contentType);
            $this->_frontend->setEntity($this);
        }

        return $this->_frontend;
    }

    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
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

    /**
     * @return mixed
     */
    public function getEntityUrl(array $params = array())
    {
        return $this->getUrlModel()->getEntityUrl($this, $params);
    }

    /**
     * @return mixed
     */
    public function getRequestPath()
    {
        if (!$this->_getData('request_path')) {
            $this->getEntityUrl();
        }
        return $this->_getData('request_path');
    }
}
