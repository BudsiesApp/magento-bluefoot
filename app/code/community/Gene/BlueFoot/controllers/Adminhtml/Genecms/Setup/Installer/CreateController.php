<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_Setup_Installer_CreateController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_Setup_Installer_CreateController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    public function startAction()
    {
        $this->_title($this->__("Gene CMS"));
        $this->_title($this->__("Installer"));
        $this->_title($this->__("Create"));

        $this->loadLayout();

        $this->_addContent($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_installer_create"))
            ->_addLeft($this->getLayout()->createBlock("gene_bluefoot/adminhtml_setup_installer_create_tabs"));


        return $this->renderLayout();
    }

    public function confirmAction()
    {
        $settings = $this->getRequest()->getPost('settings', array());
        $exports = $this->getRequest()->getPost('export', array());

        if(empty($exports)){
            $this->_getSession()->addError('You must select at least one item to be exported');
            return $this->_redirectReferer();
        }

        $exporterModel = Mage::getModel('gene_bluefoot/exporter');

        $blockSettings = array_key_exists('blocks', $settings) && is_array($settings['blocks']) ? new Varien_Object($settings['blocks']) : new Varien_Object();
        //$typeSettings = array_key_exists('content_types', $settings) && is_array($settings['content_types']) ? new Varien_Object($settings['content_types']) : new Varien_Object();
        $appSettings = array_key_exists('apps', $settings) && is_array($settings['apps']) ? new Varien_Object($settings['apps']) : new Varien_Object();
        $attrSettings = array_key_exists('attributes', $settings) && is_array($settings['attributes']) ? new Varien_Object($settings['attributes']) : new Varien_Object();

        $blockExports = array_key_exists('blocks', $exports) && is_array($exports['blocks']) ? $exports['blocks'] : array();
        $appExports = array_key_exists('apps', $exports) && is_array($exports['apps']) ? $exports['apps'] : array();
        //$typeExports = array_key_exists('content_types', $exports) && is_array($exports['content_types']) ? $exports['content_types'] : array();
        $attrExports = array_key_exists('attributes', $exports) && is_array($exports['attributes']) ? $exports['attributes'] : array();


        $exporterModel->addExport('content_blocks', $blockExports, $blockSettings);
        $exporterModel->addExport('apps', $appExports, $appSettings);
        //$exporterModel->addExport('content_types', $typeExports, $typeSettings);
        $exporterModel->addExport('attributes', $attrExports, $attrSettings);

        $json = $exporterModel->exportAsJson();

        $file_name = $this->getRequest()->getPost('json_file_name');

        if($file_name == ''){
            $file_name = 'bluefoot';
        }

        $this->_prepareDownloadResponse($file_name . '.json', $json);

        return $this;
    }

}