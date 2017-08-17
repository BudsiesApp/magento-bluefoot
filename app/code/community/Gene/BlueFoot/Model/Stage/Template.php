<?php

/**
 * Class Gene_BlueFoot_Model_Stage_Template
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */ 
class Gene_BlueFoot_Model_Stage_Template extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('gene_bluefoot/stage_template');
    }

    /**
     * Update our dates
     *
     * @return Mage_Core_Model_Abstract|void
     */
    public function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->isObjectNew()) {
            $this->setData('created_at', Mage::getSingleton('core/date')->gmtDate());
        }

        $this->setData('updated_at', Mage::getSingleton('core/date')->gmtDate());
    }

}