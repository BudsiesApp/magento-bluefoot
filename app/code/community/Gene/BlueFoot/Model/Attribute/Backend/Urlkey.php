<?php

/**
 * Class Gene_BlueFoot_Model_Atrtibute_Backend_Urlkey
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Build a URL key from the title
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false || is_null($urlKey)) {
            return $this;
        }

        if ($urlKey=='') {
            $urlKey = $object->getTitle();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }
}