<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Structural_Row
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Structural_Row extends Gene_BlueFoot_Block_Entity_Pagebuilder_Structural_Abstract
{

    /**
     * Base path of templates
     * @var string
     */
    protected $_path = 'gene/bluefoot/pagebuilder/structural/core/';

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($template = $this->getFormData('template')) {
            $this->setTemplate($this->_path . $template);
        }

        return parent::_toHtml();
    }
}