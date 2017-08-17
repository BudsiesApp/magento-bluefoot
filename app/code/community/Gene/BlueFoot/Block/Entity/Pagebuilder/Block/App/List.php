<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_App_List
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_App_List extends Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Default
{

    protected $_entityUrlModel;

    /**
     * Get the app collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract | bool
     */
    public function getAppEntityCollection()
    {
        /* @var $dataModel Gene_BlueFoot_Model_Attribute_Data_Widget_App_List */
        $dataModel = $this->getEntity()->getResource()->getAttribute('app_entity_collection')->getDataModel($this->getEntity());
        if ($dataModel instanceof Gene_BlueFoot_Model_Attribute_Data_Widget_App_List && method_exists($dataModel, 'getAppEntityCollection')) {
            return $dataModel->getAppEntityCollection();
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getEntityUrl(Gene_BlueFoot_Model_Entity $entity, array $params = array())
    {
        return $this->getUrlModel()->getEntityUrl($entity, $params);
    }

    /**
     * @return Gene_BlueFoot_Model_Url
     */
    public function getUrlModel()
    {
        if ($this->_entityUrlModel === null) {
            $this->_entityUrlModel = Mage::getModel('gene_bluefoot/url');
        }
        return $this->_entityUrlModel;
    }
}