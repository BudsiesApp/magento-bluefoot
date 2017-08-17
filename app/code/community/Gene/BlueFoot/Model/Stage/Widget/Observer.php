<?php

/**
 * An observer to implement further data when widgets are being loaded into the admin stage (page builder)
 *
 * Class Gene_BlueFoot_Model_Stage_Widget_Observer
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Widget_Observer
{
    /**
     * Event called when we're building our stages config
     *
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function stageBuildConfig(Varien_Event_Observer $observer)
    {
        /* @var $config Varien_Object */
        $config = $observer->getEvent()->getConfig();
        $pluginData = $config->getData('plugins');

        // Send the upload path URL
        $pluginData['gene_widget_upload']['config']['upload_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_upload/upload');

        // Send the media upload URL for displaying images
        $uploadUrl = Mage::helper('gene_bluefoot/config')->getUploadUrl(true);
        $pluginData['gene_widget_upload']['config']['media_url'] = $uploadUrl;

        // Search URLs
        $pluginData['gene_widget_search_product']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_search/search', array('context' => 'product'));
        $pluginData['gene_widget_search_category']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_search/search', array('context' => 'category'));
        $pluginData['gene_widget_search_staticblock']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_search/search', array('context' => 'staticblock'));
        $pluginData['gene_widget_search_app_collection']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_search/selectTaxonomy');
        $pluginData['gene_widget_search_app_collection']['config']['taxonomy_title'] = $this->getTaxonomies();
        $pluginData['gene_widget_search_app_collection']['config']['content_apps'] = $this->getContentApps();
        $pluginData['gene_widget_video']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_video/preview');
        $pluginData['gene_widget_search_app_entity']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_search/search', array('context' => 'app_entity'));
        $pluginData['gene_widget_search_content_apps']['config']['source_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/stage_widget_search/selectContentApp');

        $config->setData('plugins', $pluginData);

        return $this;
    }

    /**
     * Retrieve the taxonomy collection for the plugin config
     *
     * @return array
     */
    public function getTaxonomies()
    {
        $collection = Mage::getModel('gene_bluefoot/taxonomy')->getCollection();

        $taxonomies = array();

        foreach($collection as $taxonomy) {
            $taxonomies[] = array(
                'title' => $taxonomy->getContentApp()->getTitle() . ' - ' . $taxonomy->getTitle(),
                'id' => $taxonomy->getId(),
                'content_app_id' => $taxonomy->getContentapp()->getId()
            );
        }

        return $taxonomies;
    }

    /**
     * Retrieve the content app collection for the plugin config
     *
     * @return array
     */
    public function getContentApps()
    {
        $collection = Mage::getModel('gene_bluefoot/app')->getCollection();

        $contentApps = array();

        foreach($collection as $app) {
            $contentApps[] = array(
                'title' => $app->getTitle(),
                'id' => $app->getId()
            );
        }

        return $contentApps;
    }

}