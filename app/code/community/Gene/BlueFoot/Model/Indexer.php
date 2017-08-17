<?php

/**
 * Class Gene_BlueFoot_Model_Indexer
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'gene_bluefoot_entity_url_match_result';

    protected $_matchedEntities=array(

        Gene_BlueFoot_Model_Entity::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            //Mage_Index_Model_Event::TYPE_DELETE //indexes will worry about these so no action needed
        ),
        Gene_BlueFoot_Model_Taxonomy_Term::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        )
    );

    /**
     * @return Gene_BlueFoot_Model_Url
     */
    protected function _getUrlModel()
    {
        return Mage::getModel('gene_bluefoot/url');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Mage::helper('gene_bluefoot')->__('BlueFoot URL Index');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('gene_bluefoot')->__('Re-indexes URLS associated with the BlueFoot CMS. Apps, Content & Taxonomy Terms');
    }

    /**
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $dataObj = $event->getDataObject();
        if ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $dataObj);
        } elseif ($event->getType() == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            $event->addNewData(self::EVENT_MATCH_MASSACTION_KEY, $dataObj->getIds());
        }

        return $this;
    }

    /**
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if($event->getEntity() == Gene_BlueFoot_Model_Entity::ENTITY){
            if (!empty($data[self::EVENT_MATCH_RESULT_KEY])) {
                $this->_updateEntity($data[self::EVENT_MATCH_RESULT_KEY]);
            } elseif (!empty($data[self::EVENT_MATCH_MASSACTION_KEY]) && is_array($data[self::EVENT_MATCH_MASSACTION_KEY])) {
                $this->_massUpdateEntities($data[self::EVENT_MATCH_MASSACTION_KEY], true);
            }
        }elseif($event->getEntity() == Gene_BlueFoot_Model_Taxonomy_Term::ENTITY){
            if (!empty($data[self::EVENT_MATCH_RESULT_KEY])) {
                $this->_updateTerm($data[self::EVENT_MATCH_RESULT_KEY]);
            } elseif (!empty($data[self::EVENT_MATCH_MASSACTION_KEY]) && is_array($data[self::EVENT_MATCH_MASSACTION_KEY])) {
                $this->_massUpdateTerms($data[self::EVENT_MATCH_MASSACTION_KEY], true);
            }
        }elseif($event->getEntity() == 'gene_bluefoot_taxonomy'){
            if (!empty($data[self::EVENT_MATCH_RESULT_KEY])) {
                $this->_updateTaxonomy($data[self::EVENT_MATCH_RESULT_KEY]);
            } elseif (!empty($data[self::EVENT_MATCH_MASSACTION_KEY]) && is_array($data[self::EVENT_MATCH_MASSACTION_KEY])) {
                $this->_massUpdateTaxonomies($data[self::EVENT_MATCH_MASSACTION_KEY], true);
            }
        }
        elseif($event->getEntity() == 'gene_bluefoot_app'){
            //app urls are not stored in the rewrite table and are handled as part of the router
        }

        return $this;
    }

    public function _updateEntity(Varien_Object $entity)
    {
        $urlModel = $this->_getUrlModel();
        $urlModel->refreshEntityRewrite($entity->getId());

        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function _massUpdateEntities(array $ids)
    {
        $urlModel = $this->_getUrlModel();
        foreach($ids as $entityId){
            $urlModel->refreshEntityRewrite($entityId);
        }

        return $this;
    }

    public function _updateTerm(Varien_Object $term)
    {
        $urlModel = $this->_getUrlModel();
        $urlModel->refreshTermRewrite($term->getId());
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function _massUpdateTerms(array $ids)
    {
        $urlModel = $this->_getUrlModel();
        foreach($ids as $termId){
            $urlModel->refreshTermRewrite($termId);
        }

        return $this;
    }

    public function _updateTaxonomy(Varien_Object $taxonomy)
    {
        $urlModel = $this->_getUrlModel();

        $termIds = $taxonomy->getTermIds();
        if(is_array($termIds)){
            foreach($termIds as $termId){
                $urlModel->refreshTermRewrite($termId);
            }
        }

        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function _massUpdateTaxonomies(array $ids)
    {
        foreach($ids as $id){
            $taxonomy = Mage::getModel('gene_bluefoot/taxonomy')->load($id);
            if($taxonomy->getId()){
                $this->_updateTaxonomy($taxonomy);
            }
        }

        return $this;
    }

    public function reindexAll()
    {
        $urlModel = $this->_getUrlModel();
        foreach(Mage::app()->getStores() as $store){
            if($store->getIsActive()){
                $urlModel->refreshEntityRewrites($store->getId());
                $urlModel->refreshTermRewrites($store->getId());
            }
        }
    }

    /**
     * match whether the reindexing should be fired
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $result = parent::matchEvent($event);
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }
}