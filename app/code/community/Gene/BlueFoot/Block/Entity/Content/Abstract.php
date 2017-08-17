<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Content_Abstract
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Content_Abstract extends Mage_Core_Block_Template
{

    /**
     * @param Gene_BlueFoot_Model_Taxonomy $taxonomy
     * @param int $count
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection|mixed
     */
    public function getTaxonomyTerms(Gene_BlueFoot_Model_Taxonomy $taxonomy, $count = null)
    {
        $taxonomy->setStoreId(Mage::app()->getStore()->getId());
        $terms = $taxonomy->getTerms();
        $terms->addAttributeToFilter('status', 1);

        if(is_numeric($count)){
            $terms->setPageSize($count);
            $terms->setCurPage(1);
        }

        return $terms;
    }

    /**
     * @param Gene_BlueFoot_Model_Taxonomy $taxonomy
     * @param null $count
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection
     */
    public function getParentTaxonomyTerms(Gene_BlueFoot_Model_Taxonomy $taxonomy, $count = null)
    {
        $terms = $this->getTaxonomyTerms($taxonomy, $count);
        $terms->addFieldToFilter('parent_id', 0);

        return $terms;
    }
}