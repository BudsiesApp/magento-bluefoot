<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Stage_Template_Collection
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */ 
class Gene_BlueFoot_Model_Resource_Stage_Template_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gene_bluefoot/stage_template');
    }

}