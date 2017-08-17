<?php

/**
 * Class Gene_BlueFoot_Block_Debug_Main
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Debug_Main extends Mage_Core_Block_Template
{
    /**
     * @return bool
     * @todo Make dynamic from admin - IP and config based
     */
    public function isActive()
    {
        if(Mage::app()->getRequest()->isAjax()){
            return false;
        }
        return false;
        //return isset($_GET['gene-debug']);
    }

    public function getLayoutHandles()
    {
        return Mage::app()->getLayout()->getUpdate()->getHandles();
    }

    public function getCurrentApp()
    {
        $app = Mage::registry('current_genecms_app');
        if($app){
            $app->setData('_view_options_debug', $app->getViewOptionInstance()->getData());
        }

        return $app;
    }
}