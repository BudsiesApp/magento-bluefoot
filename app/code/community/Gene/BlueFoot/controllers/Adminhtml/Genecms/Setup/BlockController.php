<?php
class Gene_BlueFoot_Adminhtml_Genecms_Setup_BlockController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/content_blocks')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('Page Builder Blocks'), $this->__('Page Builder Blocks'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('Page Builder Blocks'));

        return $this;
    }

    public function indexAction()
    {
        $grid = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block');

        $this->_initAction()
            ->_addContent($grid);

        return $this->renderLayout();
    }

    public function newAction()
    {
        return $this->_forward('edit');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {


                $model = Mage::getModel('gene_bluefoot/type');
                $model->load($id);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('gene_bluefoot')->__('Unable to find content block to delete.'));
                }
                $model->delete();


                $this->_getSession()->addSuccess(
                    Mage::helper('gene_bluefoot')->__('The content block has been successfully deleted.')
                );


                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('gene_bluefoot')->__('An error occurred while deleting content block. Please check logs and try again.')
                );
                Mage::logException($e);
            }


            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }

        $this->_getSession()->addError(
            Mage::helper('gene_bluefoot')->__('Unable to find content block without Id passed.')
        );


        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/content_blocks')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('Page Builder Block'), $this->__('Page Builder Block'))
            ->_addBreadcrumb($this->__('Edit'), $this->__('Edit'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('Page Builder Block'))->_title($this->__('Edit'));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("gene_bluefoot/type")->load($id);
        $attributeSet = $model->getAttributeSet();

        Mage::register("type_data", $model);
        Mage::register('current_attribute_set', $attributeSet);

        if ($id && !$model->getId()) {

            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("gene_bluefoot")->__("Block no longer exists."));
            $this->_redirect("*/*/");
        }

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_block_edit"))->_addLeft($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_block_edit_tabs"));

        return $this->renderLayout();
    }

    /**
     * Ajax action to create a new group
     *
     * @return \Gene_BlueFoot_Adminhtml_Genecms_Setup_BlockController
     * @throws \Exception
     */
    public function createGroupAction()
    {
        if (($code = $this->getRequest()->getParam('code'))
            && ($name = $this->getRequest()->getParam('name'))
            && ($icon = $this->getRequest()->getParam('icon')))
        {
            if ($this->getRequest()->getParam('sort_order') && is_numeric(trim($this->getRequest()->getParam('sort_order')))) {
                $sort = $this->getRequest()->getParam('sort_order');
            } else {
                $sort = 0;
            }
            $group = Mage::getModel('gene_bluefoot/type_group');
            $group->addData(array(
                'code' => $code,
                'name' => $name,
                'icon' => $icon,
                'sort_order' => $sort
            ));
            if ($group->save()) {
                return $this->returnJson(array('success' => true, 'id' => $group->getId()));
            }
        }

        return $this->returnJson(array('success' => false, 'message' => Mage::helper('gene_bluefoot')->__('Required data missing')));
    }

    /**
     * Return JSON to the browser
     *
     * @param           $array
     * @param bool|true $doCheck
     *
     * @return $this
     */
    public function returnJson($array, $doCheck = true) {

        // If we're trying to return JSON only allow over an Ajax request
        if(!$this->getRequest()->isAjax() && $doCheck) {
            $this->getResponse()->setBody('Direct action disallowed.');
            return $this;
        }

        // If the data is an array we'll need to encode it
        if(is_array($array)) {
            $array = Mage::helper('core')->jsonEncode($array);
        }

        // Set the content type
        $this->getResponse()->setHeader('Content-type', 'application/json');

        // Set the body of the return
        $this->getResponse()->setBody($array);

        // Return the controller model
        return $this;

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
                    $model->initNewBlockType();
                }

                $setsData = (isset($data['sets_json']) && $data['sets_json'] != '') ? $data['sets_json'] : false;
                unset($data['sets_json']);

                $model->addData($data);

                $model->save();

                //Save groups and sets data
                if($setsData){
                    $setsData =  Mage::helper('core')->jsonDecode($setsData);
                    $attributeSet = $model->getAttributeSet();
                    $attributeSet->organizeData($setsData);
                    $attributeSet->validate();
                    $attributeSet->save();
                }


                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Block was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setTypeData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }

                $this->_redirect("*/*/");
                return;


            }catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setTypeData($this->getRequest()->getPost());
                return $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));

            }

        }

        return $this->_redirect("*/*/");

    }

    /**
     * Is the user allowed to view this controller?
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/gene_bluefoot/content_blocks');
    }
}