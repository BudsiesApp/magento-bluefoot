<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Attribute_Set_Main
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Attribute_Set_Main extends Mage_Adminhtml_Block_Template
{
    protected $_tempSetId;

    /**
     * Initialize template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('gene/bluefoot/setup/type/attributes.phtml');
    }

    /**
     * Prepare Global Layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main
     */
    protected function _prepareLayout()
    {
        $setId = $this->_getSetId();

        $this->setChild('group_tree',
            $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_attribute_set_main_tree_group')
//            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_tree_group')
        );

        $this->setChild('edit_set_form',
            $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_attribute_set_main_formset')
            //$this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset')
        );

        //if($this->getContentType() != 'block'){
            $this->setChild('delete_group_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label'     => Mage::helper('gene_bluefoot')->__('Delete Selected Tab'),
                    'onclick'   => 'editSet.submit();',
                    'class'     => 'delete'
                )));

            $this->setChild('add_group_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label'     => Mage::helper('gene_bluefoot')->__('Add New'),
                    'onclick'   => 'editSet.addGroup();',
                    'class'     => 'add'
                )));

            /*$currentType = Mage::registry('type_data');
            if($currentType && $currentType->getId()){
                $this->setChild('save_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                        'label'     => Mage::helper('gene_bluefoot')->__('Save Field Groups Only'),
                        'onclick'   => 'editSet.save();',
                        'class'     => 'save'
                    )));
            }*/
        //}


        return parent::_prepareLayout();
    }

    /**
     * Retrieve Attribute Set Group Tree HTML
     *
     * @return string
     */
    public function getGroupTreeHtml()
    {
        return $this->getChildHtml('group_tree');
    }

    /**
     * Retrieve Attribute Set Edit Form HTML
     *
     * @return string
     */
    public function getSetFormHtml()
    {
        return $this->getChildHtml('edit_set_form');
    }

    /**
     * Retrieve Block Header Text
     *
     * @return string
     */
    protected function _getHeader()
    {
        return Mage::helper('catalog')->__("Edit Content type: '%s'", $this->_getAttributeSet()->getAttributeSetName());
    }

    /**
     * Retrieve Attribute Set Save URL
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/*/saveGroups', array('id' => $this->_getSetId()));
    }

    /**
     * Retrieve Attribute Set Group Save URL
     *
     * @return string
     */
    public function getGroupUrl()
    {
        return $this->getUrl('*/*/saveGroups', array('id' => $this->_getSetId()));
    }

    
    /**
     * Retrieve Attribute Set Group Tree as JSON format
     *
     * @return string
     */
    public function getGroupTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();
        
        $entity = Mage::getModel('gene_bluefoot/entity');

        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();

        if(!$setId){
            $groups = new Varien_Data_Collection();

            if($this->getContentType() =='block'){
                $groupModel = Mage::getModel('eav/entity_attribute_group');
                $groupModel->setAttributeGroupName('General');
                $groupModel->setSortOrder(1);
                $groupModel->setDefaultId(1);
                $groups->addItem($groupModel);
            }else{
                $typeModel = Mage::getModel('gene_bluefoot/type');
                $defaultAttributeSet = $typeModel->getDefaultAttributeSet();

                $this->_tempSetId = $defaultAttributeSet->getId();

                $skeletonGroups = Mage::getModel('eav/entity_attribute_group')
                    ->getResourceCollection()
                    ->setAttributeSetFilter($defaultAttributeSet->getId());

                //set sort order
                $skeletonGroups->getSelect()->order('attribute_group_id ' . Varien_Data_Collection_Db::SORT_ORDER_ASC);

                foreach($skeletonGroups as $_sGroup){
                    $group = clone $_sGroup;
                    $groups->addItem($group);
                }
            }


            //die('no set ID');
        }
          

        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $item = array();
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;

            $nodeChildren = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter()
                ->setAttributeGroupFilter($node->getId())
                ->setOrder('sort_order', 'ASC')
                ->load();

            if ($nodeChildren->getSize() > 0) {
                $item['children'] = array();
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */
                    $attr = array(
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => (!$child->getIsUserDefined()) ? 'system-leaf' : 'leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => $child->getIsUserDefined(),
                        'entity_id'         => $child->getEntityAttributeId()
                    );

                    if($fLabel = $child->getFrontend()->getLabel()){
                        $attr['text'] = $fLabel . ' [' . $child->getAttributeCode() . ']';
                    }

                    $item['children'][] = $attr;
                }
            }

            $items[] = $item;
        }


        return Mage::helper('core')->jsonEncode($items);
    }

    /**
     * Retrieve Unused in Attribute Set Attribute Tree as JSON
     *
     * @return string
     */
    public function getAttributeTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();

        if(!$setId && $this->_tempSetId){
            $setId = $this->_tempSetId;
        }


        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
            ->addVisibleFilter()
            ->setAttributeSetFilter($setId)
            ->load();


        $attributesIds = array('0');
        /* @var $item Mage_Eav_Model_Entity_Attribute */
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }

        $attributes = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter();

        if($setId) {
            $attributes->setAttributesExcludeFilter($attributesIds);
        }

        $attributes->load();

        foreach ($attributes as $child) {
            $attr = array(
                'text'              => $child->getAttributeCode(),
                'id'                => $child->getAttributeId(),
                'cls'               => 'leaf',
                'allowDrop'         => false,
                'allowDrag'         => true,
                'leaf'              => true,
                'is_user_defined'   => $child->getIsUserDefined(),
                'is_configurable'   => false,
                'entity_id'         => $child->getEntityId()
            );

            if($fLabel = $child->getFrontend()->getLabel()){
                $attr['text'] = $fLabel . ' [' . $child->getAttributeCode() . ']';
            }

            $items[] = $attr;
        }

        if (count($items) == 0) {
            $items[] = array(
                'text'      => Mage::helper('gene_bluefoot')->__('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            );
        }

        return Mage::helper('core')->jsonEncode($items);
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        if ($this->getIsCurrentSetDefault()) {
            return '';
        }
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Delete Group Button HTML
     *
     * @return string
     */
    public function getDeleteGroupButton()
    {
        return $this->getChildHtml('delete_group_button');
    }

    /**
     * Retrieve Add New Group Button HTML
     *
     * @return string
     */
    public function getAddGroupButton()
    {
        return $this->getChildHtml('add_group_button');
    }

    /**
     * Retrieve Rename Button HTML
     *
     * @return string
     */
    public function getRenameButton()
    {
        return $this->getChildHtml('rename_button');
    }

    /**
     * Retrieve current Attribute Set object
     *
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    protected function _getAttributeSet()
    {
        return Mage::registry('current_attribute_set');
    }

    /**
     * Retrieve current attribute set Id
     *
     * @return int
     */
    protected function _getSetId()
    {
        return $this->_getAttributeSet()->getId();
    }

    /**
     * Check Current Attribute Set is a default
     *
     * @return bool
     */
    public function getIsCurrentSetDefault()
    {
        $isDefault = $this->getData('is_current_set_default');
        if (is_null($isDefault)) {
            $defaultSetId = Mage::getModel('eav/entity_type')
                ->load(Mage::registry('entityType'))
                ->getDefaultAttributeSetId();
            $isDefault = $this->_getSetId() == $defaultSetId;
            $this->setData('is_current_set_default', $isDefault);
        }
        return $isDefault;
    }

    /**
     * Retrieve current Attribute Set object
     *
     * @deprecated use _getAttributeSet
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    protected function _getSetData()
    {
        return $this->_getAttributeSet();
    }

    /**
     * Prepare HTML
     *
     * @return string
     */
    protected function _toHtml()
    {        
        return parent::_toHtml();
    }
}
