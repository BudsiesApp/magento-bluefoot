<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_TaxonomytermController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_TaxonomytermController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    protected function _initAction()
    {
        $this->loadLayout();
        return $this;
    }

    public function indexAction()
    {
        $taxonomyId = $this->getRequest()->getParam('taxonomy');
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy')->load($taxonomyId);
        if(!$taxonomy->getId()){
            $this->_getSession()->addError('Unable to load taxonomy');
            $this->_redirectReferer();
        }

        Mage::register('current_taxonomy', $taxonomy);

        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_term');
        $this->loadLayout()->_addContent($block);
        $this->_setAppCurrentMenu($taxonomy->getContentApp());
        $this->renderLayout();
        return $this;
    }

    public function newAction()
    {
        return $this->_forward('edit');
    }

    public function addAction()
    {
        return $this->_forward('edit');
    }

    public function editAction()
    {
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy');
        $term = Mage::getModel('gene_bluefoot/taxonomy_term');



        if($termId = $this->getRequest()->getParam('id')){
            $term->load($termId);
            if(!$term->getId()){
                $this->_getSession()->addError('Taxonomy Term does not exist anymore');
                return $this->_redirect('*/*/index');
            }

            $taxonomy->load($term->getTaxonomyId());

        }elseif($taxonomyId = $this->getRequest()->getParam('taxonomy')) {
            $taxonomy->load($taxonomyId);
            if(!$taxonomy->getId()){
                $this->_getSession()->addError('Taxonomy cannot be found for term');
                return $this->_redirect('*/*/index');
            }

            //set default value
            $term->setStatus(1);

        }else{
            $this->_getSession()->addError('No Taxonomy set for term to be associated with');
            return $this->_redirect('*/*/index');
        }

        $term->setTaxonomy($taxonomy);

        Mage::register('current_term', $term);
        Mage::register('current_taxonomy', $taxonomy);


        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock("gene_bluefoot/adminhtml_term_edit"))
            ->_addLeft($this->getLayout()->createBlock("gene_bluefoot/adminhtml_term_edit_tabs"));
        $this->_setAppCurrentMenu($taxonomy->getContentApp());
        $this->renderLayout();
        return $this;
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

        $entityModel = Mage::getModel('gene_bluefoot/taxonomy_term');
        /**
         * @var $entityModel Gene_BlueFoot_Model_Taxonomy_Term
         */

        $entityModel->setStoreId($storeId);

        if($id){
            $entityModel->load($id);
            if(!$entityModel->getId() || $entityModel->getId() != $id){
                throw new Exception('Failed to load Term entity Model');
            }

            $newEntity = false;
        }

        $entityModel->addData($postData->getData());

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
                'id'    => $entityModel->getId(),
                'store' => $storeId
            ));
        }

        return $this->_redirect('*/*/', array('store' => $storeId, 'taxonomy' => $entityModel->getTaxonomy()->getId()));
    }

    public function quickAddAction()
    {
        if(!$this->getRequest()->isAjax()){
            die('no direct action');
        }

        $contentEntityId = $this->getRequest()->get('entity_id');
        if($contentEntityId){
            $contentEntity = Mage::getModel('gene_bluefoot/entity')->load($contentEntityId);
            Mage::register('content_entity', $contentEntity);
        }

        $taxonomyId = $this->getRequest()->getPost('taxonomy');
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy')->load($taxonomyId);
        $term = Mage::getModel('gene_bluefoot/taxonomy_term');

        $return = array();

        if(!$taxonomy->getId()){
            $return['error_alert'] = 'No taxonomy found';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
            return;
        }

        //set default value
        $term->setStatus(1);

        Mage::register('current_taxonomy', $taxonomy);
        Mage::register('current_term', $term);

        $this->loadLayout();
        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_term_quickadd');


        //$return['update']['add-term-form-'. $taxonomyId] = $block->toHtml();
        $return['modal'] = $block->toHtml();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

        return $this;
    }

    public function quickSaveAction()
    {
        if(!$this->getRequest()->isAjax()){
            die('no direct action');
        }
        $storeId = $this->getRequest()->getParam('store', 0);
        $postData = new Varien_Object($this->getRequest()->getPost());
        $newEntity = true;
        $id = $postData->getEntityId();

        $taxonomyId = $this->getRequest()->getParam('taxonomy_id');

        $termModel = Mage::getModel('gene_bluefoot/taxonomy_term');
        /**
         * @var $termModel Gene_BlueFoot_Model_Taxonomy_Term
         */

        $contentEntityId = $this->getRequest()->get('content_entity_id');
        $contentEntity = Mage::getModel('gene_bluefoot/entity')->load($contentEntityId);
        Mage::register('entity', $contentEntity);

        $return = array();

        $termModel->setStoreId($storeId);

        if($id){
            $termModel->load($id);
            if(!$termModel->getId() || $termModel->getId() != $id){
                $return['error_alert'] = 'Failed to load Term entity Model';
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
                return;
            }

            $newEntity = false;
        }else{
            $postData['taxonomy_id'] = $taxonomyId;
        }

        $termModel->addData($postData->getData());

        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $termModel->setData($attributeCode, null);
            }
        }

        $error = false;
        try{
            Mage::register('current_entity_object', $termModel);

            $termModel->validate();
            $termModel->save();

            $taxonomy = $termModel->getTaxonomy();
            $taxonomyId = $taxonomy->getId();

            if(!$error){
                $this->loadLayout();
                $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_entity_edit_tab_taxonomy');
                $block->setTaxonomy($taxonomy);
                $return['update']['entity_tabs_taxonomy_'.$taxonomy->getId().'_content'] = $block->toHtml();
            }

        }catch(Mage_Eav_Model_Entity_Attribute_Exception $e){
            $return['error_alert'] = $e->getMessage();
            $error = true;
        }catch(Mage_Exception $e){

            $return['error_alert'] = $e->getMessage();
            $error = true;

        }catch(Exception $e){
            Mage::logException($e);
            $return['error_alert'] = 'An error occurred while saving the term';
            $error = true;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

        return $this;


    }

    protected function _isAllowed()
    {
        return true;
    }

}