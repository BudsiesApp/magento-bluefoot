<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Content_List
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Content_List extends Mage_Core_Block_Template
{

    /**
     * @return Gene_BlueFoot_Model_Entity_Content_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('genecms_entity_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('gene_bluefoot/entity_content_layer');
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Entity_Collection
     */
    protected function _getEntityCollection()
    {
        $layer = $this->getLayer();
        $collection = $layer->getEntityCollection();

        return $collection;
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Entity_Collection
     */
    public function getEntityCollection()
    {
        return $this->_getEntityCollection();
    }

    public function addAttribute($code)
    {
        $this->_getEntityCollection()->addAttributeToSelect($code);
        return $this;
    }

}