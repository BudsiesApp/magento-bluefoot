<?php
/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_Staticblock
 *
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_Staticblock extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{
    /**
     * Return a static block from the given field
     *
     * @return mixed
     */
    public function getBlock()
    {
        return Mage::getModel('cms/block')->load($this->getEntity()->getData($this->getAttribute()->getData('attribute_code')));
    }

    /**
     * Return an array of basic product data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {
        $block = $this->getBlock();

        // Render any page builder blocks within the static block
        $content = Mage::getModel('gene_bluefoot/stage_render')->renderPlaceholders($block->getContent());

        return array(
            'title' => $block->getTitle(),
            'identifier' => $block->getIdentifier(),
            'content' => $content
        );
    }

}