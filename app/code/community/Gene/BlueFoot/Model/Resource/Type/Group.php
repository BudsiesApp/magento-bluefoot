<?php

/**
 * Class 
 * @author Dave Macaulay <dave@gene.co.uk>
 */ 
class Gene_BlueFoot_Model_Resource_Type_Group extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('gene_bluefoot/type_group', 'group_id');
    }

}