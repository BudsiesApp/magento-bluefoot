<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Source_Entity_Child
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Source_Entity_Child extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    protected $_possibleEntities;

    /**
     * @return Gene_BlueFoot_Model_Type|bool
     */
    public function getAllowedType()
    {
        $attribute = $this->getAttribute();

        /**
         * @var $attribute Gene_BlueFoot_Model_Attribute
         */

        $additionalData = $attribute->getAdditional();

        $typeId = (isset($additionalData['entity_allowed_block_type']) ? $additionalData['entity_allowed_block_type'] : false);

        $typeModel = Mage::getModel('gene_bluefoot/type')->load($typeId);

        if($typeModel->getId()){
            return $typeModel;
        }

        return false;
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Entity_Collection
     * @throws Mage_Core_Exception
     */
    public function getPossibleEntities()
    {
        if(is_null($this->_possibleEntities)) {
            $typeModel = $this->getAllowedType();

            if( !$typeModel ) {
                return array();
            }

            if ($typeModel) {
                if ($typeModel->getId() && $attributeSet = $typeModel->getAttributeSet()) {
                    $attributeSetId = $attributeSet->getId();

                    if ($attributeSetId) {
                        $entities = Mage::getModel('gene_bluefoot/entity')->getCollection();
                        $entities->addAttributeToSelect('title', 'left');
                        $entities->addFieldToFilter('attribute_set_id', array('eq' => $attributeSetId));

                        $this->_possibleEntities = $entities;
                    } else {
                        $this->_possibleEntities = array();
                    }
                }
            } else {
                $this->_possibleEntities = array();
            }
        }


        return $this->_possibleEntities;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $entities = $this->getPossibleEntities();
            $entityValues = array();

            if($entities) {
                foreach ($entities as $entity) {
                    $label = ($entity->getTitle() ? $entity->getTitle() : '{no title}') . ' [Type: ' . $entity->getEntityType()->getName() . ', ID: ' . $entity->getId() . ']';
                    $entityValues[] = array('label' => $label, 'value' => $entity->getId());
                }
            }

            $this->_options = $entityValues;
        }

        return $this->_options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * @param int|string $value
     * @return bool
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

}