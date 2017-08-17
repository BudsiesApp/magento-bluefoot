<?php
/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Map
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Map extends Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Default
{


    /**
     * @return Gene_BlueFoot_Model_Entity|null
     */
    public function getEntity()
    {
        return $this->getData('entity');
    }


    /**
     * Get the map
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getMap()
    {
        /* @var $dataModel Gene_BlueFoot_Model_Attribute_Data_Widget_Map */
        $dataModel = $this->getEntity()->getResource()->getAttribute('map')->getDataModel($this->getEntity());
        if ($dataModel instanceof Gene_BlueFoot_Model_Attribute_Data_Widget_Map && method_exists($dataModel, 'getMap')) {
            return $dataModel->getMap();
        }
        return false;
    }

    /**
     * Function to get map data as an object
     * @return bool|Varien_Object
     */
    public function getMapData()
    {
        $map = $this->getEntity()->getMap();
        if ($map) {
            // Convert the map data into separate variables
            list($long, $lat, $zoom) = explode(',', $map);

            return new Varien_Object(array(
                'long' => $long,
                'lat' => $lat,
                'zoom' => (int) $zoom
            ));
        }
        return false;
    }
}