<?php

/**
 * Class Gene_BlueFoot_Model_Installer_FileHandler
 *
 * @author Mark Wallman <mark@gene.co.uk>
 *
 */
class Gene_BlueFoot_Model_Installer_Filehandler extends Varien_Object
{
    protected $_installerFiles = array();
    protected $_installerInfoFiles = array();

    protected $_tmpInstallDirBase;
    protected $_tmpInstallDir;

    protected $_allowedFileExtensions = array(
        'json'
    );

    protected $_infoFileExtensions = array(
        'md',
        'info',
        'html'
    );


    protected function _construct()
    {
        $this->_tmpInstallDirBase = Mage::getBaseDir('var') . DS . 'import' . DS . 'genecms';
        return parent::_construct();
    }

    public function addInstallerZipFile($file)
    {
        $this->_checkFileIsReadable($file);

        $filename = pathinfo($file, PATHINFO_FILENAME);

        $zip = new ZipArchive();
        $res = $zip->open($file);
        if($res !== true){
            throw new Exception('Failed to open zip file (code: '.$res.')');
        }

        $installDir = $this->_createTmpInstallerDir($filename.'_'.time());

        $zip->extractTo($installDir);

        $this->addInstallerDir($installDir);

        return $this;

    }

    public function addInstallerDir($dir)
    {
        $this->_checkFileIsReadable($dir);

        $file = new Varien_Io_File();
        $file->open(array('path' => $dir));


        $files = $file->ls();

        foreach($files as $fileInfo){
            $file = $dir . DS . $fileInfo['text'];

            $this->addInstallerFile($file);
        }

        return $this;

    }

    public function addInstallerFile($file)
    {
        $this->_checkFileIsReadable($file);

        $fileInfo = pathinfo($file);

        if($this->isInfoFileExtension($fileInfo['extension'])){
            $this->_installerInfoFiles[] = $file;
        }elseif($this->isAllowedFileExtension($fileInfo['extension'])){
            $this->_installerFiles[] = $file;
        }


        return $this;
    }

    public function getTempInstallDirectory()
    {
        return $this->_tmpInstallDir;
    }

    public function getInstallerFiles()
    {
        return $this->_installerFiles;
    }

    public function getInstallerInfoFiles()
    {
        return $this->_installerInfoFiles;
    }

    public function isInfoFileExtension($fileExtension)
    {
        return in_array($fileExtension, $this->_infoFileExtensions);
    }

    public function isAllowedFileExtension($fileExtension)
    {
        return in_array($fileExtension, $this->_allowedFileExtensions);
    }



    protected function _createTmpInstallerDir($name = null)
    {
        if(is_null($name)){
            $name = uniqid();
        }

        $installDir = $this->_tmpInstallDirBase . DS . $name;

        $file = new Varien_Io_File();
        $res = $file->checkAndCreateFolder($installDir);
        if(!$res){
            throw new Exception('Failed to create temp install folder "'.$installDir.'"');
        }

        $this->_tmpInstallDir = $installDir;

        return $this->_tmpInstallDir;
    }


    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    protected function _checkFileIsReadable($file)
    {
        if(is_readable($file)) {
            return true;
        }

        if(file_exists($file)){
            throw new Exception('Import File does not exist: "'.$file.'"');
        }

        throw new Exception('Import file is not readable: "'.$file.'"');

    }
}