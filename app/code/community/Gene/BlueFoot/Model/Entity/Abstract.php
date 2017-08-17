<?php
/**
 * Class Gene_BlueFoot_Model_Entity_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
abstract class Gene_BlueFoot_Model_Entity_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Identifuer of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = 0;

    const ENTITY = 'gene_bluefoot_entity';

    /**
     * This array contains codes of attributes which have value in current store
     *
     * @var array
     */
    protected $_storeValuesFlags = array();

    /**
     * Attribute default values
     *
     * This array contain default values for attributes which was redefine
     * value for store
     *
     * @var array
     */
    protected $_defaultValues = array();

    protected $_entityType = null;

    /**
     * @return array|bool
     */
    public function validate()
    {
        $this->getResource()->validate($this);
        return $this;
    }

    /**
     * @param $attributeCode
     * @return $this
     */
    public function setExistsStoreValueFlag($attributeCode)
    {
        $this->_storeValuesFlags[$attributeCode] = true;
        return $this;
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    public function getExistsStoreValueFlag($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_storeValuesFlags);
    }

    /**
     * @param $attributeCode
     * @param $value
     * @return $this
     */
    public function setAttributeDefaultValue($attributeCode, $value)
    {
        $this->_defaultValues[$attributeCode] = $value;
        return $this;
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    public function getAttributeDefaultValue($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_defaultValues) ? $this->_defaultValues[$attributeCode] : false;
    }

    /**
     * Get store ID if set otherwise get current store ID
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function getDefaultAttributeSet()
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSetId = $this->getEntityType()->getDefaultAttributeSetId();
        $attributeSet->load($attributeSetId);

        return $attributeSet;
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
     * @return mixed
     */
    public function getEntityTypeId()
    {
        return $this->getEntityType()->getId();
    }

    protected function _beforeSave()
    {
        if(!$this->hasData('entity_type_id')){
            $this->setData('entity_type_id', $this->getEntityTypeId());
        }
        return parent::_beforeSave();
    }

    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');
        $indexer->processEntityAction($this, $this::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);

        return $this;
    }

    /**
     * Init indexing process after product delete commit
     *
     * @return $this
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();

        /** @var \Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');

        $indexer->processEntityAction($this, $this::ENTITY, Mage_Index_Model_Event::TYPE_DELETE);
    }
}