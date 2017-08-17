<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Abstract
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 *
 * @method Gene_BlueFoot_Model_Attribute_Data_Abstract setEntity($entity)
 * @method Gene_BlueFoot_Model_Entity getEntity()
 * @method Gene_BlueFoot_Model_Attribute_Data_Abstract setAttribute($attribute)
 * @method Gene_BlueFoot_Model_Resource_Eav_Attribute getAttribute()
 */
class Gene_BlueFoot_Model_Attribute_Data_Abstract extends Mage_Core_Model_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{
    /**
     * Return an array to be parsed as JSON for the page builder system
     *
     * @return array
     */
    public function asJson()
    {
        return array();
    }
}