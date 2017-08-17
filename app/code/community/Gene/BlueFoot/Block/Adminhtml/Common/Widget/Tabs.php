<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Common_Widget_Tabs
 * @author Conor Farrell <conor@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Common_Widget_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        $this->setTemplate('gene/bluefoot/common/widget/tabs.phtml');
    }
}