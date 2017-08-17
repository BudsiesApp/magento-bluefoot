<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Edit_Tab_Main
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_term');

        /**
         * @var $model Gene_BlueFoot_Model_Entity
         */

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('genecms_');

        $fieldset = $form->addFieldset('base_fieldset', array (
            'legend' => Mage::helper('gene_bluefoot')->__('Information'),
            'class' => 'fieldset-wide' ));

        if ($model->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', array (
                'name' => 'entity_id' ));

            $fieldset->addField("entity_info_id", "note", array(
                'text' => $model->getId(),
                'label' => 'Entity ID:'
            ));

        }else{
            $fieldset->addField('taxonomy_id', 'hidden', array (
                    'name' => 'taxonomy_id' )
            );

            $fieldset->addField('attribute_set_id', 'hidden', array (
                'name' => 'attribute_set_id' )
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }


}
