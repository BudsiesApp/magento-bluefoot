<?php

/**
 * Class Gene_BlueFoot_Block_App_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_App_Abstract extends Mage_Core_Block_Template
{
    /**
     * @return Gene_BlueFoot_Model_App
     */
    public function getCurrentApp()
    {
        return Mage::registry('current_genecms_app');
    }

    public function getCurrentTaxonomyTerm()
    {
        return Mage::registry('current_genecms_term');
    }

    public function getContentTypes()
    {
        $app = $this->getCurrentApp();
        $contentTypeIds = $app->getContentTypeIds();

        if(is_array($contentTypeIds) && count($contentTypeIds)){
            $contentTypes = Mage::getModel('gene_bluefoot/type')->getCollection();
            $contentTypes->addFieldToFilter('type_id', array('in' => $contentTypeIds));
            return $contentTypes;
        }

        return false;
    }

    public function getEntityUrl(Gene_BlueFoot_Model_Entity $entity)
    {
        if($term = $this->getCurrentTaxonomyTerm()){
            $entity->setCurrentTermId($term->getId());
        }
        return $entity->getEntityUrl();
    }

    public function getTermUrl(Gene_BlueFoot_Model_Taxonomy_Term $term)
    {
        return $term->getTermUrl();
    }

    /**
     * @param Gene_BlueFoot_Model_App $app
     * @return mixed
     */
    public function getAppUrl(Gene_BlueFoot_Model_App $app)
    {
        return $app->getAppUrl();
    }
}



