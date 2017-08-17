<?php

/**
 * Class Gene_BlueFoot_Helper_View_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_View_Abstract extends Mage_Core_Helper_Abstract
{
    const HANDLE_PREFIX = 'bluefoot_';


    /**
     * Apply custom design
     *
     * @param string $design
     */
    public function applyCustomDesign($design)
    {
        $designInfo = explode('/', $design);
        if (count($designInfo) != 2) {
            return false;
        }
        $package = $designInfo[0];
        $theme   = $designInfo[1];
        $this->_apply($package, $theme);
    }

    /**
     * Apply package and theme
     *
     * @param string $package
     * @param string $theme
     */
    protected function _apply($package, $theme)
    {
        Mage::getSingleton('core/design_package')
            ->setPackageName($package)
            ->setTheme($theme);
    }

    public function getEntitySettings($entity)
    {
        $settings = new Varien_Object;
        if (!$entity) {
            return $settings;
        }
        $settings->setCustomDesign($entity->getCustomDesign())
            ->setPageLayout($entity->getPageLayout())
            ->setLayoutUpdates((array)$entity->getCustomLayoutUpdate())
            ->setCustomTemplate($entity->getCustomTemplate())
        ;

        return $settings;
    }

    /**
     * Get the current App from the registry
     * @return bool|mixed
     */
    public function getCurrentApp()
    {
        $app = Mage::registry('current_genecms_app');

        if ($app->getId()) {
            return $app;
        }
        return false;
    }
}