<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Type
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Type extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_entityType = null;

    protected function _construct()
    {
        $this->_init("gene_bluefoot/type", "type_id");
    }

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType(Gene_BlueFoot_Model_Entity::ENTITY);
        }
        return $this->_entityType;
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $this->_loadAttributeSet($object);
        return parent::_afterLoad($object);
    }

    /**
     * public interface for _loadAttributeSet
     *
     * @param Gene_BlueFoot_Model_Type $type
     * @return Mage_Eav_Model_Entity_Attribute_Set
     * @throws Exception
     */
    public function loadAttributeSet(Gene_BlueFoot_Model_Type $type)
    {
        return $this->_loadAttributeSet($type);
    }

    /**
     * @param Gene_BlueFoot_Model_Type $type
     * @return Mage_Eav_Model_Entity_Attribute_Set
     * @throws Exception
     */
    protected function _loadAttributeSet(Gene_BlueFoot_Model_Type $type)
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        if($type->getId()){
            $attributeSet->load($type->getAttributeSetId());
            if(!$attributeSet->getId()){
                throw new Exception('Failed to load content type attribute set');
            }
        }else{
            $attributeSet->setEntityTypeId($this->_getEntityType()->getEntityTypeId());
        }

        $type->setAttributeSet($attributeSet);
        return $attributeSet;
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /**
         * @var $object Gene_BlueFoot_Model_Type
         */
        $this->_saveAttributeSet($object);
        return parent::_beforeSave($object);
    }

    /**
     * Save Attribute Set related to Entity Type
     *
     * @param Mage_Core_Model_Abstract $type
     * @return type
     */
    protected function _afterSave(Mage_Core_Model_Abstract $type)
    {
        $attributeSet = $type->getAttributeSet();
        $attributeSetName = $type->getName() . ' {' . $type->getIdentifier() .'}';

        if($attributeSet->getAttributeSetName() != $attributeSetName){
            $attributeSet->setAttributeSetName($attributeSetName);
            $attributeSet->save();
        }

        return parent::_afterSave($type);
    }

    /**
     * @param Gene_BlueFoot_Model_Type $type
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    protected function _saveAttributeSet(Gene_BlueFoot_Model_Type $type)
    {
        return $this->_createNewAttributeSet($type);
    }

    public function initNewAttributeSetType(Mage_Eav_Model_Entity_Attribute_Set $attributeSet, $type = 'block')
    {
        $type = 'block';
        $groups = array();
        switch($type){
            case 'block':

                $groupModel = Mage::getModel('eav/entity_attribute_group');
                $groupModel->setAttributeGroupName('General');
                $groupModel->setSortOrder(1);
                $groupModel->setDefaultId(1);
                $group[] = $groupModel;

                $attributeSet->setGroups($groups);

                break;
            case 'content':

                $typeModel = Mage::getModel('gene_bluefoot/type');
                $defaultAttributeSet = $typeModel->getDefaultAttributeSet();

                $attributeSet->initFromSkeleton($defaultAttributeSet->getId());

                break;
            default:
                throw new Exception('Unknown Type "'.$type.'"');

        }
        return $this;
    }

    /**
     * Create new attribute set if not exists otherwise ensure data is correct and re-save
     *
     * @param Gene_BlueFoot_Model_Type $type
     * @return Mage_Eav_Model_Entity_Attribute_Set
     * @throws Exception
     */
    protected function _createNewAttributeSet(Gene_BlueFoot_Model_Type $type)
    {

        $attributeSet = $type->getAttributeSet();

        if(!$attributeSet->getId()){
            $this->initNewAttributeSetType($attributeSet, $type->getType());
        }

        $attributeSetName = $type->generateAttributeSetName();

        $attributeSet->setAttributeSetName($attributeSetName);
        $attributeSet->setEntityTypeId($this->_getEntityType()->getEntityTypeId());
        $attributeSet->save();

        $type->setAttributeSetId($attributeSet->getId());

        return $attributeSet;
    }
}