<?php
/**
 * Class Gene_BlueFoot_EntityController
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_EntityController extends Mage_Core_Controller_Front_Action
{
    const HANDLE_PREFIX = 'bluefoot_';

    protected $_customLayoutHandles = array();


    /**
     * @return bool|Gene_BlueFoot_Model_Entity
     */
    protected function _getCurrentEntity()
    {
        $entityId = $this->getRequest()->get('id');

        if($entityId){
            $entity = Mage::getModel('gene_bluefoot/entity')->load($entityId);
            if($entity && $entity->getId()){
                return $entity;
            }
        }

        return false;
    }

    /**
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function listTypeAction()
    {
        $contentType = Mage::getModel('gene_bluefoot/type');
        if($typeId = $this->getRequest()->getParam('type')){
            if(is_int($typeId)){
                $contentType->load($typeId);
            }else{
                $contentType->load($typeId, 'identifier');
            }

        }

        if(!$contentType->getId()){
            return $this->norouteAction();
        }

        $layer = Mage::getModel('gene_bluefoot/entity_content_layer');

        Mage::register('current_content_type', $contentType);
        Mage::register('genecms_entity_layer', $layer);


        $handles = array(
            'genecms_entity_list',
            self::HANDLE_PREFIX . 'entity_list',
            self::HANDLE_PREFIX . 'entity_list_type_' . strtoupper($contentType->getIdentifier()),
            self::HANDLE_PREFIX . 'entity_list_typeid_' . $contentType->getId(),
        );


        $this->addCustomLayoutHandle($handles);
        $this->loadLayout();

        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function viewAction()
    {
        $currentEntity = $this->_getCurrentEntity();
        if(!$currentEntity){
            $this->norouteAction();
            return;
        }

        $contentType = $currentEntity->getContentType();
        if($contentType->getContentType() != 'content'){
            $this->norouteAction();
            return;
        }

        if($currentEntity->hasData('is_active') && !$currentEntity->getIsActive() && !$this->getRequest()->getParam('preview')){
            $this->norouteAction();
            return;
        }

        Mage::register('current_genecms_entity', $currentEntity);
        Mage::register('current_genecms_app', $currentEntity->getContentApp());

        $handles = array(
            'genecms_entity_view',
            self::HANDLE_PREFIX . 'entity_view',
            self::HANDLE_PREFIX . 'entity_view_type_' . strtolower($contentType->getIdentifier()),
            self::HANDLE_PREFIX . 'entity_view_' . $currentEntity->getId(),
        );

        $this->addCustomLayoutHandle($handles);
        $this->loadLayout();


        // Use the items title for the page
        if($currentEntity->getTitle()) {
            $originalTitle = $this->getLayout()->getBlock('head')->getTitle();
            $pageTitle = $currentEntity->getTitle();
            if($currentEntity->getMetaTitle() && trim($currentEntity->getMetaTitle()) != ''){
                $pageTitle = $currentEntity->getMetaTitle();
                $this->getLayout()->getBlock('head')->setTitle($pageTitle);
            }

            if($currentEntity->getMetaDescription() && trim($currentEntity->getMetaDescription()) != ''){
                $description = $currentEntity->getMetaDescription();
                $this->getLayout()->getBlock('head')->setDescription($description);
            }
        }

        return $this->renderLayout();
    }

    /**
     * @param string $handle
     * @return $this
     */
    public function addCustomLayoutHandle($handles)
    {
        if(!is_array($handles)){
            $handles = array($handles);
        }

        foreach($handles as $handle){
            $this->_customLayoutHandles[] = $handle;
        }

        return $this;
    }

    /**
     * @see Mage_Core_Controller_Front_Action
     * @return $this
     */
    public function addActionLayoutHandles()
    {
        parent::addActionLayoutHandles();
        if(count($this->_customLayoutHandles)){
            foreach($this->_customLayoutHandles as $updateHandle){
                $update = $this->getLayout()->getUpdate();
                $update->addHandle($updateHandle);
            }
        }
        return $this;
    }
}