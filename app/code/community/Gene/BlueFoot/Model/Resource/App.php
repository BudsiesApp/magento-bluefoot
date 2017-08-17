<?php

/**
 * Class Gene_BlueFoot_Model_Resource_App
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_App extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init("gene_bluefoot/content_app", "app_id");
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        return parent::_afterLoad($object);
    }


    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        return parent::_afterSave($object);
    }

    /**
     * Save Associated Taxonomies in pivot table
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    public function saveTaxonomies(Mage_Core_Model_Abstract $object)
    {
        $taxonomyIds = $object->getTaxonomyIds();

        $connection = $this->_getWriteAdapter();
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('gene_bluefoot/taxonomy');

        if(count($taxonomyIds)){
            $taxonomies = Mage::getModel('gene_bluefoot/taxonomy')->getCollection();
            $taxonomies->addFieldToFilter('taxonomy_id', array('in' => $taxonomyIds));
            $saveTaxonomyIds = $taxonomies->getAllIds();

            $data = array(
                'app_id' => $object->getId(),
            );


            if(count($data)){
                $connection->update($table, $data, array('taxonomy_id IN (?)' => $saveTaxonomyIds));
            }
        }

        return $this;
    }

    /**
     * Save Associated Content Types in pivot table
     *
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    public function saveContentTypes(Mage_Core_Model_Abstract $object)
    {
        $cTypeIds = $object->getContentTypeIds();

        $connection = $this->_getWriteAdapter();
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('gene_bluefoot/type');

        if(count($cTypeIds)){
            $contentTypes = Mage::getModel('gene_bluefoot/type')->getCollection()->addContentTypeFilter('content');
            $contentTypes->addFieldToFilter('type_id', array('in' => $cTypeIds));
            $saveTypeIds = $contentTypes->getAllIds();
            $data = array();


            $data = array(
                'app_id' => $object->getId(),
            );


            if(count($data)){
                $connection->update($table, $data, array('type_id IN (?)' => $saveTypeIds));
            }
        }
        return $this;
    }

    public function loadTaxonomyIds(Mage_Core_Model_Abstract $object)
    {
        $appId = $object->getId();

        $connection = $this->_getWriteAdapter();
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('gene_bluefoot/taxonomy');

        $select = $connection->select()->from($table, 'taxonomy_id')->where('app_id = ?', $appId);

        $taxonomyIds = $connection->fetchCol($select, 'taxonomy_id');

        return $taxonomyIds;
    }

    public function loadContentTypeIds(Mage_Core_Model_Abstract $object)
    {
        $appId = $object->getId();

        $connection = $this->_getWriteAdapter();
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('gene_bluefoot/type');

        $select = $connection->select()->from($table, 'type_id')->where('app_id = ?', $appId);
        $cTypeIds = $connection->fetchCol($select, 'type_id');

        return $cTypeIds;
    }

    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cp.app_id')
            //->order('cps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->where('cp.url_prefix = ?', $identifier);

        if (!is_null($isActive)) {
            //$select->where('cp.is_active = ?', $isActive);
        }

        return $select;
    }
}