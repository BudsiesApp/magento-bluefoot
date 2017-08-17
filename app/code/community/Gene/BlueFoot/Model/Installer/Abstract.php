<?php

/**
 * Class Gene_BlueFoot_Model_Installer_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
abstract class Gene_BlueFoot_Model_Installer_Abstract extends Varien_Object
{
    protected $_createdEntities = array();

    protected $_mode = 'live';

    protected $_errors = array();

    protected $_validationErrors = array();

    protected $_newAttributes = array();

    protected $_exceptionOnError = false;

    protected $_exceptions = array();

    protected $_newEntities = array();

    /**
     * @return Gene_BlueFoot_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gene_bluefoot');
    }

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        return $this->_getHelper()->getEntityType();
    }

    /**
     * @param $type
     * @return Gene_BlueFoot_Model_Installer_Abstract
     * @throws Exception
     */
    protected function _getInstaller($type)
    {
        $installerClass = Mage::getSingleton('gene_bluefoot/installer_' . $type);
        if(!$installerClass){
            throw new Exception('No such installer class for type "'.$type.'".');
        }

        return $installerClass;
    }

    public function getMode()
    {
        return $this->_mode;
    }

    public function isMockMode()
    {
        return ($this->_mode == 'mock');
    }

    public function isLiveMode()
    {
        return ($this->_mode == 'live');
    }

    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

    public function setMockMode()
    {
        return $this->setMode('mock');
    }

    public function setLiveMode()
    {
        return $this->setMode('live');
    }

    public function getCreatedEntities()
    {
        return $this->_createdEntities;
    }

    public function registerNewAttribute($attr)
    {
        if(is_object($attr)){
            $attrCode = $attr->getAttributeCode();
        }elseif(is_array($attr)){
            $attrCode = isset($attr['attribute_code']) ? $attr['attribute_code'] : false;
            $attr = new Varien_Object($attr);
        }else{
            throw new Exception('Cannot register new attribute. Needs to be of type object or array');
        }

        if(!$attrCode){
            throw new Exception('Cannot register new attribute. No attribute code detected.');
        }

        $this->_newAttributes[$attrCode] = $attr;

        return $this;
    }

    public function registerNewEntities($entities)
    {
        if(is_array($entities)) {
            foreach ($entities as $entityType => $typeEntities) {
                if(!isset($this->_newEntities[$entityType])){
                    $this->_newEntities[$entityType] = $typeEntities;
                }

                $this->_newEntities[$entityType] = array_merge($this->_newEntities[$entityType], $typeEntities);
            }
        }

        return $this;
    }

    public function registerMultipleNewAttributes(array $attributes)
    {
        foreach($attributes as $attr){
            $this->registerNewAttribute($attr);
        }

        return $this;
    }

    public function getNewAttributes()
    {
        return $this->_newAttributes;
    }

    public function setExceptionOnError($exceptionOnError = true)
    {
        $this->_exceptionOnError = (bool) $exceptionOnError;

        return $this;
    }

    public function hasErrors()
    {
        return (count($this->_errors) > 0);
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Check to see if an attribute already exists
     *
     * @param $attributeCode
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function attributeExists($attributeCode)
    {
        $entityType = $this->_getEntityType();

        $attribute = Mage::getModel('gene_bluefoot/attribute');
        $attribute->loadByCode($entityType, $attributeCode);
        if($attribute && $attribute->getId()){
            return true;
        }


        if(array_key_exists($attributeCode, $this->_newAttributes)){
            return true;
        }


        return false;
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function contentTypeExists($identifier)
    {
        $model = Mage::getModel("gene_bluefoot/type");
        $model->load($identifier, 'identifier');
        if($model->getId()){
            return true;
        }

        return false;
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function contentBlockExists($identifier)
    {
        $model = Mage::getModel("gene_bluefoot/type");
        $model->load($identifier, 'identifier');
        if($model->getId()){
            return $model;
        }

        return false;
    }

    public function contentBlockCreated($identifier, $field = 'identifier')
    {
        $createdBlocks = (isset($this->_newEntities['blocks']) && is_array($this->_newEntities['blocks'])) ? $this->_newEntities['blocks'] : array();
        foreach($createdBlocks as $createdBlock){
            if($identifier = $createdBlock->getData($field)){
                return $createdBlock;
            }
        }

        return false;
    }
}