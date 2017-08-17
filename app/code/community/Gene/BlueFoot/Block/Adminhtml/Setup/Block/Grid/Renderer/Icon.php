<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Grid_Renderer_Icon
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Grid_Renderer_Icon extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $icon =  'fa fa-' . $row->getData($this->getColumn()->getIndex());
        return '<i style="font-size:24px;" class = "'.$icon.'">'.'</i>';
    }
}