<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Type_Group_Collection
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */ 
class Gene_BlueFoot_Model_Resource_Type_Group_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gene_bluefoot/type_group');
    }

    /**
     * Convert the collection into an option array
     *
     * @return array
     */
    public function toOptionArray()
    {
         return $this->_toOptionArray('group_id', 'name');
    }

    /**
     * Convert the collection into an option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('group_id', 'name');
    }

    /**
     * Return an array of page builder data
     *
     * @return array
     */
    public function toPageBuilderArray()
    {
        $array = array();

        foreach ($this->getItems() as $item) {
            $array[$item->getId()] = array(
                'icon' => $item->getIcon(),
                'name' => $item->getName(),
                'sort' => $item->getSortOrder()
            );
        }

        return $array;

    }

}