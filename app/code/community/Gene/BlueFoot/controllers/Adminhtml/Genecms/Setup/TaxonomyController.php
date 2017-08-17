<?php
class Gene_BlueFoot_Adminhtml_Genecms_Setup_TaxonomyController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->_usedModuleName = 'gene_bluefoot';

        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/installer')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('Taxonomies'), $this->__('Taxonomies'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('Taxonomies'));

        return $this;
    }

    public function indexAction()
    {
        $grid = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_taxonomy');

        $this->_initAction()
            ->_addContent($grid);

        return $this->renderLayout();
    }

    public function newAction()
    {
        return $this->_forward('edit');
    }

    public function editAction()
    {
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy');
        if($taxonomyId = $this->getRequest()->getParam('id')){
            $taxonomy->load($taxonomyId);
            if(!$taxonomy->getId()){
                $this->_getSession()->addError('Taxonomy does not exist anymore');
                return $this->_redirect('*/*/index');
            }
        }

        if($appId = $this->getRequest()->get('app_id')){
            $taxonomy->setAppId($appId);
        }

        Mage::register('current_taxonomy', $taxonomy);

        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_taxonomy_edit"));
        $this->renderLayout();
        return $this;
    }

    public function deleteAction()
    {
        $taxonomyId = $this->getRequest()->getPost('id');
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy');
        if($taxonomyId){
            $taxonomy->load($taxonomyId);
            if(!$taxonomy->getId()){
                $this->_getSession()->addError('Failed to load taxonomy');
                return $this->_redirectReferer();
            }
        }

        try {
            $taxonomy->delete();
        }catch (Exception $e){
            $this->_getSession()->addError('Failed to delete taxonomy: ' . $e->getMessage());
            Mage::logException($e);
            return $this->_redirectReferer();
        }
        $this->_getSession()->addSuccess('Successfully deleted taxonomy');
        return $this->_redirect('*/*/index');
    }

    public function saveAction()
    {
        $taxonomyId = $this->getRequest()->getPost('taxonomy_id');
        $data = $this->getRequest()->getPost();

        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy');
        if($taxonomyId){
            $taxonomy->load($taxonomyId);
            if(!$taxonomy->getId()){
                $this->_getSession()->addError('Failed to load taxonomy');
                return $this->_redirectReferer();
            }
        }

        try {
            $taxonomy->addData($data);
            $taxonomy->save();
        }catch(Exception $e){
            $this->_getSession()->addError('Exception while saving: ' . $e->getMessage());
            Mage::logException($e);
            return $this->_redirectReferer();
        }

        $this->_getSession()->addSuccess('Taxonomy saved successfully');
        return $this->_redirect("*/genecms_setup_app/edit", array("id" => $taxonomy->getAppId(), 'tab' => 'taxonomies'));

    }

    /**
     * Is the user allowed to view this controller?
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/gene_bluefoot/content_apps');
    }
}