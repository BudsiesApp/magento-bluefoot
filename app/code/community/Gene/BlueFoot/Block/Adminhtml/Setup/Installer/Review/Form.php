<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Review_Form
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Review_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gene/bluefoot/setup/installer/mock/review.phtml');
    }

    protected function _prepareForm()
    {
        $installerId = Mage::registry('current_installer_id');
        $installerType = Mage::registry('current_installer_type');
        if(!$installerType){
            $installerType = 'config';
        }

        if($installerType == 'db'){
            $actionUrl = $this->getUrl('*/*/liveImport', array('id' => $installerId));
        }else{
            $actionUrl = $this->getUrl('*/*/liveImport', array('installer_code' => $installerId));
        }

        $form = new Varien_Data_Form(array (
            'id' => 'edit_form',
            'action' => $actionUrl,
            'method' => 'post',
            'enctype' => 'multipart/form-data' ));
        $form->setUseContainer(true);
        $this->setForm($form);


        $installer = Mage::registry('current_installer');

        /**
         * @var $installer Gene_BlueFoot_Model_Installer
         */


        $fieldset = $form->addFieldset("gene_cms_installer_review", array("legend" => Mage::helper("gene_bluefoot")->__("Report"), 'class' => 'fieldset-wide'));

        $fieldset->addField('import_review', 'note', array(
            'label'     => Mage::helper('gene_bluefoot')->__('Import Log'),
            'name'      => 'import_review',
            'text'  => 'Please review the log from the mock import and confirm if you wish to proceed.',
            'disabled' => true,
            'readonly' => true,
        ));

        return parent::_prepareForm();
    }

    /**
     * @return Gene_BlueFoot_Model_Installer
     */
    public function getInstaller()
    {
        $installer = Mage::registry('current_installer');
        return $installer;
    }
}