<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Success
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Success extends Mage_Adminhtml_Block_Widget
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('gene/bluefoot/setup/app/wizard/success.phtml');
    }

    /**
     * @return Gene_BlueFoot_Model_App|null
     */
    public function getCreatedApp()
    {
        return Mage::registry('created_app');
    }
}