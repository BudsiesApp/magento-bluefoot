<?php

/**
 * Class Gene_BlueFoot_Helper_View_Entity
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_View_Entity extends Gene_BlueFoot_Helper_View_Abstract
{

    /**
     * @param Gene_BlueFoot_Model_Entity $entity
     * @param $controller
     * @return $this
     */
    public function initEntityLayout(Gene_BlueFoot_Model_Entity $entity, $controller)
    {
        $settings = $this->getEntitySettings($entity);
        if ($settings->getCustomDesign()) {
            $this->applyCustomDesign($settings->getCustomDesign());
        }

        $contentType = $entity->getContentType();

        $update = $controller->getLayout()->getUpdate();
        $update->addHandle('default');
        $controller->addActionLayoutHandles();

        $update->addHandle('genecms_entity_view');
        $update->addHandle(self::HANDLE_PREFIX . 'entity_view');
        $update->addHandle(self::HANDLE_PREFIX . 'entity_view_type_' . strtoupper($contentType->getIdentifier()));
        $update->addHandle(self::HANDLE_PREFIX . 'entity_view_' . $entity->getId());

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


}