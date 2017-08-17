<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Tabs
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Content Information');
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $model = Mage::registry('entity');

        /**
         * @var $model Gene_BlueFoot_Model_Entity
         */

        $return = parent::_prepareLayout();

        /**
         * @var $currentAttributeSet Mage_Eav_Model_Entity_Attribute_Set
         */
        $currentAttributeSet = Mage::registry('attribute_set');
        $currentAttributeSetId = $currentAttributeSet->getId();

        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($currentAttributeSetId)
            ->setSortOrder();


        if($model->getId()){
//            $this->addTab(
//                'main_section',
//                array(
//                    'label' => 'Information',
//                    'title' => 'Information',
//                    'content' => $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_edit_tab_main')->toHtml(),
//                    'active' => false,
//                )
//            );
        }


        $firstAttTab = true;
        foreach($groups as $fieldGroup){

            $groupBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_edit_tab_dynamic');
            $groupBlock->setGroup($fieldGroup);

            $this->addTab(
                'field_group_' . $fieldGroup->getId(),
                array(
                    'label' => $fieldGroup->getAttributeGroupName(),
                    'title' => $fieldGroup->getAttributeGroupName(),
                    'content' => $groupBlock->toHtml(),
                    'active' => $firstAttTab,
                )
            );

            $firstAttTab = false;
        }

        $model = Mage::registry('entity');
        /**
         * @var $model Gene_BlueFoot_Model_Entity
         */

        $contentType = $model->getContentType();
        $contentApp = $contentType->getContentApp();

        $taxonomies = $contentApp->getTaxonomies();

        foreach ($taxonomies as $taxonomy) {

            $taxonomyBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_edit_tab_taxonomy');
            $taxonomyBlock->setTaxonomy($taxonomy);

            $this->addTab(
                'taxonomy_' . $taxonomy->getId(),
                array(
                    'label' => $taxonomy->getTitle(),
                    'title' => $taxonomy->getTitle(),
                    'content' => $taxonomyBlock->toHtml(),
                )
            );

        }
        
        return $return;
    }
}
