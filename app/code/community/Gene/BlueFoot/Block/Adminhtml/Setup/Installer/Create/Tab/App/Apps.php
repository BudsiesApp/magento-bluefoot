<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_App_Apps
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_App_Apps extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'gene/bluefoot/setup/installer/new/apps.phtml';

    public function getContentApps()
    {
        $apps = Mage::getModel('gene_bluefoot/app')->getCollection();
        return $apps;
    }

}