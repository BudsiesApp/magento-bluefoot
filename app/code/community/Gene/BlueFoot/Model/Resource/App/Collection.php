<?php

/**
 * Class Gene_BlueFoot_Model_Resource_App_Collection
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_App_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init("gene_bluefoot/app");
    }
}
