<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_Map
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_Map extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{


    /**
     * Return Map data from the given field
     *
     * @return mixed
     */
    public function getMap()
    {
        return $this->getEntity()->getData($this->getAttribute()->getData('attribute_code'));
    }

    /**
     * Return an array of basic map data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {
        $map = $this->getMap();
        list($long, $lat, $zoom) = explode(',', $map);

        return array(
            'long' => $long,
            'lat' => $lat,
            'zoom' => (int) $zoom
        );
    }

}