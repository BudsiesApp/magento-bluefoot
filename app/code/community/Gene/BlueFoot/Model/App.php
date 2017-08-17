<?php

/**
 * Class Gene_BlueFoot_Model_App
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_App extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix      = 'gene_bluefoot_app';

    protected $_urlModel;

    protected $_updateRelatedEntitiesOnSave = false;

    /**
     * @var Gene_BlueFoot_Model_App_Viewoptions
     */
    protected $_viewOptionsInstance;

    protected function _construct()
    {
        $this->_init("gene_bluefoot/app");
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $viewOptions = $this->getViewOptionInstance();
        $this->setData('view_options_serialized', $viewOptions->serializeForSave());
        return parent::_beforeSave();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if($this->_updateRelatedEntitiesOnSave){
            $this->saveRelatedEntities();
        }
        return parent::_afterSave();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        if($this->getViewOptionsSerialized()){
            $viewOptions = $this->getViewOptionInstance();
            $viewOptions->initSerializedData($this->getViewOptionsSerialized());
        }
        return parent::_afterLoad();
    }

    /**
     * @return mixed
     */
    public function getViewOptionsAsArray()
    {
        return $this->getViewOptionInstance()->getData();
    }

    /**
     * @return Gene_BlueFoot_Model_App_Viewoptions
     */
    public function getViewOptions()
    {
        return $this->getViewOptionInstance();
    }

    /**
     * @param $data
     * @return $this
     */
    public function setViewOptions($data)
    {
        $viewOptions = $this->getViewOptionInstance();
        $viewOptions->setData($data);

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setViewOption($key, $value)
    {
        $viewOptions = $this->getViewOptionInstance();
        $viewOptions->setData($key, $value);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addViewOptions(array $data)
    {
        $viewOptions = $this->getViewOptionInstance();
        $viewOptions->addData($data);

        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_App_Viewoptions
     */
    public function getViewOptionInstance()
    {
        if(is_null($this->_viewOptionsInstance)){
            $this->_viewOptionsInstance = Mage::getModel('gene_bluefoot/app_viewoptions');
            $this->_viewOptionsInstance->setData($this->_getData('view_options'));
        }
        return $this->_viewOptionsInstance;
    }

    /**
     * Flag to save related entities when saving main model
     *
     * @param bool $onSave
     * @return $this
     */
    public function updateRelatedEntitesOnSave($onSave = true)
    {
        $this->_updateRelatedEntitiesOnSave = (bool) $onSave;
        return $this;
    }

    /**
     * Update pivot tables with related entities
     *
     * @return $this
     */
    public function saveRelatedEntities()
    {
        $this->getResource()->saveContentTypes($this);
        $this->getResource()->saveTaxonomies($this);

        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Collection
     */
    public function getTaxonomies()
    {
        $taxonomyIds = $this->getTaxonomyIds();
        $taxonomies = Mage::getModel('gene_bluefoot/taxonomy')->getCollection();
        if(!$taxonomyIds || !count($taxonomyIds)){
            //we will load an empty collection
            $taxonomyIds = array('null');
        }

        $taxonomies->addFieldToFilter('taxonomy_id', array('in' => $taxonomyIds));

        return $taxonomies;
    }

    /**
     * @param bool|false $forceReload
     * @return mixed
     */
    public function getTaxonomyIds($forceReload = false)
    {
        if($this->hasData('taxonomy_ids') && is_array($this->getData('taxonomy_ids')) && !$forceReload){
            return $this->getData('taxonomy_ids');
        }else{
            $taxonomyIds = $this->getResource()->loadTaxonomyIds($this);
            if(!is_array($taxonomyIds)){
                $taxonomyIds = array();
            }

            $this->setData('taxonomy_ids', $taxonomyIds);
        }

        return $this->getData('taxonomy_ids');
    }


    /**
     * @param bool|false $forceReload
     * @return mixed
     */
    public function getContentTypeIds($forceReload = false)
    {

        if($this->hasData('content_type_ids') && is_array($this->getData('content_type_ids')) && !$forceReload){
            return $this->getData('content_type_ids');
        }else{
            $contentTypeIds = $this->getResource()->loadContentTypeIds($this);
            if(!is_array($contentTypeIds)){
                $contentTypeIds = array();
            }

            $this->setData('content_type_ids', $contentTypeIds);
        }


        return $this->getData('content_type_ids');
    }

    /**
     * Get all entities, with the option of filtering by content type
     *
     * @param null $type
     * @return array|Gene_BlueFoot_Model_Resource_Entity_Collection
     */
    public function getAllEntities($type = null)
    {
        $contentTypeIds = $this->getContentTypeIds();

        if(is_array($contentTypeIds)){

            $typeFilter = false;
            if($type instanceof Varien_Object && $type->getId()){
                $typeFilter = $type->getId();
            }elseif(is_int($type)){
                $typeFilter = $type;
            }

            $collection = Mage::getModel('gene_bluefoot/entity')->getCollection();

            $types = Mage::getModel('gene_bluefoot/type')->getCollection()->addFieldToFilter('type_id', array('in' => $contentTypeIds));
            if($typeFilter){
                $types->addFieldToFilter('type_id', $typeFilter);
            }

            $collection->addTypeIdFilter($types->getAllIds());

            return $collection;
        }

        return array();
    }


    /**
     * @return Gene_BlueFoot_Model_Resource_Type_Collection|array
     */
    public function getContentTypes()
    {
        $contentTypeIds = $this->getContentTypeIds();
        if(!count($contentTypeIds)){
            return array();
        }

        $contentTypes = Mage::getModel('gene_bluefoot/type')->getCollection();
        $contentTypes->addFieldToFilter('type_id', array('in' => $contentTypeIds));

        return $contentTypes;
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
    public function getAppUrl()
    {
        return $this->getUrlModel()->getAppUrl($this);
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

    /**
     * @return mixed
     */
    public function getUrlKey()
    {
        return ($this->getData('url_key') ? $this->getData('url_key') : $this->getData('url_prefix'));
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

}