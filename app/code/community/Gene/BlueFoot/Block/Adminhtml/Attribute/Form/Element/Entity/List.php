<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Entity_List
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Entity_List extends Varien_Data_Form_Element_Multiselect
{
    protected $_entityType = null;

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::helper('gene_bluefoot')->getEntityType();
        }
        return $this->_entityType;
    }

    public function getValues()
    {
        $attributeName = $this->getName();

        $excludeId = false;
        $currentEntity = Mage::registry('entity');
        if($currentEntity && $currentEntity->getId()){
            $excludeId = $currentEntity->getId();
        }

        $attributeModel = Mage::getModel('gene_bluefoot/attribute')
            ->setEntityTypeId($this->_getEntityType()->getId())
            ->load($attributeName, 'attribute_code');

        $attributeSetFilters = array();
        $attributeSetFiltersCode = array();
        $attrSetTypes = array();

        //Get the content types which can be selected
        $additionalData = $attributeModel->getAdditional();
        $typeIds = (isset($additionalData['entity_allowed_content_types'])? $additionalData['entity_allowed_content_types'] : array());



        $typeCollection = Mage::getModel('gene_bluefoot/type')->getCollection();
        $typeCollection->addContentTypeFilter('block');
        if(count($typeIds)){
            $typeCollection->addFieldToFilter('type_id', array('in' => $typeIds));
        }

        foreach($typeCollection as $typeModel){
            //$typeModel = Mage::getModel('gene_bluefoot/type')->load($typeId);
            if($attributeSet = $typeModel->getAttributeSet()){
                $attrSetTypes[$attributeSet->getId()] = $typeModel;
                $attributeSetFilters[] = $attributeSet->getId();
                $attributeSetFiltersCode[] = $typeModel->getIdentifier();
            }
        }

        $entityValues = array();
        $entities = Mage::getModel('gene_bluefoot/entity')->getCollection();
        $entities->addAttributeToSelect('title', 'left');

        //exclude current entity
        if($excludeId){
            $entities->addAttributeToFilter('entity_id', array('neq' => $excludeId));
        }

        if($attributeSetFilters && count($attributeSetFilters) > 0){
            $entities->addFieldToFilter('attribute_set_id', array('in' => $attributeSetFilters));
        }

        if(count($attributeSetFilters) > 1){
            foreach($entities as $entity){
                if(!isset($entityValues[$entity->getAttributeSetId()])){
                    $entityValues[$entity->getAttributeSetId()] = array(
                        'label' =>  isset($attrSetTypes[$entity->getAttributeSetId()]) ? $attrSetTypes[$entity->getAttributeSetId()]->getName() : 'Unknown Types',
                        'value' => array()
                    );
                }

                $entityValues[$entity->getAttributeSetId()]['value'][] = array(
                    'value' => $entity->getTitle() . ' [ID: '. $entity->getId() . ']'
                );

            }
        }else{
            foreach($entities as $entity){
                $entityValues[] = array('label' => ' ' . $entity->getTitle() . ' [Type: '.$entity->getEntityType()->getName() .', ID: ' . $entity->getId().']', 'value' => $entity->getId());
            }
        }

        return $entityValues;
    }

    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
    }
}