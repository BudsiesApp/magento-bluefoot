<?php

/**
 * Class Gene_BlueFoot_Model_Entity_Frontend_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
abstract class Gene_BlueFoot_Model_Entity_Frontend_Abstract extends Varien_Object
{
    protected $_entity;

    protected $_defaultRenderer = '';

    protected $_defaultTemplate = '';

    public function setEntity(Gene_BlueFoot_Model_Entity_Abstract $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_Entity_Abstract
     */
    public function getEntity()
    {
        $this->entitySet();
        return $this->_entity;
    }

    /**
     * @return Gene_BlueFoot_Model_Type
     */
    public function getEntityType()
    {
        return $this->getEntity()->getContentType();
    }

    public function entitySet($throwException = true)
    {
        if(!$this->_entity){
            if($throwException){
                throw new Exception('No entity model set.');
            }else{
                return false;
            }
        }

        return true;
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    abstract public function getRenderBlock();

    abstract public function getViewTemplate();

    protected function _getRender()
    {

    }

    /**
     * @return Gene_BlueFoot_Helper_Config
     */
    public function getConfigHelper()
    {
        return Mage::helper('gene_bluefoot/config');
    }


}