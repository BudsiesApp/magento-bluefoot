<?php

/**
 * Class Gene_BlueFoot_Model_App_Attribute_Source_Menuposition
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_App_Attribute_Source_Menuposition extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Available options for menu position
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                '' => 'None',
                'start' => 'Start',
                'last' => 'End'
            );
        }
        return $this->_options;
    }
}