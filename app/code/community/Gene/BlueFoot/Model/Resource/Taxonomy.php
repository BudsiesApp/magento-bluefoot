<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Taxonomy
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Taxonomy extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_entityType = null;

    protected function _construct()
    {
        $this->_init("gene_bluefoot/taxonomy", "taxonomy_id");
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        return parent::_afterLoad($object);
    }


    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $type)
    {
        return parent::_afterSave($type);
    }
}