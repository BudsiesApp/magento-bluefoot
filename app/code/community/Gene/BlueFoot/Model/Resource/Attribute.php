<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Attribute
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
    /**
     * Ensure additional data is set
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Entity_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if($object->getAdditional()){
            $object->setAdditionalData(serialize($object->getAdditional()));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Ensure additional data is set
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Resource_Entity_Attribute
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if($object->getAdditionalData()){
            $object->setAdditional(unserialize($object->getAdditionalData()));
        }
        return parent::_afterLoad($object);
    }

    //Not used
    protected function _getEavWebsiteTable()
    {
        return null;
    }

    //Not used
    protected function _getFormAttributeTable()
    {
        return null;
    }
    
}