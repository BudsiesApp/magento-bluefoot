<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Eav_Attribute
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    const MODULE_NAME = 'Gene_BlueFoot';
    const ENTITY = 'gene_bluefoot_eav_attribute';

    const SCOPE_STORE                           = 0;
    const SCOPE_GLOBAL                          = 1;
    const SCOPE_WEBSITE                         = 2;


    protected $_eventPrefix = 'gene_bluefoot_entity_attribute';
    protected $_eventObject = 'attribute';

    static protected $_labels = null;

    protected function _construct()
    {
        $this->_init('gene_bluefoot/attribute');
    }

    /**
     * @return bool
     */
    public function isScopeStore()
    {
        return $this->getIsGlobal() == self::SCOPE_STORE;
    }

    /**
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * @return bool
     */
    public function isScopeGlobal()
    {
        return (!$this->isScopeStore() && !$this->isScopeWebsite());
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Return the data model for this attribute
     *
     * @param $entity
     *
     * @return mixed
     * @throws
     */
    public function getDataModel($entity)
    {
        $model = $this->getData('data_model');
        if ($model) {
            try {
                $modelInstance = Mage::getModel($model);
                if ($modelInstance) {
                    $modelInstance->setEntity($entity);
                    $modelInstance->setAttribute($this);
                    return $modelInstance;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Initialize store Labels for attributes
     *
     * @deprecated
     * @param int $storeId
     * @test
     */
    public static function initLabels($storeId = null)
    {
        echo __CLASS__ . '::' . __FUNCTION__ . '<br/>';
        die();
        if (is_null(self::$_labels)) {
            if (is_null($storeId)) {
                $storeId = Mage::app()->getStore()->getId();
            }
            $attributeLabels = array();
            $attributes = Mage::getResourceSingleton('gene_bluefoot/entity')->getAttributesByCode();
            foreach ($attributes as $attribute) {
                if (strlen($attribute->getData('frontend_label')) > 0) {
                    $attributeLabels[] = $attribute->getData('frontend_label');
                }
            }

            self::$_labels = Mage::app()->getTranslator()->getResource()
                ->getTranslationArrayByStrings($attributeLabels, $storeId);
        }
    }

    public function getBackendTypeByInput($type)
    {
        echo __CLASS__ . '::' . __FUNCTION__ . '<br/>';
        die();
        switch ($type) {
            case 'file':
                //do nothing
            case 'image':
                return 'varchar';
                break;
            default:
                return parent::getBackendTypeByInput($type);
                break;
        }
    }

    protected function _beforeDelete()
    {
        echo __CLASS__ . '::' . __FUNCTION__ . '<br/>';
        die();
        if (!$this->getIsUserDefined()) {
            throw new Mage_Core_Exception(Mage::helper('gene_bluefoot')->__('This attribute is not deletable'));
        }
        return parent::_beforeDelete();
    }
}