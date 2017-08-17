<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Notifications
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Return a message information the admin they need to update their module
     *
     * @return string
     */
    public function getMessage()
    {
        if (Mage::getStoreConfig('gene_bluefoot/version/needs_update')) {
            return Mage::helper('gene_bluefoot')->__('<strong>BlueFoot is currently outdated</strong>, please %s to view more information on how to update.', '<a href="' . Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit', array('section' => 'bluefoot')) . '">click here</a>');
        }
    }
}