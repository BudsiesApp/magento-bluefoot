<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Entity_Collection
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 *
 */
class Gene_BlueFoot_Model_Resource_Entity_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('gene_bluefoot/entity');
    }

    /**
     * Filter by content type (content_type field)
     * @param $contentType
     * @return $this
     */
    public function addContentTypeFilter($contentType)
    {
        $types = Mage::getModel('gene_bluefoot/type')->getCollection()->addContentTypeFilter($contentType);

        $attributeSetIds = $types->getColumnValues('attribute_set_id');
        $this->addFieldToFilter('attribute_set_id', array('in' => $attributeSetIds));

        return $this;
    }

    /**
     * @param $typeId
     * @return $this
     */
    public function addTypeIdFilter($typeId)
    {
        $types = Mage::getModel('gene_bluefoot/type')->getCollection()->addIdFilter($typeId);
        $attributeSetIds = $types->getColumnValues('attribute_set_id');

        $this->addFieldToFilter('attribute_set_id', array('in' => $attributeSetIds));

        return $this;
    }

    /**
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @return $this
     */
    public function addTermFilter(Gene_BlueFoot_Model_Taxonomy_Term $term)
    {
        $termId = $term->getId();

        $cond = $this->getConnection()
            ->quoteInto('e.entity_id = content_term.content_id AND content_term.term_id = ?', $termId);

        $this->getSelect()->join(
            array('content_term' => $this->getTable('gene_bluefoot/taxonomy_term_content')),
            $cond,
            array('cat_index_position' => 'position')
        );

        return $this;
    }

    /**
     * Filter entity collection by term id(s)
     * @param $termIds
     * @return $this
     */
    public function addTermIdFilter($termIds)
    {
        if(!is_array($termIds)){
            $termIds = array($termIds);
        }

        $cond = $this->getConnection()
            ->quoteInto('e.entity_id = content_term.content_id AND content_term.term_id IN(?)', $termIds);

        $this->getSelect()->join(
            array('content_term' => $this->getTable('gene_bluefoot/taxonomy_term_content')),
            $cond
        );

        $this->getSelect()->group('e.entity_id');

        return $this;
    }

    /**
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addAttributeToFilter('is_active', 1, 'left');
        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Gene_ExpertCms_Model_Resource_Entity_Collection
     */
    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }

    /**
     * @param Varien_Db_Select $select
     * @param string $table
     * @param string $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $helper = Mage::getResourceHelper('eav');
            $adapter        = $this->getConnection();
            $valueExpr      = $adapter->getCheckSql(
                't_s.value_id IS NULL',
                $helper->prepareEavAttributeValue('t_d.value', $type),
                $helper->prepareEavAttributeValue('t_s.value', $type)
            );

            $select->columns(array(
                'default_value' => $helper->prepareEavAttributeValue('t_d.value', $type),
                'store_value'   => $helper->prepareEavAttributeValue('t_s.value', $type),
                'value'         => $valueExpr
            ));
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }
        return $select;
    }

    /**
     * Retrieve attributes load select
     *
     * @param string $table
     * @param array|int $attributeIds
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $storeId = $this->getStoreId();

        if ($storeId) {

            $adapter        = $this->getConnection();
            $entityIdField  = $this->getEntity()->getEntityIdField();
            $joinCondition  = array(
                't_s.attribute_id = t_d.attribute_id',
                't_s.entity_id = t_d.entity_id',
                $adapter->quoteInto('t_s.store_id = ?', $storeId)
            );
            $select = $adapter->select()
                ->from(array('t_d' => $table), array($entityIdField, 'attribute_id'))
                ->joinLeft(
                    array('t_s' => $table),
                    implode(' AND ', $joinCondition),
                    array())
                ->where('t_d.entity_type_id = ?', $this->getEntity()->getTypeId())
                ->where("t_d.{$entityIdField} IN (?)", array_keys($this->_itemsById))
                ->where('t_d.attribute_id IN (?)', $attributeIds)
                ->where('t_d.store_id = ?', 0);
        } else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id = ?', $this->getDefaultStoreId());
        }

        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param string $method
     * @param object $attribute
     * @param string $tableAlias
     * @param array $condition
     * @param string $fieldCode
     * @param string $fieldAlias
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $store_id = $this->_joinAttributes[$fieldCode]['store_id'];
        } else {
            $store_id = $this->getStoreId();
        }

        $adapter = $this->getConnection();

        if ($store_id != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '('.implode(') AND (', $condition).')';
            $defAlias     = $tableAlias . '_default';
            $defAlias     = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias   = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $adapter->quoteInto(
                " AND " . $adapter->quoteColumnAs("$defAlias.store_id", null) . " = ?",
                $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = $this->getConnection()->getCheckSql("{$tableAlias}.value_id > 0",
                $fieldAlias, $defFieldAlias);
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        } else {
            $store_id = $this->getDefaultStoreId();
        }
        $condition[] = $adapter->quoteInto(
            $adapter->quoteColumnAs("$tableAlias.store_id", null) . ' = ?', $store_id);
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return Gene_ExpertCms_Model_Resource_Entity_Collection
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'title', $additional = array())
    {
        $this->addAttributeToSelect('title');
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    protected function _toOptionHash($valueField = 'entity_id', $labelField = 'title')
    {
        $this->addAttributeToSelect('title');
        return parent::_toOptionHash($valueField, $labelField);
    }

    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}