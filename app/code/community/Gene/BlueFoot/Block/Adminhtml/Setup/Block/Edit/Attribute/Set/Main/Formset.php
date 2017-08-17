<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Attribute_Set_Main_Formset
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $data = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend'=> Mage::helper('gene_bluefoot')->__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'hidden', array(
            'name' => 'attribute_set_name',
            'value' => ''
        ));

        if( !$this->getRequest()->getParam('id', false) ) {
            $fieldset->addField('gotoEdit', 'hidden', array(
                'name' => 'gotoEdit',
                'value' => '1'
            ));

        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
