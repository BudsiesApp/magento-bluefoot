<?php

/**
 * Class Gene_BlueFoot_Model_Type
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Type extends Mage_Core_Model_Abstract
{
    protected $_entityType = null;

    protected $_eventPrefix      = 'gene_bluefoot_type';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init("gene_bluefoot/type");
    }

    /**
     * @return string
     */
    public function generateAttributeSetName()
    {
        return $this->getName() . ' {'.$this->getIdentifier().'}';
    }

    /**
     * @param $attSetId
     * @return Mage_Core_Model_Abstract
     */
    public function loadByAttributeSetId($attSetId)
    {
        return $this->load($attSetId, 'attribute_set_id');
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function checkIdentifierExists($identifier)
    {
        $model = Mage::getModel('gene_bluefoot/type');
        $model->load($identifier, 'identifier');

        if($model->getId()){
            return true;
        }

        return false;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if($this->getContentType() == 'block'){
            $this->setSingularName($this->getName());
        }
        return parent::_beforeSave();
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function getDefaultAttributeSet()
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSetId = $this->_getEntityType()->getDefaultAttributeSetId();
        $attributeSet->load($attributeSetId);

        return $attributeSet;
    }

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    public function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType(Gene_BlueFoot_Model_Entity::ENTITY);
        }

        return $this->_entityType;
    }

    /**
     * @return mixed
     */
    public function getEntityTypeId()
    {
        return $this->_getEntityType()->getId();
    }

    /**
     * @return $this
     */
    public function initNewBlockType()
    {
        $blockData = array(
            'content_type' => 'block',
        );
        $this->addData($blockData);

        return $this;
    }

    /**
     * @return $this
     */
    public function initNewContentType()
    {
        $blockData = array(
            'content_type' => 'content',
        );
        $this->addData($blockData);

        return $this;
    }

    /**
     * Used to process skeleton data from attribute set/groups so it generates new ids
     * @param $data
     */
    public function processNewSetData($data)
    {
        if(isset($data['groups'])) {
            foreach ($data['groups'] as $gKey => $group) {

                //replace ids with non numeric value to generate new ids
                $group[0] = 'ynode-' . $group[0];
                $group[3] = '';

                //override original with new data
                $data['groups'][$gKey] = $group;
            }
        }

        if(isset($data['attributes'])) {
            foreach( $data['attributes'] as $aKey => $attribute ) {

                //replace group ids to match to new non numeric value
                $attribute[1] = 'ynode-' . $attribute[1];

                //override original with new data
                $data['attributes'][$aKey] = $attribute;

            }

        }

        $data['removeGroups'] = array();

        return $data;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function getAttributeSet()
    {
        if(!$this->getId()){
            //throw new Exception('Cannot load attribute set as Content Type has not loaded or does not have an id');
        }

        if(!$this->getData('attribute_set')){
            $this->getResource()->loadAttributeSet($this);
        }

        return $this->getData('attribute_set');
    }

    /**
     * @return $this
     */
    public function validate()
    {
        return $this;
    }

    /**
     * @param null $typeId
     * @return mixed
     * @throws Exception
     */
    public function getAllAttributes($typeId = null)
    {
        if($typeId){
            $type = Mage::getModel('gene_bluefoot/type')->load($typeId);
            if(!$type->getId()){
                throw new Exception('Failed to load content/block type with id "'.$typeId.'"');
            }
        }else{
            $type = $this;
            $typeId = $type->getId();
        }

        $attrSetId = false;
        $attrSet = $type->getAttributeSet();
        if($attrSet){
            $attrSetId = $attrSet->getId();
        }

        if(!$attrSetId){
            throw new Exception('Failed to load attribute set for content type (ID: '.$typeId.')');
        }

        $attributes = Mage::getModel('gene_bluefoot/entity')->getResource()
            ->loadAllAttributes()
            ->getSortedAttributes($attrSetId);

        return $attributes;
    }

    public function getVisibleAttributes()
    {
        $attSetId = $this->getAttributeSet()->getId();

        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
            ->addVisibleFilter()
            ->setAttributeSetFilter($attSetId)
            ->addSetInfo();

        return $collection;
    }

    /**
     * @return Gene_BlueFoot_Model_App
     */
    public function getContentApp()
    {
        if(!$this->getData('content_app')){

            $app = Mage::getModel('gene_bluefoot/app');
            $app->load($this->getAppId());

            $this->setData('content_app', $app);
        }

        return $this->getData('content_app');
    }

}