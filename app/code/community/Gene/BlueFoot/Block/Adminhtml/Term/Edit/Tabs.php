<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Edit_Tabs
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('term_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Term Information');
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();

        $term = $this->_getCurrentEntity();
        $taxonomy = $term->getTaxonomy();

        $currentAttributeSet = $taxonomy->getAttributeSet();
        $currentAttributeSetId = $taxonomy->getAttributeSetId();

        /**
         * @var $currentAttributeSet Mage_Eav_Model_Entity_Attribute_Set
         */

        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($currentAttributeSetId)
            ->setSortOrder();


        $firstAttTab = true;
        foreach($groups as $fieldGroup){

            $groupBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_term_edit_tab_dynamic');
            $groupBlock->setGroup($fieldGroup);
            $groupBlock->setIsFirst($firstAttTab);

            $this->addTab(
                'field_group_' . $fieldGroup->getId(),
                array(
                    'label' => $fieldGroup->getAttributeGroupName(),
                    'title' => $fieldGroup->getAttributeGroupName(),
                    'content' => $groupBlock->toHtml(),
                    'active' => $firstAttTab
                )
            );

            $firstAttTab = false;
        }
        
        return $return;
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy_Term|bool
     */
    protected function _getCurrentEntity()
    {
        if(Mage::registry('current_term')){
            return Mage::registry('current_term');
        }

        return false;
    }
}
