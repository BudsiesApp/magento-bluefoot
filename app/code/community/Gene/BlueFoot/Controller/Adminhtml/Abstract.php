<?php

/**
 * Class Gene_BlueFoot_Controller_Adminhtml_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_Action
{
    protected $_entityType = NULL;
    protected $_entityTypeCode = 'gene_bluefoot_entity';

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($this->_entityTypeCode);
        }
        return $this->_entityType;
    }

    /**
     * Set the admin active menu item
     * @param Gene_BlueFoot_Model_App $app
     * @return $this
     */
    protected function _setAppCurrentMenu(Gene_BlueFoot_Model_App $app)
    {
        $this->_setActiveMenu('genecms_app_'.$app->getId());
        return $this;
    }
}