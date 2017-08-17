<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Default
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Default extends Mage_Core_Block_Template
{
    /**
     * @return Gene_BlueFoot_Model_Entity|null
     */
    public function getEntity()
    {
        return $this->getData('entity');
    }

    /**
     * Array of directions, used for the metrics
     * @var array
     */
    protected $_order = array('top', 'right', 'bottom', 'left');

    /**
     * Return the attribute text from the entity
     *
     * @param $key
     * @return null
     */
    protected function getAttributeText($key)
    {
        if($this->getEntity()
            && $this->getEntity()->getId()
            && $this->getEntity()->getResource()->getAttribute($key)
            && $this->getEntity()->getResource()->getAttribute($key)->getFrontend()
        ) {
            return $this->getEntity()->getResource()->getAttribute($key)->getFrontend()->getValue($this->getEntity());
        }
        return null;
    }

    /**
     * Convert string to url/css class format
     * @param bool|false $string
     * @return bool
     */
    public function convertToCssClass($string = false)
    {
        if ($string) {
            return Mage::getModel('catalog/product_url')->formatUrlKey($string);
        }
        return false;
    }

    /**
     * Does the entity have child entities for a specific field
     *
     * @param $field
     *
     * @return bool
     */
    public function hasChildEntities($field)
    {
        $structure = $this->getStructure();
        return ($structure && is_array($structure) && isset($structure['children']) && isset($structure['children'][$field]));
    }

    /**
     * Return the child entities of the entity
     *
     * @param $field
     *
     * @return bool|\Varien_Data_Collection
     * @throws \Exception
     */
    public function getChildEntities($field)
    {
        $structure = $this->getStructure();
        if ($structure && is_array($structure) && isset($structure['children']) && isset($structure['children'][$field])) {
            $children = $structure['children'][$field];
            $childCollection = new Varien_Data_Collection();

            /* @var $renderer Gene_BlueFoot_Model_Stage_Render */
            $renderer = Mage::getSingleton('gene_bluefoot/stage_render');

            // Iterate through the children and build up the blocks
            foreach($children as $child) {
                $block = $renderer->buildEntityBlock($child);
                if ($block) {
                    $childCollection->addItem($block);
                }
            }

            return $childCollection;
        }

        return false;
    }

    /**
     * Return the count of child entities
     *
     * @param $field
     *
     * @return int
     */
    public function getChildEntitiesCount($field)
    {
        if ($this->hasChildEntities($field)) {
            $structure = $this->getStructure();
            return count($structure['children'][$field]);
        }

        return 0;
    }


    /**
     * Function to return css classes as a well formatted string
     * @return string
     */
    public function getCssAttributes()
    {
        $html = 'bluefoot-entity';

        // Add Align class
        $align = '';
        if ($align = $this->getEntity()->getAlign()) {
            $align = 'bluefoot-align-' . $align;
        }

        // Build and array of classes from the entity, the block and the alignment
        $classes = $this->parseCss($this->getCssClasses() . ' ' . $align . ' ' . $this->getEntity()->getCssClasses());

        if (!empty($classes)) {

            // Loop through all the classes
            foreach($classes as $class) {
                $html .= ' ' . $class;
            }
        }
        return $html;
    }


    /**
     * Convert classes to an array with only unique values
     * @param bool|false $string
     * @return array
     */
    public function parseCss($string = false)
    {
        $array = array();
        if($string) {
            $array = explode(' ', trim($string));
        }
        return array_unique(array_filter($array));
    }



    /**
     * Function to build up the style attributes of a block
     * @return string
     */
    public function getStyleAttributes()
    {
        if ($this->getStyles() || $this->parseMetrics()) {
            $html = ' style="';
            $html .= $this->getStyles() . $this->parseMetrics();
            $html .= '"';
            return $html;
        }
        return '';
    }


    /**
     * Function to return the metrics as a useful string
     * @return string
     */
    public function parseMetrics()
    {
        $html = '';
        if($this->getEntity() && $this->getEntity()->getMetric()) {

            foreach(json_decode($this->getEntity()->getMetric(), true) as $key => $string) {

                $values = explode(' ', $string);

                // Loop through all metrics and add any with values
                $i = 0; foreach ($values as $value) {
                    if ($value != '-') {
                        $html .= $key . '-' . $this->_order[$i] . ':' . $value . ';';
                    }
                    $i++;
                }
            }
        }
        return $html;
    }


}