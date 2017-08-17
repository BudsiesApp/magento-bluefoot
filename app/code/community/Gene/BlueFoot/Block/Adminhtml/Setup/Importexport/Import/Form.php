<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Importexport_Import_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Importexport_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array (
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/saveimportfile'),
            'method' => 'post',
            'enctype' => 'multipart/form-data' ));
        $form->setUseContainer(true);
        $this->setForm($form);


        $fieldset = $form->addFieldset('file_fieldset',
            array('legend'=>Mage::helper('gene_bluefoot')->__('File'))
        );

        $fieldset->addField('file_upload', 'file', array(
            'name'  => 'file_upload',
            'label' => Mage::helper('gene_bluefoot')->__('Import File'),
            'title' => Mage::helper('gene_bluefoot')->__('Import File'),
            'note'  => '',
            'required' => true,
        ));

        /*$fieldset = $form->addFieldset('url_fieldset',
            array('legend'=>Mage::helper('gene_bluefoot')->__('Url'))
        );

        $fieldset->addField('import_url', 'text', array(
            'name'  => 'import_url',
            'label' => Mage::helper('gene_bluefoot')->__('Import URL'),
            'title' => Mage::helper('gene_bluefoot')->__('Import'),
            'note'  => '',
            'required' => false,
        ));*/

        $fieldset = $form->addFieldset('options_fieldset',
            array('legend'=>Mage::helper('gene_bluefoot')->__('Options'))
        );

        return parent::_prepareForm();
    }

}