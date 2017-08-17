<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_Setup_AppwizardController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_Setup_AppwizardController extends Mage_Adminhtml_Controller_Action
{
    protected $_wizardModel =null;

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/content_app_wizard')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('App Wizard'), $this->__('App Wizard'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('App Wizard'));

        return $this;
    }

    public function indexAction()
    {
        $this->_forward('start');
    }

    /**
     * @return Gene_BlueFoot_Model_App_Wizard
     */
    protected function _getWizardModel()
    {
        if(is_null($this->_wizardModel)){
            $this->_wizardModel = Mage::getModel('gene_bluefoot/app_wizard');
        }
        return $this->_wizardModel;
    }

    public function startAction()
    {
        $wizardModel = $this->_getWizardModel();
        if(!$this->getRequest()->get('continue')){
            $wizardModel->clearSession();
        }

        return $this->_renderStep('app_info');
    }

    public function reloadStepAction()
    {
        $stepId = $this->_getWizardModel()->getCurrentStepId('app_info');
        return $this->_renderAjaxStep($stepId);
    }

    public function saveStepAction()
    {
        $return = array();
        $wizardModel = $this->_getWizardModel();
        $stepId = $this->getRequest()->getPost('step_id') ? $this->getRequest()->getPost('step_id') : $wizardModel->getCurrentStepId();

        $postData = $this->getRequest()->getPost();
        unset($postData['step_id']);
        unset($postData['form_key']);


        //try and to validation
        $currentStep = $wizardModel->getStep($stepId);
        if($currentStep && $currentStep->getDataModel()){

            $dataModel = false;
            try {
                $dataModel = Mage::getModel($currentStep->getDataModel());
            }catch (Exception $e){

            }

            if(is_object($dataModel) && method_exists($dataModel, 'validate')){
                if(!$dataModel->validate($postData)){
                    $errors = $dataModel->getErrors();
                    if(is_array($errors)){
                        $return['error_alert'] = implode(',' . PHP_EOL, $errors);
                    }else{
                        $return['error_alert'] = 'Unspecified validation error occurred. Please check form and try again.';
                    }

                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

                    return;
                }
            }
        }

        $wizardModel->setStepData($stepId, $postData);

        Mage::register('app_wizard', $wizardModel);

        //validate data

        if($nextStep = $wizardModel->nextStep()){
            $steps = $wizardModel->getSteps();
            $this->loadLayout();
            $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
            $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

            $progressBlock->setSteps($steps);

            $return['update']['app-wizard-step'] = $block->getStepBlockHtml($nextStep);
            $return['update']['wizard-progress'] = $progressBlock->toHtml();
            $return['update']['step-info'] = $block->getStepInfoBlockHtml($nextStep);
            $return['update']['app-step-additional'] = '';
            $return['update']['wizard-step-title'] = 'App Wizard : ' .$nextStep->getTitle();

        }else{
            $return['error'] = 'Unable to load next step';
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

        return;

    }

    public function removeContentTypeAction()
    {
        if(!$this->getRequest()->isAjax()){
            return $this->norouteAction();
        }
        $return = array();
        $identifier = $this->getRequest()->get('identifier');

        $wizardModel = $this->_getWizardModel();

        $contentTypeData = $wizardModel->getStepData('content_type_data');
        if($contentTypeData && isset($contentTypeData['type_data']) && is_array($contentTypeData['type_data'])){
            $typesData = $contentTypeData['type_data'];
            if(array_key_exists($identifier, $typesData)){
                unset($typesData[$identifier]);
                $contentTypeData['type_data'] = $typesData;
                $wizardModel->setStepData('content_type_data', $contentTypeData);
            }
        }

        Mage::register('app_wizard', $wizardModel);

        $wizardModel->setCurrentStep('content_types');
        $currentStep = $wizardModel->getCurrentStep();
        $steps = $wizardModel->getSteps();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
        $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

        $progressBlock->setSteps($steps);

        $return['update']['app-wizard-step'] = $block->getStepBlockHtml($currentStep);
        $return['update']['wizard-progress'] = $progressBlock->toHtml();
        $return['update']['step-info'] = $block->getStepInfoBlockHtml($currentStep);
        $return['showhide']['wizard-navigation'] = true;
        $return['showhide']['wizard-navigation-replace'] = false;

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

    }

    public function addContentTypeAction()
    {
        $wizardModel = $this->_getWizardModel();

        if($wizardModel->getCurrentStepId() != 'content_types'){
            //return $this->_redirect('*/*/start');
        }

        $this->_initAction();

        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
        $block->setHideButtons(true);
        $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

        $wizardModel->setCurrentStep('content_types');
        $steps = $wizardModel->getSteps();
        $currentStepId = $wizardModel->getCurrentStepId();
        $currentStep = $wizardModel->getCurrentStep();

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $currentStep->setAddNewContentType(true);

        $block->setSteps($steps);
        $block->setCurrentStep($currentStep);

        Mage::register('app_wizard', $wizardModel);

        $progressBlock->setSteps($steps);
        $progressBlock->setTitle('Progress');

        $block->setChild('progress', $progressBlock);

        $this
            //->_addLeft($progressBlock)
            ->_addContent($block);

        return $this->renderLayout();
    }

    public function addTaxonomyAction()
    {
        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy');
        Mage::register('current_taxonomy', $taxonomy);

        $wizardModel = $this->_getWizardModel();

        $return = array();

        $currentStep = $wizardModel->getCurrentStep();
        $steps = $wizardModel->getSteps();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_taxonomy_wizard');

        $return['update']['app-step-additional'] = $block->toHtml();
        $return['showhide']['wizard-navigation'] = false;
        $return['showhide']['wizard-navigation-replace'] = true;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
    }


    protected function _loadStep($stepId)
    {
        $steps = $this->_getSteps();
        $step = array_key_exists($stepId, $steps) ? $steps[$stepId]: null;

        return $step;
    }

    public function loadPreviousStepAction()
    {
        $wizardModel = $this->_getWizardModel();
        $currentStep = $wizardModel->getCurrentStep();

        $previousStepId = $currentStep->getPreviousStep();
        if(!$previousStepId){
            $previousStepId = 'app_info';
        }

        $wizardModel->removeCompletedStep($wizardModel->getCurrentStepId());
        $wizardModel->removeCompletedStep($previousStepId);

        return $this->_renderAjaxStep($previousStepId);
    }

    public function loadStepAction()
    {
        $wizardModel = $this->_getWizardModel();
        $stepId = $this->getRequest()->getParam('step') ? $this->getRequest()->getParam('step') : $wizardModel->getCurrentStepId();

        return $this->_renderStep($stepId);
    }

    protected function _renderAjaxStep($stepId)
    {
        $wizardModel = $this->_getWizardModel();
        Mage::register('app_wizard', $wizardModel);

        $wizardModel->setCurrentStep($stepId);
        $currentStep = $wizardModel->getCurrentStep();
        $steps = $wizardModel->getSteps();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
        $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

        $progressBlock->setSteps($steps);

        $return['update']['app-wizard-step'] = $block->getStepBlockHtml($currentStep);
        $return['update']['wizard-progress'] = $progressBlock->toHtml();
        $return['update']['step-info'] = $block->getStepInfoBlockHtml($currentStep);
        $return['update']['app-step-additional'] = '';
        $return['showhide']['wizard-navigation'] = true;
        $return['showhide']['wizard-navigation-replace'] = false;

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
    }

    protected function _renderStep($stepId)
    {
        $wizardModel = $this->_getWizardModel();
        $wizardModel->setCurrentStep($stepId);

        $this->_initAction();

        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
        $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

        $steps = $wizardModel->getSteps();
        $currentStepId = $wizardModel->getCurrentStepId();
        $currentStep = $wizardModel->getCurrentStep();

        Mage::register('app_wizard', $wizardModel);

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $block->setSteps($steps);
        $block->setCurrentStep($currentStep);

        $block->setChild('progress', $progressBlock);

        $progressBlock->setSteps($steps);
        $progressBlock->setTitle('Progress');

        $this
            //->_addLeft($progressBlock)
            //->_addContent($progressBlock)
            ->_addContent($block);

        return $this->renderLayout();
    }

    public function saveTaxonomyAction()
    {
        $wizardModel = $this->_getWizardModel();
        $data = $this->getRequest()->getPost();
        unset($data['form_key']);

        $identifier = isset($data['title']) ? $data['title'] : uniqid('taxonomy-');
        $taxonomyData = $wizardModel->getStepData('taxonomy_data');
        $taxonomyData['taxonomies'][$identifier] = $data;

        $wizardModel->setStepData('taxonomy_data', $taxonomyData);

        $return  = array();
        Mage::register('app_wizard', $wizardModel);

        $wizardModel->setCurrentStep('taxonomy');
        $currentStep = $wizardModel->getCurrentStep();
        $steps = $wizardModel->getSteps();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
        $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

        $progressBlock->setSteps($steps);

        $return['update']['app-wizard-step'] = $block->getStepBlockHtml($currentStep);
        $return['update']['wizard-progress'] = $progressBlock->toHtml();
        $return['update']['step-info'] = $block->getStepInfoBlockHtml($currentStep);
        $return['update']['app-step-additional'] = '';
        $return['showhide']['wizard-navigation'] = true;
        $return['showhide']['wizard-navigation-replace'] = false;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

    }

    public function saveTypeDataAction()
    {
        $wizardModel = $this->_getWizardModel();

        $contentTypeData = $wizardModel->getStepData('content_type_data');

        $data = $this->getRequest()->getPost();

        $identifier = isset($data['identifier']) ? $data['identifier'] : uniqid('type-');
        unset($data['form_key']);

        //check identifier does not exist
        $typeModel = Mage::getModel('gene_bluefoot/type');
        if($typeModel->checkIdentifierExists($identifier)){
            $return['error_alert'] = 'Identifier "'.$identifier.'" already exists for a content type. Please choose another.';
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
        }

        $contentTypeData['type_data'][$identifier] = $data;

        $wizardModel->setStepData('content_type_data', $contentTypeData);

        //show the page
        $return = array();

        Mage::register('app_wizard', $wizardModel);

        $wizardModel->setCurrentStep('content_types');
        $currentStep = $wizardModel->getCurrentStep();
        $steps = $wizardModel->getSteps();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard');
        $progressBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_progress');

        $progressBlock->setSteps($steps);

        $return['update']['app-wizard-step'] = $block->getStepBlockHtml($currentStep);
        $return['update']['wizard-progress'] = $progressBlock->toHtml();
        $return['update']['step-info'] = $block->getStepInfoBlockHtml($currentStep);
        $return['showhide']['wizard-navigation'] = true;
        $return['showhide']['wizard-navigation-replace'] = false;

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

    }

    public function saveAllAction()
    {
        $wizardModel = $this->_getWizardModel();

        $return = array();

        if($wizardModel->validateStepData()){

            $app = $wizardModel->create();
            $appId = $app->getId();

            return $this->_redirect('*/*/success', array('app_id' => $appId));
        }else{
            $return['error'] = 'Validation failed';
        }
    }

    public function successAction()
    {
        if($appId = $this->getRequest()->getParam('app_id')){
            $app = Mage::getModel('gene_bluefoot/app');
            $app->load($appId);
            if($app->getId()){
                Mage::register('created_app', $app);
            }
        }

        $this->_initAction();

        $block = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_app_wizard_success');

        $this->_addContent($block);

        //clear admin menu cache on save
        Mage::helper('gene_bluefoot/admin')->clearAdminMenuCache();

        return $this->renderLayout();
    }

    public function reviewAction()
    {
        $return['redirect'] = $this->getUrl('adminhtml/genecms_setup_appwizard/saveAll');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));

        return;
    }

    /**
     * Is the user allowed to view this controller?
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/gene_bluefoot/content_app_wizard');
    }
}