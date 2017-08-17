<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            "id" => "edit_form",
            "action" => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("app_id"))),
            "method" => "post",
            "enctype" => "multipart/form-data",
                )
        );
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        return $this;
    }

}
