<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Entity_Child
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Entity_Child extends Varien_Data_Form_Element_Multiselect
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
        $attributeModel = $this->getEntityAttribute();
        $attributeName = $this->getData('name');

        $entityValues = array();

        $excludeId = false;
        $currentEntity = Mage::registry('entity');
        if($currentEntity && $currentEntity->getId()){
            $excludeId = $currentEntity->getId();
        }

        if(!$attributeModel) {
            $attributeModel = Mage::getModel('gene_bluefoot/attribute')
                ->setEntityTypeId($this->_getEntityType()->getId())
                ->load($attributeName, 'attribute_code');
        }

        $attrSource = $attributeModel->getSource();
        if($attrSource && method_exists($attrSource, 'getPossibleEntities')) {
            $entities = $attrSource->getPossibleEntities();

            foreach ($entities as $entity) {
                $entityValues[] = array('label' => ' ' . $entity->getTitle() . ' [Type: ' . $entity->getEntityType()->getName() . ', ID: ' . $entity->getId() . ']', 'value' => $entity->getId());
            }
        }


        return $entityValues;
    }

    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
    }
}