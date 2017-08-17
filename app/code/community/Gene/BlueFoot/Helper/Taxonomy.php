<?php

class Gene_BlueFoot_Helper_Taxonomy extends Mage_Core_Helper_Abstract
{
    protected $_entityType;

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if(is_null($this->_entityType)){
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY);
        }

        return $this->_entityType;
    }
}