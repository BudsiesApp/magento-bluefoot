<?php

/**
 * Class Gene_BlueFoot_Helper_App
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_App extends Mage_Core_Helper_Abstract
{
    /**
     * @return array
     */
    public function getDisplayModeArray()
    {
        $array = array(
            'content_all' => 'All Content',
            'content_split' => 'Split Content - by content type',
            'no_content' => 'Description Only '
//            'taxonomy_terms' => 'Taxonomy Terms Only',
//            'taxonomy_terms_content_all' => 'Taxonomy Terms & List',
//            'taxonomy_terms_content_split' => 'Taxonomy Terms & Split List',
        );

        return $array;
    }

    public function getDateTimeFields()
    {
        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection');
        $collection->addFieldToFilter('backend_type', 'datetime');
        return $collection;
    }
}