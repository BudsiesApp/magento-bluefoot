<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Type_Collection
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init("gene_bluefoot/type");
    }

    /**
     * @param $contentType
     * @return $this
     */
    public function addContentTypeFilter($contentType)
    {
        $this->addFieldToFilter('content_type', $contentType);
        return $this;
    }

    /**
     * @param $typeIds
     * @return $this
     */
    public function addIdFilter($typeIds)
    {
        if(!is_array($typeIds)){
            $typeIds = array($typeIds);
        }

        $this->addFieldToFilter('type_id', $typeIds);
        return $this;
    }
}
