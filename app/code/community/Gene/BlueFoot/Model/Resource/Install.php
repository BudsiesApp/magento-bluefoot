<?php

class Gene_BlueFoot_Model_Resource_Install extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("gene_bluefoot/install", "installation_id");

    }

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

        if($object->getLog()){
            $object->setLogData(serialize($object->getLog()));
        }

        if ($object->isObjectNew()) {
            $object->setData('date_added', Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setData('updated_at', Mage::getSingleton('core/date')->gmtDate());

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
        }else{
            $object->setAdditional(array());
        }

        if($object->getLogData()){
            $object->setLog(unserialize($object->getLogData()));
        }else{
            $object->setLog(array());
        }

        return parent::_afterLoad($object);
    }
}