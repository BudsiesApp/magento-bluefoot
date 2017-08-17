<?php

/**
 * Class Gene_BlueFoot_Model_Entity_Frontend_Block
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Entity_Frontend_Block extends Gene_BlueFoot_Model_Entity_Frontend_Abstract
{
    protected $_defaultRenderer = 'gene_bluefoot/entity_pagebuilder_block_default';
    protected $_defaultTemplate = 'gene/bluefoot/pagebuilder/blocks/core/basic/blank.phtml';

    /**
     * Return the rendering block for the entity
     *
     * @return mixed
     * @throws \Exception
     */
    public function getRenderBlock()
    {
        $configHelper = $this->getConfigHelper();
        $blockName = $this->_defaultRenderer;
        if ($rendererIdentfier = $this->getEntityType()->getRenderer()){
            if ($blockClass = $configHelper->getBlockConfig('renderers/' . $rendererIdentfier . '/class')) {
                $blockName = (string) $blockClass;
            }
        }

        $block = Mage::app()->getLayout()->createBlock($blockName);
        if(!$block){
            throw new Exception('Failed to load template block ('.$blockName.') for entity.');
        }
        $block->setEntity($this->getEntity());

        return $block;
    }

    /**
     * Return the front-end view template
     *
     * @return string|\Varien_Simplexml_Element
     */
    public function getViewTemplate()
    {
        $configHelper = $this->getConfigHelper();
        $templateFile = (string) $this->_defaultTemplate;
        if($templateIdentfier = $this->getEntityType()->getItemViewTemplate()){
            $templateFile = (string) $configHelper->getBlockConfig('templates/'.$templateIdentfier . '/file');
        }

        return $templateFile;
    }
}