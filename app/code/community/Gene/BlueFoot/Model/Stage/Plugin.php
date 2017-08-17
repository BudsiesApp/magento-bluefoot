<?php

/**
 * Build and return the data associated with JS plugins, these are plugins that interact with the stage (page builder)
 * within the admin.
 *
 * Class Gene_BlueFoot_Model_Stage_Plugin
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Plugin extends Mage_Core_Model_Abstract
{
    /**
     * Return the JS admin plugins to be loaded
     *
     * @return \Mage_Core_Model_Config_Element
     */
    public function getJsPlugins()
    {
        $plugins = Mage::helper('gene_bluefoot/config')->getConfig('plugins/js');
        $jqueryPlugins = Mage::helper('gene_bluefoot/config')->getConfig('plugins/jquery');
        $asyncPlugins = Mage::helper('gene_bluefoot/config')->getConfig('plugins/async');
        $onBuildWidgets = Mage::helper('gene_bluefoot/config')->getConfig('on_build/widgets');
        if($plugins) {
            $pluginsArray = $plugins->asArray();
            if ($jqueryPlugins) {
                $pluginsArray['jquery'] = $jqueryPlugins->asArray();
            }
            if ($asyncPlugins) {
                $pluginsArray['async'] = $asyncPlugins->asArray();
            }
            if ($onBuildWidgets) {
                $pluginsArray['on_build'] = $onBuildWidgets->asArray();
            }
            return $pluginsArray;
        }

        return false;
    }
}