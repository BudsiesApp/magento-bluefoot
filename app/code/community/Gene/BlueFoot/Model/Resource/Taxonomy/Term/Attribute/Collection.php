<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Taxonomy_Term_Attribute_Collection
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Taxonomy_Term_Attribute_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{

    /**
     * Join to additional table to get data from both
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
            ->where('main_table.entity_type_id=?', Mage::getModel('eav/entity')->setType('gene_bluefoot_taxonomy_term')->getTypeId())
            ->join(
                array('additional_table' => $this->getTable('gene_bluefoot/eav_attribute')),
                'additional_table.attribute_id=main_table.attribute_id'
            );
        return $this;
    }

    protected function _construct()
    {
        $this->_init('gene_bluefoot/attribute');
    }


    /**
     * @param int|Mage_Eav_Model_Entity_Type $typeId
     * @return $this
     */
    public function setEntityTypeFilter($typeId)
    {
        //Filter already added in _initSelect()
        return $this;
    }

    /**
     * @return $this
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('additional_table.is_visible', 1);
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {        
        return parent::_afterLoad();
    }
}
