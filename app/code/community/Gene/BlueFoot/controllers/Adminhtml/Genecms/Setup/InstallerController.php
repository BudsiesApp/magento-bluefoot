<?php

/**
 * Class Gene_BlueFoot_Adminhtml_Genecms_Setup_InstallerController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Genecms_Setup_InstallerController extends Gene_BlueFoot_Controller_Adminhtml_Abstract
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->_usedModuleName = 'gene_bluefoot';

        $this->loadLayout()
            ->_setActiveMenu('system/bluefoot/installer')
            ->_addBreadcrumb($this->__('BlueFoot'), $this->__('BlueFoot'))
            ->_addBreadcrumb($this->__('Installer'), $this->__('Installer'));

        $this->_title($this->__('BlueFoot'))->_title($this->__('Installer'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent(
                $this->getLayout()
                    ->createBlock('gene_bluefoot/adminhtml_setup_installer')
            );

        return $this->renderLayout();

    }

    /**
     * @return $this
     */
    public function importfileAction()
    {
        $this->_initAction()
            ->_addContent(
                $this->getLayout()
                    ->createBlock('gene_bluefoot/adminhtml_setup_importexport_import')
                    ->setData('action', $this->getUrl('*/*/saveImportFile'))
            );

        return $this->renderLayout();
    }

    public function saveimportfileAction()
    {
        $importPath = Mage::getBaseDir('var') . DS . 'import'. DS . 'genecms';

        if(!is_dir($importPath)){
            $file = new Varien_Io_File();
            $res = $file->checkAndCreateFolder($importPath);
            if(!$res){
                throw new Exception('Failed to create import folder "'.$importPath.'"');
            }
        }

        if(!is_dir_writeable($importPath)){
            throw new Exception('Import Directory is not writable ('.$importPath.')');
        }



        if (isset($_FILES['file_upload']['name']) && $_FILES['file_upload']['name'] != '') {
            try {
                $uploader = new Varien_File_Uploader('file_upload');
                $uploader->setAllowedExtensions(array('zip','json'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('var') . DS . 'import'. DS . 'genecms';
                $result = $uploader->save($importPath);

                if(!$result){
                    throw new Exception('Failed to upload file, unknown reason');
                }

                $uploadedFilePath = $result['path'] . DS . $result['file'];
                $uploadedFile = $result['file'];

            } catch (Exception $e) {
                throw new Exception('Failed to upload import file: ' . $e->getMessage());
            }

            $install = Mage::getModel('gene_bluefoot/install');
            $install->initNewInstall();
            $install->setName('Admin import from file');
            $install->setImportFile($uploadedFilePath);
            $install->addLogMessage('Created new install from file "'.$uploadedFile.'"');
            $install->save();

            return $this->_redirect('*/genecms_setup_installer/mockImport', array('id' => $install->getId()));

        }else{
            $this->_getSession()->addError('Please upload a file to install');
            return $this->_redirectReferer();
        }
    }

    protected function _setupDbInstaller($installerId)
    {
        $installer = Mage::getModel('gene_bluefoot/installer');

        $installModel = Mage::getModel('gene_bluefoot/install');
        $installModel->load($installerId);
        if(!$installModel->getId()){
            throw new Mage_Exception('Cannot find install with id: ' . $installerId);
        }

        $installerFileAbs = $installModel->getImportFile();

        if(!file_exists($installerFileAbs)){
            throw new Mage_Exception('Cannot find installer file ('.$installerFileAbs.')');
        }

        if(!is_readable($installerFileAbs)){
            throw new Mage_Exception('Installer file ('.$installerFileAbs.') cannot be read. Please check file permissions.');
        }

        $handler = $installer->getFileHandler();
        $handler->addInstallerFile($installerFileAbs);

        return $installer;
    }

    /**
     * @param $installerId
     * @return Gene_BlueFoot_Model_Installer
     * @throws Mage_Exception
     */
    protected function _setupConfigInstaller($installerId)
    {
        $installer = Gene_BlueFoot_Model_Installer::initConfigInstaller($installerId);

        return $installer;
    }

    public function mockImportAction()
    {
        if($installerId = $this->getRequest()->getParam('id')){
            $type = 'db';
        }else{
            $installerId = $this->getRequest()->getParam('installer_code');
            $type = 'config';
        }


        try {
            if($type == 'config') {
                $installer = $this->_setupConfigInstaller($installerId);
                $handler = $installer->getFileHandler();
            }elseif($type == 'db'){
                $installer = $this->_setupDbInstaller($installerId);
                $handler = $installer->getFileHandler();
            }else{
                throw new Mage_Exception('Incorrect installation type');
            }

            $installer->setMockMode();
            $installer->setExceptionOnError(false);
            $installer->installFromHandle($handler);

        }catch (Mage_Exception $e){
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect('*/*/importfile');
        }catch(Exception $e){
            if(Mage::getIsDeveloperMode()){
                $this->_getSession()->addError($e->getMessage());
            }else{
                $this->_getSession()->addError('An exception occurred during the mock import. Please refer to the logs for details.');
            }

            Mage::logException($e);

            $this->_redirectReferer();
        }

        $this->_getSession()->addNotice('The mock import has finished successfully. Please review the output below and confirm you actions.');

        Mage::register('current_installer', $installer);
        Mage::register('current_installer_id', $installerId);
        Mage::register('current_installer_type', $type);

        $this->_initAction()
            ->_addContent(
                $this->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_installer_review')
            );

        return $this->renderLayout();

    }

    public function liveImportAction()
    {
        if($installerId = $this->getRequest()->getParam('id')){
            $type = 'db';
        }else{
            $installerId = $this->getRequest()->getParam('installer_code');
            $type = 'config';
        }

        try {
            if($type == 'config') {
                $installer = $this->_setupConfigInstaller($installerId);
                $handler = $installer->getFileHandler();
            }elseif($type == 'db'){
                $installer = $this->_setupDbInstaller($installerId);
                $handler = $installer->getFileHandler();
            }else{
                throw new Mage_Exception('Incorrect installation type');
            }

            $installer->setLiveMode();
            $installer->setExceptionOnError(false);
            $installer->installFromHandle($handler);

        }catch (Mage_Exception $e){
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect('*/*/importfile');
        }catch(Exception $e){
            if(Mage::getIsDeveloperMode()){
                $this->_getSession()->addError($e->getMessage());
            }else{
                $this->_getSession()->addError('An exception occurred during the mock import. Please refer to the logs for details.');
            }

            Mage::logException($e);

            $this->_redirectReferer();
        }

        $this->_getSession()->addSuccess('Successfully finished installing');
        return $this->_redirect('*/*/');
    }

    /**
     * Is the user allowed to view this controller?
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/gene_bluefoot/installer');
    }
}