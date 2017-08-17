<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_Setup_AppController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_Setup_AppController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/content_apps')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('Content Apps'), $this->__('Content Apps'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('Content Apps'));

        return $this;
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $grid = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app');

        $this->_initAction()
            ->_addContent($grid);

        return $this->renderLayout();
    }

    public function newAction()
    {
        $this->_getSession()->addNotice('Did you know Apps can be created using the App Wizard (<a href="'.$this->getUrl("*/genecms_setup_appwizard").'">click here to enter the App Wizard)</a>');
        return $this->_forward('edit');
    }

    /**
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function editAction()
    {
        $app = Mage::getModel('gene_bluefoot/app');

        if($appId = $this->getRequest()->get('id')){
            $app->load($appId);

            if(!$app->getId()){
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('gene_bluefoot')->__('This app no longer exists')
                );
                return $this->_redirect('*/*/');
            }
        }

        Mage::register('current_app', $app);

        if($activeTab = $this->getRequest()->get('tab')){
            Mage::register('active_tab', $activeTab);
        }

        $this->_initAction()
            ->_addContent(
                $this->getLayout()
                    ->createBlock('gene_bluefoot/adminhtml_setup_app_edit')
                    ->setData('action', $this->getUrl('adminhtml/genecms_setup_app/save'))
            )
            ->_addLeft($this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_edit_tabs'));

        return $this->renderLayout();
    }

    /**
     * @return $this
     */
    public function saveAction()
    {
        $appId = $this->getRequest()->getPost('id');
        $data = $this->getRequest()->getPost();

        $redirectBack = $this->getRequest()->getParam('back', false);

        $viewData = $this->getRequest()->getPost('view_options');
        unset($data['view_options']);

        $data['content_type_ids'] = $this->getRequest()->getPost('content_type_ids', array());
        $data['taxonomy_ids'] = $this->getRequest()->getPost('taxonomy_ids', array());

        $app = Mage::getModel('gene_bluefoot/app');
        if($appId){
            $app->load($appId);
            if(!$app->getId()){
                $this->_getSession()->addError('Application does not exist');
                return $this->_redirectReferer();
            }
        }


        try {
            $app->addData($data);

            if($viewData){
                $app->setViewOptions($viewData);
            }

            $app->updateRelatedEntitesOnSave();
            $app->save();

            //clear admin menu cache on save
            Mage::helper('gene_bluefoot/admin')->clearAdminMenuCache();


        }catch(Exception $e){
            $this->_getSession()->addError('Exception while saving: ' . $e->getMessage());
            Mage::logException($e);
            return $this->_redirectReferer();
        }

        $this->_getSession()->addSuccess('App saved successfully');

        if($redirectBack){
            return $this->_redirect('*/*/edit', array(
                'id'    => $app->getId(),
            ));
        }

        return $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $appId = $this->getRequest()->getParam('id');
        $app = Mage::getModel('gene_bluefoot/app');

        $app->load($appId);
        if(!$app->getId()){
            $this->_getSession()->addError('Application does not exist');
            return $this->_redirectReferer();
        }

        try{
            $app->delete();
            //clear admin menu cache on save
            Mage::helper('gene_bluefoot/admin')->clearAdminMenuCache();

        }catch (Mage_Exception $e){
            $this->_getSession()->addError($e->getMessage());
            Mage::logException($e);
            return $this->_redirectReferer();
        }catch(Exception $e){
            $this->_getSession()->addError('An error occurred while deleting the application, please check the log for details');
            Mage::logException($e);
            return $this->_redirectReferer();
        }


        $this->_getSession()->addSuccess('Application deleted');
        return $this->_redirect('*/*/');

    }

    /**
     * Is the user allowed to view this controller?
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return true;
        return Mage::getSingleton('admin/session')->isAllowed('system/gene_bluefoot/content_apps');
    }
}