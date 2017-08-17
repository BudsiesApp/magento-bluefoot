<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Progress
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Progress extends Mage_Adminhtml_Block_Widget
{
    protected $_defaultTitle = 'Progress';

    protected function _construct()
    {
        $this->setTemplate('gene/bluefoot/setup/app/wizard/progress.phtml');
    }

    public function getTitle()
    {
        return $this->hasData('title') ? $this->getData('title') : $this->_defaultTitle;
    }
}