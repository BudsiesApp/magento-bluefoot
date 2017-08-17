<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_EntityController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_EntityController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    protected $_entityTypeCode = 'gene_bluefoot_entity';

    protected function _initAction()
    {
        $this->_usedModuleName = 'gene_bluefoot';

        $this->loadLayout()
            ->_setActiveMenu('gene_bluefoot_app');
        //->_addBreadcrumb($this->__('CMS'), $this->__('CMS'));

        return $this;
    }

    protected function _setTypeId()
    {
        Mage::register('entityType',
            $this->_getEntityType()->getEntityTypeId(), 1);
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $this->_setTypeId();

        if($this->getRequest()->getParam('type_id')){
            $typeFilter = Mage::getModel('gene_bluefoot/type')->load($this->getRequest()->getParam('type_id'));
            Mage::register('type_filter', $typeFilter);
        }


        $this->_title($this->__("Gene Cms"));
        $this->_title($this->__("Content"));

        $grid = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity');

        $this->_initAction()
            ->_addContent($grid);

        if($typeFilter && $typeFilter->getContentApp()){
            $this->_setAppCurrentMenu($typeFilter->getContentApp());
        }

        return $this->renderLayout();

        return $this->renderLayout();
    }

    public function newAction()
    {
        return $this->_forward('edit');
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function saveAction()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $postData = new Varien_Object($this->getRequest()->getPost());
        $newEntity = true;
        $id = $postData->getEntityId();

        $taxonomyTerms = $postData->getTaxonomyTerms();
        $taxonomyTermIds = null;
        //build up array of taxonomy terms ids
        if(is_array($taxonomyTerms)){
            $taxonomyTermIds = array();
            foreach($taxonomyTerms as $taxonomyId => $termIdString){
                $termIds = explode(',', ltrim($termIdString, ','));
                $taxonomyTermIds = array_merge($taxonomyTermIds, $termIds);
            }
        }

        $entityModel = Mage::getModel('gene_bluefoot/entity');
        /**
         * @var $entityModel Gene_BlueFoot_Model_Entity
         */

        $entityModel->setStoreId($storeId);

        if($id){
            $entityModel->load($id);
            if(!$entityModel->getId() || $entityModel->getId() != $id){
                throw new Exception('Failed to load content entity Model');
            }

            $newEntity = false;
        }

        $entityModel->addData($postData->getData());

        $entityModel->setTaxonomyTermIds($taxonomyTermIds);

        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $entityModel->setData($attributeCode, null);
            }
        }

        try{
            Mage::register('current_entity_object', $entityModel);

            $entityModel->validate();
            $entityModel->save();

        }catch(Mage_Eav_Model_Entity_Attribute_Exception $e){
            //Used for validation errors when cycling through attributes on entity validate & save
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('gene_bluefoot')->__('Validation Error: ' . $e->getMessage())
            );
            return $this->_redirectReferer();
        }catch(Mage_Exception $e){

            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('gene_bluefoot')->__('Error: ' . $e->getMessage())
            );

            return $this->_redirectReferer();

        }catch(Exception $e){
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('gene_bluefoot')->__('Exception: ' . $e->getMessage())
            );

            return $this->_redirectReferer();
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('gene_bluefoot')->__('Saved Content Successfully')
        );

        if($redirectBack){
            return $this->_redirect('*/*/edit', array(
                'entity_id'    => $entityModel->getId(),
                'store' => $storeId
            ));
        }

        // If we have an entity model with a type id redirect back to that grid
        if($entityModel->getContentType() && $entityModel->getContentType()->getId()) {
            return $this->_redirect('*/*/', array('type_id' => $entityModel->getContentType()->getId()));
        }

        return $this->_redirect('*/*/', array('store' => $storeId));


    }

    public function editAction()
    {

        $entity = Mage::getModel('gene_bluefoot/entity');

        $storeId = $this->getRequest()->getParam('store', 0);
        $entity->setStoreId($storeId);
        if($entityId = $this->getRequest()->get('entity_id')){
            $entity->load($entityId);
            if(!$entity->getId()){
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('gene_bluefoot')->__('This content entity no longer exists')
                );
                return $this->_redirect('*/*/');

            }
        }elseif($entityTypeId = $this->getRequest()->getParam('type_id')){

            $entityType = Mage::getModel('gene_bluefoot/type')->load($entityTypeId);

            if(!$entityType->getId()){
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('gene_bluefoot')->__('No such entity type exists')
                );

                return $this->_redirect('*/*/');
            }

            $attributeSetId = $entityType->getAttributeSetId();
            $entity->setTypeId();
            $entity->setAttributeSetId($attributeSetId);
            //set default value
            $entity->setIsActive(1);

        }else{
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('gene_bluefoot')->__('Invalid Parameters to add/edit content')
            );

            return $this->_redirectReferer();
            return $this->_redirect('*/*/');
        }


        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSet->load($entity->getAttributeSetId());

        Mage::register('attribute_set', $attributeSet);
        Mage::register('entity', $entity);

        $this->_initAction()
            ->_addContent(
                $this->getLayout()
                    ->createBlock('gene_bluefoot/adminhtml_entity_edit')
                    ->setData('action', $this->getUrl('adminhtml/genecms_entity/save'))
            )
            ->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher', 'store_switcher'))
            ->_addLeft($this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_edit_tabs'));

        //set the current menu
        $this->_setAppCurrentMenu($entity->getContentApp());

        return $this->renderLayout();
    }

    public function taxonomyTabAction()
    {
        $entity = Mage::getModel('gene_bluefoot/entity');

        $storeId = $this->getRequest()->getParam('store', 0);
        $entity->setStoreId($storeId);
        if($entityId = $this->getRequest()->get('entity_id')){
            $entity->load($entityId);
            if(!$entity->getId()){
                throw new Exception('This content entity no longer exists');
            }
        }elseif($entityTypeId = $this->getRequest()->getParam('type_id')){

            $entityType = Mage::getModel('gene_bluefoot/type')->load($entityTypeId);

            if(!$entityType->getId()){
                throw new Exception('No such entity type exists');
            }

            $attributeSetId = $entityType->getAttributeSetId();
            $entity->setTypeId();
            $entity->setAttributeSetId($attributeSetId);

        }else{
            throw new Exception('Invalid Parameters to add/edit content');
        }

        $taxonomyId = $this->getRequest()->get('taxonomy');
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy')->load($taxonomyId);

        if(!$taxonomy->getId()){
            throw new Exception('Failed to load taxonomy, does not exist.');
        }


        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSet->load($entity->getAttributeSetId());

        Mage::register('attribute_set', $attributeSet);
        Mage::register('entity', $entity);

        $this->loadLayout();
        $taxonomyBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_edit_tab_taxonomy');
        $taxonomyBlock->setTaxonomy($taxonomy);

        $this->getResponse()->setBody($taxonomyBlock->toHtml());
        return;
    }

    /**
     * Delete action
     * @return $this|Mage_Adminhtml_Controller_Action
     * @throws Exception
     */
    public function deleteAction()
    {

        //load the model
        $entity = Mage::getModel('gene_bluefoot/entity');

        //check if we have entity id
        if($entityId = $this->getRequest()->get('entity_id')){

            //load the entity
            $entity->load($entityId);

            if(!$entity->getId()){
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('gene_bluefoot')->__('This content entity no longer exists')
                );
                return $this->_redirect('*/*/');

            }

            try{
                //take the type id to redirect lated
                $typeId = $entity->getContentType()->getId();

                //delete the entity
                $entity->delete();

                //show success and redirectg
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('gene_bluefoot')->__('Successfully deleted content')
                );

                return $this->_redirect('*/*/', array('type_id' => $typeId));

            }catch(Exception $e){
                //log the exception and show error
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('gene_bluefoot')->__('Error Exception: ' . $e->getMessage())
                );

                return $this->_redirectReferer();
            }
        }else{
            //something is wrong, go back
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('gene_bluefoot')->__('Invalid Parameters to delete content')
            );

            return $this->_redirectReferer();
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}