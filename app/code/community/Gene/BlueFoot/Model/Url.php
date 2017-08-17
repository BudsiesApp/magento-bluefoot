<?php

/**
 * Class Gene_BlueFoot_Model_Url
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Url extends Varien_Object
{

    protected  $_url;

    protected $_urlRewrite;

    protected $_stores = array();

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (null === $this->_url) {
            $this->_url = Mage::getModel('core/url');
        }
        return $this->_url;
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Gene_BlueFoot_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (null === $this->_urlRewrite) {
            $this->_urlRewrite = Mage::getModel('gene_bluefoot/url_rewrite');
        }
        return $this->_urlRewrite;
    }

    public function getEntityUrl(Gene_BlueFoot_Model_Entity $entity, $params = array())
    {
        $url = $entity->getData('url');
        if (!empty($url)) {
            return $url;
        }

        $requestPath = $entity->getData('request_path');

        if (empty($requestPath)) {
            $requestPath = $this->_getRequestPath($entity, $this->_getEntityTermIdForUrl($entity, $params));
            $entity->setRequestPath($requestPath);
        }

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $entity->getStoreId();
        }

        if ($storeId != $this->_getStoreId()) {
            //$params['_store_to_url'] = true;
        }

        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }

        $this->getUrlInstance()->setStore($storeId);
        $entityUrl = $this->_getEntityUrl($entity, $requestPath, $params);

        $entity->setData('url', $entityUrl);
        return $entity->getData('url');
    }

    public function _getEntityUrl($entity, $requestPath, $routeParams)
    {
        if (!empty($requestPath)) {
            if($urlPrefix = $entity->getContentApp()->getUrlPrefix()){
                $requestPath = $urlPrefix . '/'. $requestPath;
            }
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }

        $routeParams['id'] = $entity->getId();
        $routeParams['s'] = $entity->getUrlKey();
        $termId = $this->_getEntityTermIdForUrl($entity, $routeParams);
        if ($termId) {
            $routeParams['term'] = $termId;
        }

        return $this->getUrlInstance()->getUrl('bluefoot/content/view', $routeParams);
    }

    protected function _getAppUrl($entity, $requestPath, $routeParams)
    {
        if (!empty($requestPath)) {
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }

        if($entity->getUrlPrefix()){
            return $this->getUrlInstance()->getDirectUrl($entity->getUrlPrefix(), $routeParams);
        }else{
            $routeParams['id'] = $entity->getId();
            $routeParams['s'] = $entity->getUrlKey();

            return $this->getUrlInstance()->getUrl('bluefoot/app/view', $routeParams);
        }

    }

    protected function _getTermUrl($entity, $requestPath, $routeParams)
    {
        if (!empty($requestPath)) {
            if($urlPrefix = $entity->getContentApp()->getUrlPrefix()){
                $requestPath = $urlPrefix . '/'. $requestPath;
            }
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }

        $routeParams['term_id'] = $entity->getId();
        $routeParams['s'] = $entity->getUrlKey();

        return $this->getUrlInstance()->getUrl('bluefoot/app/viewTerm', $routeParams);
    }

    public function getAppUrl(Gene_BlueFoot_Model_App $app, $params = array())
    {
        $url = $app->getData('url');
        if (!empty($url)) {
            return $url;
        }

        $requestPath = $app->getData('request_path');

        if (empty($requestPath)) {
            $requestPath = $this->_getAppRequestPath($app);
            $app->setRequestPath($requestPath);
        }

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $app->getStoreId();
        }

        if ($storeId != $this->_getStoreId()) {
            //$params['_store_to_url'] = true;
        }

        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }

        $this->getUrlInstance()->setStore($storeId);
        $appUrl = $this->_getAppUrl($app, $requestPath, $params);

        $app->setData('url', $appUrl);
        return $app->getData('url');
    }

    public function getTermUrl(Gene_BlueFoot_Model_Taxonomy_Term $term, $params = array())
    {
        $url = $term->getData('url');
        if (!empty($url)) {
            return $url;
        }

        $requestPath = $term->getData('request_path');

        if (empty($requestPath)) {
            $requestPath = $this->_getTermRequestPath($term);
            $term->setRequestPath($requestPath);
        }

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $term->getStoreId();
        }

        if ($storeId != $this->_getStoreId()) {
            //$params['_store_to_url'] = true;
        }

        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }

        $this->getUrlInstance()->setStore($storeId);
        $termUrl = $this->_getTermUrl($term, $requestPath, $params);

        $term->setData('url', $termUrl);
        return $term->getData('url');
    }

    protected function _getEntityTermIdForUrl($entity, $params)
    {
        if (isset($params['_ignore_taxonomy'])) {
            return null;
        } else {
            return $entity->getCurrentTermId() && !$entity->getDoNotUseTermId()
                ? $entity->getCurrentTermId() : null;
        }
    }

    /**
     * Retrieve request path
     *
     * @param Gene_BlueFoot_Model_Entity $entity
     * @param int $taxTermId
     * @return bool|string
     * @return bool|string
     */
    protected function _getRequestPath($entity, $taxTermId, $appId=null)
    {
        $idPath = sprintf('entity/%d', $entity->getEntityId());

        if($appId){
            $idPath = sprintf('%s/app/%d', $idPath, $appId);
        }

        if ($taxTermId) {
            $idPath = sprintf('%s/term/%d', $idPath, $taxTermId);
        }

        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($entity->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }

    /**
     * Retrieve request path
     *
     * @param Gene_BlueFoot_Model_Entity $entity
     * @param int $taxTermId
     * @return bool|string
     * @return bool|string
     */
    protected function _getAppRequestPath($app)
    {
        $idPath = sprintf('app/%d', $app->getEntityId());

        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($app->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }

    protected function _getTermRequestPath($term)
    {
        $idPath = sprintf('taxonomyterm/%d', $term->getEntityId());

        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($term->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }

    /**
     * Returns checked store_id value
     *
     * @param int|null $id
     * @return int
     */
    protected function _getStoreId($id = null)
    {
        return Mage::app()->getStore($id)->getId();
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }


    public function refreshEntityRewrites($storeId)
    {
        $entityCollection = Mage::getModel('gene_bluefoot/entity')->getCollection();
        $entityCollection->setStoreId($storeId);

        $entityIds = $entityCollection->getAllIds();

        foreach($entityIds as $entityId){
            $this->refreshEntityRewrite($entityId, $storeId);
        }

        return $this;
    }

    public function refreshTermRewrites($storeId)
    {
        $terms = Mage::getModel('gene_bluefoot/taxonomy_term')->getCollection();
        $terms->setStoreId($storeId);
        $termIds = $terms->getAllIds();
        foreach($termIds as $termId){
            $this->refreshTermRewrite($termId, $storeId);
        }

        return $this;

    }

    public function refreshTermRewrite($termId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshTermRewrite($termId, $store->getId());
            }
            return $this;
        }

        $term = Mage::getModel('gene_bluefoot/taxonomy_term')->setStoreId($storeId)->load($termId);
        if ($term && $term->getUrlKey()) {
            $this->_refreshTermRewrite($term);

            $childTermIds = $term->getChildIds();

            if(is_array($childTermIds)){
                foreach($childTermIds as $childTermId){
                    $this->refreshTermRewrite($childTermId, $storeId);
                }
            }

            $termContentIds = $term->getContentIds();
            if(is_array($termContentIds)){
                foreach($termContentIds as $termContentId){
                    $this->refreshEntityRewrite($termContentId, $storeId, array($termId));
                }
            }

        }
    }

    protected function _refreshTermRewrite(Varien_Object $term)
    {
        $urlKey = $term->getFullUrlKey();
        if(!$urlKey){
            return false;
        }
        $idPath = sprintf('taxonomyterm/%d', $term->getId());
        $targetPath  = 'bluefoot/app/viewTerm/term_id/' . $term->getId();

        $requestPath = $urlKey;

        $rewriteData = array(
            'store_id'      => $term->getStoreId() ? $term->getStoreId() : 0,
            'id_path'       => $idPath,
            'app_id'       => $term->getContentApp()->getId(),
            'taxonomy_term_id' => $term->getId(),
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
        );

        $this->_saveRewrite($rewriteData);
    }

    /**
     * Refresh entity rewrite urls for one store or all stores
     * Called as a reaction on entity change that affects rewrites
     *
     * @param int $entityId
     * @param int|null $storeId
     * @return $this
     */
    public function refreshEntityRewrite($entityId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshEntityRewrite($entityId, $store->getId());
            }
            return $this;
        }

        $entity = Mage::getModel('gene_bluefoot/entity')->setStoreId($storeId)->load($entityId);
        if ($entity && $entity->getUrlKey()) {
            $store = $this->getStores($storeId);
            $termIds = $entity->getTaxonomyTermIds();

            $entity->setStoreId($storeId);
            $this->_refreshEntityRewrite($entity);

            if(is_array($termIds) && count($termIds)){

                $terms = Mage::getModel('gene_bluefoot/taxonomy_term')->getCollection();
                $terms->setStoreId($storeId);
                $terms->addAttributeToSelect(array('url_key', 'title'));
                $terms->addFieldToFilter('entity_id', array('in' => $termIds));

                // Create entity term url rewrites
                foreach ($terms as $term) {
                    $this->_refreshEntityTermRewrite($entity, $term);
                }
            }else{
                $termIds = array();
            }

            //remove all other rewrites
            $this->clearEntityRewrites($entityId, $storeId, $termIds);

            //early garbage collection
            unset($terms);
            unset($entity);
        } else {

        }

        return $this;
    }

    /**
     * Refresh entity rewrite
     *
     * @param Varien_Object $entity
     * @return $this
     */
    protected function _refreshEntityRewrite(Varien_Object $entity)
    {
        if ($entity->getUrlKey() == '') {
            $urlKey = $this->formatUrlKey($entity->getName());
        }
        else {
            $urlKey = $this->formatUrlKey($entity->getUrlKey());
        }

        if(!$urlKey){
            return false;
        }

        $idPath = sprintf('entity/%d', $entity->getId());
        $targetPath  = 'bluefoot/content/view/id/' . $entity->getId();
        $requestPath = $urlKey;

        $updateKeys = true;

        $rewriteData = array(
            'store_id'      => $entity->getStoreId() ? $entity->getStoreId() : 0,
            'id_path'       => $idPath,
            'entity_id'    => $entity->getId(),
            'app_id'       => $entity->getContentType()->getAppId(),
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
        );

        $this->_saveRewrite($rewriteData);

        return $this;
    }

    protected function _refreshEntityTermRewrite(Varien_Object $entity, Varien_Object $taxonomyTerm)
    {
        $idPath = sprintf('entity/%d/term/%d', $entity->getId(), $taxonomyTerm->getId());
        $targetPath  = 'bluefoot/content/view/id/' . $entity->getId() . '/term/' . $taxonomyTerm->getId();

        $entityUrlKey = $entity->getUrlKey();

        $requestPath = $taxonomyTerm->getFullUrlKey() . '/' . $entityUrlKey;

        $rewriteData = array(
            'store_id'      => $entity->getStoreId() ? $entity->getStoreId() : 0,
            'id_path'       => $idPath,
            'entity_id'    => $entity->getId(),
            'app_id'       => $entity->getContentType()->getAppId(),
            'taxonomy_term_id' => $taxonomyTerm->getId(),
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
        );

        $this->_saveRewrite($rewriteData);

        return $this;
    }


    /**
     * @param null $storeId
     * @return array|bool
     */
    public function getStores($storeId = null)
    {
        if(!count($this->_stores)){
            $_allStores = Mage::app()->getStores();
            $stores = array();
            foreach($_allStores as $_store){

                if($_store->getIsActive()){
                    $stores[$_store->getId()] = $_store;
                }
            }
            $this->_stores = $stores;
        }

        if(!is_null($storeId)){
            return isset($this->_stores[$storeId]) ? $this->_stores[$storeId] : false;
        }

        return $this->_stores;
    }

    protected function _saveRewrite($rewriteData)
    {
        $storeId = isset($rewriteData['store_id']) ? $rewriteData['store_id'] : 0;
        $urlRewrite = $this->getUrlRewrite()->setStoreId($storeId);
        $urlRewrite->loadByIdPath($rewriteData['id_path']);
        if($urlRewrite->getId()){
            $rewriteData['url_rewrite_id'] = $urlRewrite->getId();
        }

        $urlRewrite->setData($rewriteData);


        $urlRewrite->save();

        //reset object for next use
        $urlRewrite->unsetData();
        $urlRewrite->unsetOldData();

        return $this;
    }

    public function clearEntityRewrites($entityId, $storeId, $excludeTermIds)
    {
        if(empty($excludeTermIds)){
            return $this;
        }
        $urlRewriteResouce = $this->getUrlRewrite()->getResource();

        $where = array(
            'entity_id = ?' => $entityId,
            'store_id = ?' => $storeId
        );


        $where['taxonomy_term_id NOT IN (?)'] = $excludeTermIds;
        $where[] = 'taxonomy_term_id IS NOT NULL';

        $urlRewriteResouce->clearRewrites($where);

        return $this;
    }

}