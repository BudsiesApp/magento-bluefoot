<?php

/**
 * Class Gene_BlueFoot_Model_Installer
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Installer extends Gene_BlueFoot_Model_Installer_Abstract
{
    protected $_createdAttributes = array();

    protected $_createdBlocks = array();

    protected $_installLog = array();

    protected $_fileHandler;

    protected $_mode = 'live';

    protected $_errors = array();

    protected $_attributeData = null;

    protected $_dependencies = array(
        'attributes' => array(),
        'blocks' => array(),
        'content_types' => array(),
        'taxonomies' => array(),
    );

    protected $_runDependencies = false;


    protected function _construct()
    {
        return parent::_construct();
    }

    /**
     * @param Gene_BlueFoot_Model_Installer_Filehandler $handler
     * @return $this
     */
    public function setFileHandler(Gene_BlueFoot_Model_Installer_Filehandler $handler)
    {
        $this->_fileHandler = $handler;
        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_Installer_Filehandler
     */
    public function getFileHandler()
    {
        if(is_null($this->_fileHandler)){
            $this->_fileHandler = Mage::getModel('gene_bluefoot/installer_filehandler');
        }

        return $this->_fileHandler;
    }


    public function setMockMode()
    {
        $this->_mode = 'mock';
    }

    public function isMockMode()
    {
        return ($this->_mode == 'mock');
    }

    /**
     * @param $forceRebuild
     * @return array
     */
    public function getExistingAttributes($forceRebuild = false)
    {

        if(is_null($this->_attributeData) || $forceRebuild) {

            $this->_attributeData = array();
            $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter();

            foreach ($collection as $attr) {
                $this->_attributeData[$attr->getAttributeCode()] = $attr;
            }
        }

        return $this->_attributeData;
    }

    /**
     * @param $attributeCode
     * @return null
     */
    public function getAttributeData($attributeCode)
    {
        $attrData = $this->getExistingAttributes();
        return (array_key_exists($attributeCode, $attrData) ? $attrData[$attributeCode] : null);
    }

    /**
     * Use a file Handler to run install with - File handler allows multiple installer files
     *
     * @param Gene_BlueFoot_Model_Installer_Filehandler $handle
     * @return $this
     * @throws Exception
     */
    public function installFromHandle(Gene_BlueFoot_Model_Installer_Filehandler $handle = null)
    {
        if ($handle) {
            $this->setFileHandler($handle);
        }

        $files = $handle->getInstallerFiles();
        if (!count($files)) {
            throw new Exception('There are no data files to install');
        }

        foreach ($files as $installFile) {
            $json = file_get_contents($installFile);
            if (!$json) {
                $this->_errors[] = 'Failed to open file "' . $installFile . '"';
                continue;
            }

            $fileData = json_decode($json, true);
            if (!$fileData) {
                $this->_errors[] = 'Failed to decode file "' . $installFile . '"';
                continue;
            }

            $dataSets[] = $fileData;
        }

        if ($this->hasErrors()) {
            throw new Exception('Cannot complete installation due to errors.');
        }

        return $this->installData($dataSets);

    }

    public function installData($dataSets)
    {
        $fieldData = array();
        $taxonomyData = array();
        $blockData = array();
        $typesData = array();
        $appsData = array();

        //build up our data sets from all of the install files
        foreach($dataSets as $dataSet){

            $fieldData = array_merge($fieldData, (array_key_exists('attributes', $dataSet) ? $dataSet['attributes'] : array()));
            $taxonomyData = array_merge($taxonomyData, (array_key_exists('taxonomies', $dataSet) ? $dataSet['taxonomies'] : array()));
            $blockData = array_merge($blockData, (array_key_exists('content_blocks', $dataSet) ? $dataSet['content_blocks'] : array()));
            $typesData = array_merge($typesData, (array_key_exists('content_types', $dataSet) ? $dataSet['content_types'] : array()));
            $appsData = array_merge($appsData, (array_key_exists('content_apps', $dataSet) ? $dataSet['content_apps'] : array()));
        }


        //@TODO seperate below functionality out

        $connection = Mage::getModel('core/resource')->getConnection('core_write');
        if($this->getMode() == 'live'){
            $connection->beginTransaction();
        }


        try {

            if($this->getMode() == 'live') {
                $this->log('Live Installation');
            }else{
                $this->log('Mock Installation');
            }

            $this->log('-');
            $this->log("Starting Install of content fields", null, 'info', 'content_fields');
            $fieldInstaller = $this->installFields($fieldData);

            if ($fieldInstaller->hasErrors()) {
                $attrErrors = $fieldInstaller->getErrors();
                $this->log("Errors installing content fields", $attrErrors, 'error', 'content_fields');
            } else {
                $this->log("No errors installing content fields", null, 'info', 'content_fields');
            }

            $installedFields = $fieldInstaller->getCreatedEntities();
            $this->_createdEntities['attributes'] = $installedFields;
            $this->registerMultipleNewAttributes($installedFields);
            $installedAttrCodes = array_keys($installedFields);

            if(count($installedAttrCodes)){
                $this->log("Installed Fields", $installedAttrCodes, 'info', 'content_fields');
            }else{
                $this->log("No Installed Fields", null, 'info', 'content_fields');
            }

            $this->log('-');


            ////content Blocks installation //////

            if(count($blockData)) {

                $this->log("Starting install of content blocks", null, 'info', 'content_blocks');
                $blockInstaller = $this->installBlocks($blockData);


                if ($blockInstaller->hasErrors()) {
                    $blockErrors = $blockInstaller->getErrors();
                    $this->log('Errors installing content blocks', $blockErrors, 'error', 'content_blocks');
                } else {
                    $this->log("No errors installing content blocks", null, 'info', 'content_blocks');
                }

                $installedBlocks = $blockInstaller->getCreatedEntities();
                $this->_createdEntities['blocks'] = $installedBlocks;

                if (count($installedBlocks)) {
                    $this->log("Installed Content Blocks", $installedBlocks, 'info', 'content_blocks');
                } else {
                    $this->log("No Installed Content Blocks", null, 'info', 'content_blocks');
                }
            }else{
                $this->log("No Content Block data to install", null, 'info', 'content_blocks');
            }

            $this->log('-');


            ////content apps installation /////
            if(!empty($appsData)) {
                $this->log("Starting install of content Apps", null, 'info', 'content_apps');
                $appInstaller = $this->installContentApps($appsData);

                if ($appInstaller->hasErrors()) {
                    $appErrors = $appInstaller->getErrors();
                    $this->log('Errors installing content apps', $appErrors, 'error', 'content_apps');
                } else {
                    $this->log("No errors installing content apps", null, 'info', 'content_apps');
                }

                $installedApps = $appInstaller->getCreatedEntities();
                $this->_createdEntities['apps'] = $installedApps;

                if (count($installedApps)) {
                    $this->log("Installed Content Apps", $installedApps, 'info', 'content_apps');
                } else {
                    $this->log("No Installed Content Apps", $installedApps, 'info', 'content_apps');
                }

            }else{
                $this->log("No Content App data to install", null, 'info', 'content_apps');
            }

            $this->log('-');


            ////content types installation //////
            if(!empty($typesData)) {
                $this->log("Starting install of content types", null, 'info', 'content_types');
                $contentTypesInstaller = $this->installContentTypes($typesData);


                if ($contentTypesInstaller->hasErrors()) {
                    $typeErrors = $contentTypesInstaller->getErrors();
                    $this->log('Errors installing content types', $typeErrors, 'error', 'content_types');
                } else {
                    $this->log("No errors installing content types", null, 'info', 'content_types');
                }

                $installedTypes = $contentTypesInstaller->getCreatedEntities();
                $this->_createdEntities['content_types'] = $installedTypes;

                if (count($installedTypes)) {
                    $this->log("Installed Content Types", $installedTypes, 'info', 'content_types');
                } else {
                    $this->log("No Installed Content Types", null, 'info', 'content_types');
                }
            }else{
                $this->log("No Content Types data to install", null, 'info', 'content_types');
            }

            $this->log('-');


            $this->_runDependencies = true;

            $this->log("Installing Dependencies", null, 'info', null);
            //installed delayed entities
            $delayedAttributes = $this->_dependencies['attributes'];
            if(count($delayedAttributes)){
                $this->log("Installing Dependent Attributes", null, 'info', 'content_fields');
                $attributeInstaller = $this->installFields($delayedAttributes);
                $installedFields = $attributeInstaller->getCreatedEntities();

                $this->_createdEntities['attributes'] = $installedFields;
                $installedAttrCodes = array();
                if(is_array($installedFields)){
                    $this->registerMultipleNewAttributes($installedFields);
                    $installedAttrCodes = array_keys($installedFields);
                }

                if(count($installedAttrCodes)){
                    $this->log("Installed Fields", $installedAttrCodes, 'info', 'content_fields');
                }else{
                    $this->log("No Installed Fields", null, 'info', 'content_fields');
                }

                $this->log('-');

            }

        }catch (Exception $e){
            if($this->getMode() == 'live') {
                $connection->rollBack();
            }
            $this->log("Failed to install due to Exception: " . $e->getMessage(), null, 'info', 'content_types');
            return $this;
        }

        if($this->getMode() == 'live'){
            $connection->commit();
        }


        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (count($this->_errors) > 0);
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->_installLog;
    }

    /**
     * @param $group
     * @return array
     */
    public function getLogByGroup($group)
    {
        $groupLogs = array();
        $log = $this->getLog();
        foreach($log as $logEntry){
            if($logEntry->getGroup() == $group){
                $groupLogs[] = $logEntry;
            }
        }

        return $groupLogs;
    }

    /**
     * @param $type
     * @return array
     */
    public function getLogByType($type)
    {
        $typeLogs = array();
        $log = $this->getLog();
        foreach($log as $logEntry){
            if($logEntry->getType() == $type){
                $typeLogs[] = $logEntry;
            }
        }

        return $typeLogs;
    }

    public function log($message, $data = null, $type = 'info', $group = null, $indent = 0)
    {
        $this->_installLog[] = new Varien_Object(array(
            'message' => $message,
            'message_data' => $data,
            'type' => $type,
            'group' => $group,
            'indent' => $indent
        ));

        return $this;
    }

    /**
     * @param array $fieldData
     * @return Gene_BlueFoot_Model_Installer_Attribute
     * @throws Exception
     */
    public function installFields(array $fieldData)
    {
        $attributeInstaller = $this->_getInstaller('attribute');

        $attributeInstaller->registerNewEntities($this->getCreatedEntities());

        /**
         * @var $attributeInstaller Gene_BlueFoot_Model_Installer_Attribute
         */

        foreach($fieldData as $field)
        {
            $attrCode = $field['attribute_code'];

            if(isset($field['additional_data']) && is_array($field['additional_data'])){
                $field['additional_data'] = serialize($field['additional_data']);
            }

            if(
                isset($field['entity_allowed_block_type']) && $field['entity_allowed_block_type'] != ''
                && isset($field['frontend_input']) && $field['frontend_input'] == 'child_entity'
            ){
                if(!$this->_runDependencies){
                    $this->_dependencies['attributes'][$attrCode] = $field;
                    continue;
                }
            }


            $attributeInstaller->createAttribute($attrCode, $field);
        }

        return $attributeInstaller;
    }

    /**
     * @param array $blockData
     * @return Gene_BlueFoot_Model_Installer_Block
     * @throws Exception
     */
    public function installBlocks(array $blockData)
    {
        $blockInstaller = $this->_getInstaller('block');
        /**
         * @var $blockInstaller Gene_BlueFoot_Model_Installer_Block
         */

        $blockInstaller->registerMultipleNewAttributes($this->getNewAttributes());

        foreach($blockData as $block){
            $blockInstaller->createContentBlock($block['identifier'], $block);
        }


        return $blockInstaller;

    }

    public function installContentApps(array $appData)
    {
        $appInstaller = $this->_getInstaller('app');
        /**
         * @var $appInstaller Gene_BlueFoot_Model_Installer_App
         */

        foreach($appData as $app){
            $appInstaller->createFullApp(uniqid(), $app);
        }

        return $appInstaller;
    }

    public function installContentTypes(array $typeData)
    {
        $contentTypeInstaller = $this->_getInstaller('type');
        /**
         * @var $contentTypeInstaller Gene_BlueFoot_Model_Installer_Type
         */

        $contentTypeInstaller->registerMultipleNewAttributes($this->getNewAttributes());

        foreach($typeData as $type){
            $contentTypeInstaller->createContentType($type['identifier'], $type);
        }

        return $contentTypeInstaller;
    }


    /**
     * @param $type
     * @return Gene_BlueFoot_Model_Installer_Abstract
     * @throws Exception
     */
    protected function _getInstaller($type)
    {
        $installerClass = Mage::getSingleton('gene_bluefoot/installer_' . $type);
        if(!$installerClass){
            throw new Exception('No such installer class for type "'.$type.'".');
        }

        $installerClass->setExceptionOnError($this->getExceptionOnError());

        if($this->isMockMode()){
            $installerClass->setMockMode();
        }


        return $installerClass;
    }

    /**
     * @param $type
     * @return Gene_BlueFoot_Model_Installer_Abstract
     * @throws Exception
     */
    public function getTypeInstaller($type)
    {
        return $this->_getInstaller($type);
    }

    public static function initConfigInstaller($installerId)
    {
        $installer = Mage::getModel('gene_bluefoot/installer');
        $configHelper = Mage::helper('gene_bluefoot/config');
        $installerConfig = $configHelper->getInstallerConfig($installerId);

        if(!$installerConfig){
            throw new Mage_Exception('No config found for installer "'.$installerId.'"');
        }else{
            $installerConfig = new Varien_Object($installerConfig->asArray());
        }

        $installerModule = $installerConfig->getModule();
        $installerModule = str_replace(" ", "_", ucwords(str_replace("_", " ", $installerModule)));

        $installDir = Mage::getModuleDir('data', $installerModule);
        $installerFile = $installerConfig->getFile();
        $installerFileAbs = $installDir . DS . $installerFile;

        if(!file_exists($installerFileAbs)){
            throw new Mage_Exception('Cannot find installer file ('.$installerFile.')');
        }

        if(!is_readable($installerFileAbs)){
            throw new Mage_Exception('Installer file ('.$installerFile.') cannot be read. Please check file permissions.');
        }

        $handler = $installer->getFileHandler();
        $handler->addInstallerFile($installerFileAbs);

        return $installer;
    }



}