<?php

/**
 * Class Gene_BlueFoot_ContentController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_ContentController extends Mage_Core_Controller_Front_Action
{
    const HANDLE_PREFIX = 'bluefoot_';

    protected $_customLayoutHandles = array();

    /**
     * @return Gene_BlueFoot_Model_Entity
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
     * @return bool|Gene_BlueFoot_Model_Taxonomy_Term
     */
    protected function _getCurrentTerm()
    {
        $termId = $this->getRequest()->get('term_id');
        if(!$termId){
            $termId = $this->getRequest()->get('term');
        }
        if($termId){
            $term = Mage::getModel('gene_bluefoot/taxonomy_term')->load($termId);
            if($term && $term->getId()){
                return $term;
            }
        }

        return false;
    }

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

    protected function _initEntityLayout(Gene_BlueFoot_Model_Entity $entity)
    {
        $helper = Mage::helper('gene_bluefoot/view_entity');
        $helper->initEntityLayout($entity, $this);

        return $this;
    }

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

        $currentTerm = $this->_getCurrentTerm();

        if($currentTerm){
            $currentEntity->setCurrentTermId($currentTerm->getId());
        }


        Mage::register('current_genecms_entity', $currentEntity);
        Mage::register('current_genecms_app', $currentEntity->getContentApp());
        Mage::register('current_gemecms_term', $currentTerm);
        Mage::register('bluefoot_page_type', 'entity_view');

        $this->_initEntityLayout($currentEntity);

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