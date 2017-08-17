<?php

/**
 * Class Gene_BlueFoot_Model_Entity_Content_Layer
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Entity_Content_Layer extends Varien_Object
{
    protected $_entityCollection;

    public function getEntityCollection()
    {
        $currentType = $this->getCurrentContentType();

        $collection = Mage::getModel('gene_bluefoot/entity')->getCollection();
        $collection->addContentTypeFilter('content');

        if($currentType){
            $collection->addFieldToFilter('attribute_set_id', $currentType->getAttributeSetId());
        }

        $collection->addAttributeToSelect('*');

        return $collection;

    }

    /**
     * @return Gene_BlueFoot_Model_Type|bool
     */
    public function getCurrentContentType()
    {
        if($contentType = Mage::registry('current_content_type')){
            return $contentType;
        }

        return false;
    }
}