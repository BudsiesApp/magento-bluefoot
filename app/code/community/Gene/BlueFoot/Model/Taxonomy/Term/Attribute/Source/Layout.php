<?php

/**
 * Class Gene_BlueFoot_Model_Taxonomy_Term_Attribute_Source_Layout
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Taxonomy_Term_Attribute_Source_Layout extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getSingleton('page/source_layout')->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>Mage::helper('gene_bluefoot')->__('Use Default')));
        }
        return $this->_options;
    }
}
