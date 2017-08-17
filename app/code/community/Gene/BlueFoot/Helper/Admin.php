<?php

/**
 * Class Gene_BlueFoot_Helper_Admin
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_Admin extends Gene_BlueFoot_Helper_Data
{
    /**
     * Clears the admin menu cache
     * @return bool
     */
    public function clearAdminMenuCache()
    {
        try {
            Mage::app()->getCache()->clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS)
            );
            return false;
        }catch (Exception $e){
            Mage::logException($e);
            return false;
        }
    }
}