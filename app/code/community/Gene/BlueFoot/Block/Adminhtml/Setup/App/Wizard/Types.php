<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Types
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Types extends Mage_Adminhtml_Block_Widget_Form
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('gene/bluefoot/setup/app/wizard/types.phtml');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                "id" => "edit_form",
                "action" => $this->getUrl("*/*/saveStep", array("id" => $this->getRequest()->getParam("app_id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getNewContentTypeUrl()
    {
        return $this->getUrl('adminhtml/genecms_setup_appwizard/addContentType');
    }

    public function getRemoveUrl($typeIdentifier)
    {
        return $this->getUrl('adminhtml/genecms_setup_appwizard/removeContentType', array('identifier' => $typeIdentifier));
    }

    public function getNewTypeHtml()
    {
        $model = Mage::getModel("gene_bluefoot/type");
        $attributeSet = $model->getAttributeSet();

        Mage::register("type_data", $model);
        Mage::register('current_attribute_set', $attributeSet);

        $block = $this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_content_wizard");
        $html = $block->toHtml();

        $fieldsBlock = $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_attribute_set_main')->setContentType('content');

        $html .= $fieldsBlock->toHtml();

        return $html;
    }

    public function showNewTypeForm()
    {
        if($this->getParentBlock() && $this->getParentBlock()->getCurrentStep() && $this->getParentBlock()->getCurrentStep()->getAddNewContentType()){
            return true;
        }

        return false;
    }

    public function hasNewContentTypes()
    {
        return (bool)$this->getNewContentTypes();
    }

    public function getNewContentTypes()
    {
        if($wizardModel = Mage::registry('app_wizard')){

            $contentTypeData = $wizardModel->getStepData('content_type_data');
            if($contentTypeData && isset($contentTypeData['type_data'])){
                $typesData = $contentTypeData['type_data'];
                if(is_array($typesData)){
                    return $typesData;
                }
            }
        }

        return false;
    }
}