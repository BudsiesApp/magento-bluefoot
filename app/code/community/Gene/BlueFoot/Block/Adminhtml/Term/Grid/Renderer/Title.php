<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Grid_Renderer_Title
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Grid_Renderer_Title extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $title =  $row->getData($this->getColumn()->getIndex());

        if($pathTitle = $row->getPathTitle(' > ')){
            $title = $pathTitle;
        }

        return $title;

    }

}