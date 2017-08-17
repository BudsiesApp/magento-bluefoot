<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Block_Types
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Block_Types extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'gene/bluefoot/setup/installer/new/block-types.phtml';

    protected function _getBlockDetails()
    {
        $blockCollection = Mage::getModel('gene_bluefoot/type')->getCollection()
            ->addContentTypeFilter('block');

        $blockCollection->setOrder('sort_order', Varien_Data_Collection_Db::SORT_ORDER_ASC);

        return $blockCollection;

    }
    public function _getBlockAttributes(){

        $blockCollection = Mage::getModel('gene_bluefoot/type')->getCollection()->addContentTypeFilter('block');

        foreach ($blockCollection as $block) {

            $blockAttributes = $block->getAllAttributes();

            $blockAttributesArray = array();
            foreach ($blockAttributes as $bAttr) {
                $blockAttributeCodes[] = $bAttr->getAttributeCode();
            }

            $attributeCollection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter()->addFieldToFilter('attribute_code', array('in' => $blockAttributeCodes));


            return $attributeCollection;

        }
        return array();
    }
}