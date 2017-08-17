<?php

/**
 * Class Gene_BlueFoot_Model_Taxonomy_Term_Attribute_Source_Columntype
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Taxonomy_Term_Attribute_Source_Columntype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                '' => 'Use Default',
                'grid' => 'Grid',
                'list' => 'List'
            );
        }
        return $this->_options;
    }
}