<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_Setup_AttributeController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_Setup_AttributeController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    /**
     * Init the controller
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_usedModuleName = 'gene_bluefoot';

        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/setup_attributes')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('Content Attributes'), $this->__('Content Attributes'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('Content Attributes'));

        return $this;
    }

    protected function _initAttribute()
    {
        $attribute = Mage::getModel('gene_bluefoot/attribute');

        return $attribute;
    }


    /**
     * Grid of Content Attributes
     * @return $this
     */
    public function indexAction()
    {
        $grid = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_attribute');

        $this->_initAction()
            ->_addContent($grid);

        $this->renderLayout();

        return $this;
    }

    public function newAction()
    {
        return $this->_forward('edit');
    }

    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute();

        $attributeObject->load($attributeId);
        if (!$attributeObject->getId())
        {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('gene_bluefoot')->__('This attribute no longer exists')
            );
            $this->_redirect('*/*/');
            return;
        }

        $attributeObject->delete();

        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('gene_bluefoot')->__('Attribute deleted successfully')
        );
        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()
            ->setEntityTypeId($this->_getEntityType()->getId());

        if ($attributeId)
        {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId())
            {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('gene_bluefoot')->__('This attribute no longer exists')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)){
            $attributeObject->setData($data);
        }

        Mage::register('gene_cms_attribute', $attributeObject);
        Mage::register('entity_attribute', $attributeObject);
        $this->_initAction()
            ->_addContent(
                $this->getLayout()
                    ->createBlock('gene_bluefoot/adminhtml_setup_attribute_edit')
                    ->setData('action', $this->getUrl('adminhtml/genecms_setup_attributes/save'))
            )
            ->_addLeft($this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_attribute_edit_tabs'));

        $this->renderLayout();

        return $this;
    }


    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $eavHelper = Mage::helper('gene_bluefoot/eav');
        $contentAttribute = $this->_initAttribute();

        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $redirectBack = $this->getRequest()->getParam('back', false);

            if (!isset($data['additional'])){
                $data['additional'] = array();
            }

            $id = $this->getRequest()->getPost('attribute_id');

            //Load if editing
            if ($id) {
                $contentAttribute->load($id);
                if (!$contentAttribute->getId()){
                    $session->addError(Mage::helper('gene_bluefoot')->__('This Attribute no longer exists'));
                    return $this->_redirect('*/*/');
                }
            } else {
                $data['is_user_defined'] = 1;
            }

            //run Validation against input type
            if (isset($data['frontend_input'])) {
                /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        //@todo - not the best way to handle
                        throw new Exception($message);
                        $session->addError($message);
                    }
                    if($id){
                        return $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    } else {
                        return $this->_redirect('*/*/edit', array('_current' => true));
                    }
                }

                $data['source_model'] = $eavHelper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $eavHelper->getAttributeBackendModelByInputType($data['frontend_input']);

                if (is_null($contentAttribute->getIsUserDefined()) || $contentAttribute->getIsUserDefined() != 0) {
                    $data['backend_type'] = $contentAttribute->getBackendTypeByInput($data['frontend_input']);
                }

                $defaultValueField = $contentAttribute->getDefaultValueByInput($data['frontend_input']);
                if ($defaultValueField) {
                    $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
                }

            }

            try {
                $contentAttribute->addData($data);
                $contentAttribute->setEntityTypeId($this->_getEntityType()->getEntityTypeId());
                $contentAttribute->save();
            } catch (Mage_Exception $e) {
                $session->addError($e->getMessage());
                return $this->_redirectReferer();
            } catch(Exception $e) {
                $session->addError('An exception occurred while saving the attribute. Please check the logs for details');
                return $this->_redirect('*/*');
            }

            if($id){
                $successMsg = 'Successfully added new attribute ['.$contentAttribute->getAttributeCode() . ']';
            } else {
                $successMsg = 'Successfully updated new attribute ['.$contentAttribute->getAttributeCode() . ']';
            }

            $session->addSuccess($successMsg);

            if($redirectBack){
                return $this->_redirect('*/*/edit', array(
                    'attribute_id'    => $contentAttribute->getId(),
                ));
            }
            return $this->_redirect('*/*/');

        } else {
            $this->_getSession()->addError('Cannot save attribute without form data');
            return $this->_redirectReferer();
        }

        $redirectBack = $this->getRequest()->getParam('back', false);

        if ($data = $this->getRequest()->getPost()) {

            if(!$id = $this->getRequest()->getPost('attribute_id')){
                $id = $this->getRequest()->getParam('id');
            }

            $model = Mage::getModel('gene');
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->_getSession()->addError(
                        Mage::helper('')->__('This  no longer exists.')
                    );
                    $this->_redirect('*/*/');
                    return;
                }
            }

            // save model
            try {
                $model->addData($data);
                $this->_getSession()->setFormData($data);
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(
                    Mage::helper('')->__('The  has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('')->__('Unable to save the .'));
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack) {
                $this->_redirect('*/*/', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Is the user allowed to view this controller?
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/gene_bluefoot/setup_attributes');
    }
}