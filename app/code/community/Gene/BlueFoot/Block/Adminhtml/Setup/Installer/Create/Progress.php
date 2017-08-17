<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Progress
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Progress extends Mage_Adminhtml_Block_Widget
{
    protected function _construct()
    {
        $this->setTemplate('gene/bluefoot/setup/installer/new/progress.phtml');
    }
}