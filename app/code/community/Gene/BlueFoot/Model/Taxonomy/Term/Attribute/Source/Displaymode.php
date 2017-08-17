<?php

/**
 * Class Gene_BlueFoot_Model_Taxonomy_Term_Attribute_Source_Displaymode
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Taxonomy_Term_Attribute_Source_Displaymode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                '' => 'Use Default',
                'content_all' => 'All Content',
                'term_list' => 'Child Terms',
                'term_list_content' => 'Child Terms with content',
                'content_split' => 'Split Content - by content type',
                'no_content' => 'Description Only'
            );
        }
        return $this->_options;
    }
}