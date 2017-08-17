<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Content_View
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Content_View extends Gene_BlueFoot_Block_Entity_Content_Abstract
{
    /**
     * Builds meta data and breadcrumbs
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $app = $this->getContentApp();
        $entity = $this->getEntity();
        $currentTerm = $this->getEntityTerm();


        if(!Mage::registry('bluefoot_layout_view_initialised') && Mage::registry('bluefoot_page_type') == 'entity_view') {

            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            /**
             * @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs
             */
            $breadcrumbs->addCrumb('home', array('label' => Mage::helper('cms')->__('Home'), 'title' => Mage::helper('cms')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));

            if ($app) {
                $breadcrumbs->addCrumb('bluefoot_app', array('label' => $app->getTitle(), 'title' => $app->getTitle(), 'link' => $app->getAppUrl()));
            }

            if ($currentTerm && $currentTerm->getId()) {
                $this->_buildTermBreadcrumb($currentTerm, $breadcrumbs);
            }

            $breadcrumbs->addCrumb('bluefoot_app_entity', array('label' => $entity->getTitle(), 'title' => $entity->getTitle()));

            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {

                $title = $entity->getMetaTitle() ? $entity->getMetaTitle() : $entity->getTitle();
                if ($title) {
                    $headBlock->setTitle($title);
                }
                $keyword = $entity->getMetaKeyword();
                if ($keyword) {
                    $headBlock->setKeywords($keyword);
                } else {
                    $headBlock->setKeywords($entity->getName());
                }
                $description = $entity->getMetaDescription();
                if ($description) {
                    $headBlock->setDescription(($description));
                }
                $params = array('_ignore_category' => true, '_nosid' => true);
                $headBlock->addLinkRel('canonical', $entity->getUrlModel()->getEntityUrl($entity, $params));

            }

            //Used to ensure not run more than once as block gets re-used
            Mage::register('bluefoot_layout_view_initialised', 1);
        }

        return parent::_prepareLayout();
    }

    /**
     * Recursive function used for building nested term breadcrumbs
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @param Mage_Page_Block_Html_Breadcrumbs $breadcrumbs
     * @param int $level
     * @return $this
     */
    protected function _buildTermBreadcrumb(Gene_BlueFoot_Model_Taxonomy_Term $term, Mage_Page_Block_Html_Breadcrumbs $breadcrumbs, $level = 0)
    {
        if($parentTerm = $term->getParentTerm()){
            $this->_buildTermBreadcrumb($parentTerm, $breadcrumbs, $level+1);
        }
        $breadcrumbs->addCrumb('bluefoot_app_entity_term_main_' . $level, array('label' => $term->getTitle(), 'title' => $term->getTitle(), 'link' => $term->getTermUrl()));

        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_Entity|null
     */
    public function getEntity()
    {
        return Mage::registry('current_genecms_entity');
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy_Term|null
     */
    public function getEntityTerm()
    {
        return Mage::registry('current_gemecms_term');
    }

    /**
     * @return Gene_BlueFoot_Model_App
     */
    public function getContentApp()
    {
        $app = $this->getEntity()->getContentApp();
        return $app;
    }

    public function getEntityTaxonomies()
    {
        $app = $this->getContentApp();
        $taxonomies = $app->getTaxonomies();
        $currentTermIds = $this->getEntity()->getTaxonomyTermIds();

        if(!is_array($currentTermIds) || count($currentTermIds) < 1){
            $currentTermIds = array('null');
        }

        foreach($taxonomies as $taxonomyId => $taxonomy){
            /**
             * @var $taxonomy Gene_BlueFoot_Model_Taxonomy
             */
            $terms = $taxonomy->getTerms();

            $terms->addFieldToFilter('entity_id', array('in' => $currentTermIds));


            foreach($terms as $termId => $term){
                if(in_array($termId, $currentTermIds)){
                    $term->setBelongsToEntity(true);
                    $taxonomy->setHasTerms(true);
                }
            }
        }

        return $taxonomies;
    }

    public function getTaxonomies()
    {
        $app = $this->getContentApp();
        $taxonomies = $app->getTaxonomies();
        $currentTermIds = $this->getEntity()->getTaxonomyTermIds();

        if(!is_array($currentTermIds) || count($currentTermIds) < 1){
            $currentTermIds = array('null');
        }

        foreach($taxonomies as $taxonomyId => $taxonomy){
            /**
             * @var $taxonomy Gene_BlueFoot_Model_Taxonomy
             */
            $terms = $taxonomy->getTerms();

            foreach($terms as $termId => $term){
                if(in_array($termId, $currentTermIds)){
                    $term->setBelongsToEntity(true);
                    $taxonomy->setBelongsToEntity(true);
                }
            }
        }

        return $taxonomies;
    }


}