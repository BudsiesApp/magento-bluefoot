<?php

/**
 * Class Gene_BlueFoot_Helper_View_Term
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_View_Term extends Gene_BlueFoot_Helper_View_Abstract
{
    public function initTermLayout(Gene_BlueFoot_Model_Taxonomy_Term $term, $controller)
    {
        $settings = $this->getEntitySettings($term);
        if ($settings->getCustomDesign()) {
            $this->applyCustomDesign($settings->getCustomDesign());
        }

        $update = $controller->getLayout()->getUpdate();
        $update->addHandle('default');
        $controller->addActionLayoutHandles();

        $update->addHandle('genecms_entity_view');
        $update->addHandle(self::HANDLE_PREFIX . 'app_taxonomy_term_view');
        $update->addHandle(self::HANDLE_PREFIX . 'app_taxonomy_term_view_' . $term->getId());
        $update->addHandle(self::HANDLE_PREFIX . 'app_taxonomy_term_view_MODE_' . $term->getDisplayMode());
        if ($currentApp = $this->getCurrentApp()) {
            $update->addHandle(self::HANDLE_PREFIX . 'app_view_' . $currentApp->getUrlKey());
            $update->addHandle(self::HANDLE_PREFIX . 'app_view_term_view_' . $currentApp->getUrlKey());
        }

        $controller->loadLayoutUpdates();

        // Apply custom layout update once layout is loaded
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates) {
            if (is_array($layoutUpdates)) {
                foreach($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }
        }

        $controller->generateLayoutXml()->generateLayoutBlocks();

        // Apply custom layout (page) template once the blocks are generated
        if ($settings->getPageLayout()) {
            $controller->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
        }

        return $this;

    }

    public function getEntitySettings($entity)
    {
        $settings = new Varien_Object;
        if (!$entity) {
            return $settings;
        }

        $term = $entity;

        $taxonomy = $term->getTaxonomy();
        if($taxonomy){
            $termDefaults = is_array($taxonomy->getTermDefaults()) ? $taxonomy->getTermDefaults() : array();
            foreach($termDefaults as $key => $defaultValue){
                //only override data if not set already
                if(!$term->getData($key)){
                    $term->setData($key, $defaultValue);
                }
            }
        }

        return parent::getEntitySettings($term);
    }
}