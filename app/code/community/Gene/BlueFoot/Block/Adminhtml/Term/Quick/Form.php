<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Quick_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Quick_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $term = $this->_getCurrentEntity();
        $taxonomy = $this->_getCurrentTaxonomy();

        $urlParams = array(
            'taxonomy_id' => $taxonomy->getId()
        );
        if($contentEntity = Mage::registry('content_entity')){
            $urlParams['content_entity_id'] = $contentEntity->getId();
        }

        $form = new Varien_Data_Form(array (
                'id' => 'term_quick_add',
                'action' => $this->getUrl('*/genecms_taxonomyterm/quickSave', $urlParams),
                'method' => 'post', 
                'enctype' => 'multipart/form-data' ));

        $form->setUseContainer(true);
        $this->setForm($form);



        $currentAttributeSet = $taxonomy->getAttributeSet();
        $currentAttributeSetId = $taxonomy->getAttributeSetId();

        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($currentAttributeSetId)
            ->setSortOrder();

        $i=1;
        foreach($groups as $fieldGroup){
            $attributes = array();
            $attrCollection = Mage::getResourceModel('gene_bluefoot/taxonomy_term_attribute_collection')
                ->addVisibleFilter()
                ->setAttributeGroupFilter($fieldGroup->getId())
                ->setAttributeSetFilter($currentAttributeSetId)
                ->addSetInfo();
            foreach($attrCollection as $key => $attribute){
                $attribute->unsIsVisible();
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }

            $fieldset = $form->addFieldset('term_fieldset_' . $fieldGroup->getId(), array (
                'legend' => Mage::helper('gene_bluefoot')->__($fieldGroup->getAttributeGroupName()),
                'class' => 'fieldset-wide',
                'expanded' => false
            ));

            if ($term->getEntityId()) {
                $fieldset->addField('entity_id', 'hidden', array (
                    'name' => 'entity_id' ));
            }

            $this->_setFieldset($attributes, $fieldset, array());

            if($i==1){
                $currentId = $term->getId();
                $taxonomyTerms = $taxonomy->getTerms('*', array($currentId));
                $parentOptions = array(
                    0 => 'None'
                );
                foreach($taxonomyTerms as $taxonomyTerm){
                    if($taxonomyTerm->getId() == $term->getId()){
                        continue;
                    }
                    $parentOptions[$taxonomyTerm->getId()] = $taxonomyTerm->getPathTitle(' > ');
                }

                if($taxonomy->getIsNestable()) {
                    $fieldset->addField('parent_id', 'select', array(
                        'name' => 'parent_id',
                        'label' => 'Parent',
                        'options' => $parentOptions
                    ), 'title');
                }

                $fieldset->addField('position', 'text', array(
                    'name' => 'position',
                    'label' => 'Sort Order',
                    'class' => 'validate-number',
                    'value' => 0
                ), 'title');
            }

            $i++;
            //break; //only display the first group
        }

        $form->addValues($term->getData());

        return parent::_prepareForm();
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

    public function _getCurrentTaxonomy()
    {
        return Mage::registry('current_taxonomy');
    }
}
