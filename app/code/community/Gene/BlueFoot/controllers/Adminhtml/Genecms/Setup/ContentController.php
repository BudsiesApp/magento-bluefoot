<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_Setup_ContentController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_Setup_ContentController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    protected function _initAction()
    {
        $this->loadLayout();
        return $this;
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $this->_title($this->__("Gen Cms"));
        $this->_title($this->__("Content Types"));

        $grid = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_content');

        $this->_initAction()
            ->_addContent($grid);

        return $this->renderLayout();
    }

    public function newAction()
    {
        return $this->_forward('edit');
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function editAction()
    {
        $this->_title($this->__("Gene CMS"));
        $this->_title($this->__("Content Type"));
        $this->_title($this->__("Edit"));

        $this->loadLayout();
        $this->_setActiveMenu("gene_cms/type");

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("gene_bluefoot/type")->load($id);
        $attributeSet = $model->getAttributeSet();

        Mage::register("type_data", $model);
        Mage::register('current_attribute_set', $attributeSet);

        if ($id && !$model->getId()) {

            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("gene_bluefoot")->__("Content Type no longer exists."));
            $this->_redirect("*/*/");
        }

        if(!$model->getId()){
            if($appId = $this->getRequest()->getParam('app_id')){
                $model->setData('app_id', $appId);
            }
        }

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_content_edit"))
            ->_addLeft($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_content_edit_tabs"));

        return $this->renderLayout();
    }

    /**
     * @return $this|Mage_Core_Controller_Varien_Action|void
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        unset($data['fields']);

        if ($data) {

            $isNewType = false;

            try{

                $model = Mage::getModel("gene_bluefoot/type");
                if($this->getRequest()->getParam("id")){
                    $model->load($this->getRequest()->getParam("id"));
                }

                if(!$model->getId()){
                    $isNewType = true;
                    $model->initNewContentType();
                }

                $setsData = (isset($data['sets_json']) && $data['sets_json'] != '') ? $data['sets_json'] : false;
                unset($data['sets_json']);


                try {
                    $model->getResource()->beginTransaction();

                    $model->addData($data);

                    $model->save();

                    //Save groups and sets data
                    if ($setsData) {
                        $setsData = Mage::helper('core')->jsonDecode($setsData);
                        $setsData['attribute_set_name'] = $model->generateAttributeSetName();

                        //if it's a new type we need to adjust the data as it will be using the skeleton attribute set and groups
                        if ($isNewType) {
                            $setsData = $model->processNewSetData($setsData);
                        }

                        $attributeSet = $model->getAttributeSet();
                        $attributeSet->organizeData($setsData);
                        $attributeSet->validate();
                        $attributeSet->save();
                    }

                    $model->validate();

                    $model->getResource()->commit();

                }catch (Exception $e){
                    $model->getResource()->rollBack();
                    throw $e;
                }


                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Content type was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setTypeData(false);

                if ($this->getRequest()->getParam("back")) {
                    return $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                    return;
                }

                $this->_redirect("*/genecms_setup_app/edit", array("id" => $model->getContentApp()->getId(), 'tab' => 'content_types'));
                return;


            }catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setTypeData($this->getRequest()->getPost());
                return $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));

            }

        }

        return $this->_redirect("*/*/");

    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {


                $model = Mage::getModel('gene_bluefoot/type');
                $model->load($id);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('gene_bluefoot')->__('Unable to find Content Type to delete or Content Type no longer exists.'));
                }

                $attributeSet = $model->getAttributeSet();

                $model->delete();

                $attributeSet->setAttributeSetName($attributeSet->getAttributeSetName() . ' - Deleted {'.time().'}');
                $attributeSet->save();

                $this->_getSession()->addSuccess(
                    Mage::helper('gene_bluefoot')->__('The Content Type has been successfully deleted.')
                );


                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('gene_bluefoot')->__('An error occurred while deleting Content Type. Please review error log and try again.')
                );
                Mage::logException($e);
            }


            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }

        $this->_getSession()->addError(
            Mage::helper('gene_bluefoot')->__('Unable to find a Content Type to delete. No ID.')
        );


        $this->_redirect('*/*/');

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