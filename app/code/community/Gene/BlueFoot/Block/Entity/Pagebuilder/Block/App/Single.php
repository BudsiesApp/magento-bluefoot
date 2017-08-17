<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_App_Single
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_App_Single extends Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Default
{

    protected $_entityUrlModel;

    /**
     * Get the app entity
     * @return bool
     */
    public function getAppEntity()
    {
        /* @var $dataModel Gene_BlueFoot_Model_Attribute_Data_Widget_App_Single */
        $dataModel = $this->getEntity()->getResource()->getAttribute('app_entity_id')->getDataModel($this->getEntity());
        if ($dataModel instanceof Gene_BlueFoot_Model_Attribute_Data_Widget_App_Single && method_exists($dataModel, 'getAppEntity')) {
            return $dataModel->getAppEntity();
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