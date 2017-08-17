<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('entity');

        $form = new Varien_Data_Form(array (
                'id' => 'edit_form', 
                'action' => $this->getData('action'), 
                'method' => 'post', 
                'enctype' => 'multipart/form-data' ));

        $form->setUseContainer(true);


        if ($model->getEntityId()) {
            $form->addField('entity_id', 'hidden', array (
                'name' => 'entity_id',
                'value' => $model->getId()
            ));
        }else{
            $form->addField('attribute_set_id', 'hidden', array (
                'name' => 'attribute_set_id',
                'value' => $model->getAttributeSetId()
            ));
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
