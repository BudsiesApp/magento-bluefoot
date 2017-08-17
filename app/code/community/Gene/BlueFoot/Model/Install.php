<?php

class Gene_BlueFoot_Model_Install extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 'pending';
    const STATUS_INSTALLED = 'installed';
    const STATUS_UNINSTALLED = 'uninstalled';
    const STATUS_FAILED = 'failed';

    const DEFAULT_STATUS = 'pending';

    protected function _construct()
    {
        $this->_init("gene_bluefoot/install");
    }

    public function addLogMessage($message, $save = true)
    {
        $log = $this->getLog();
        if(!is_array($log)){
            $log = array();
        }
        $log[] = array(
            'time' => Mage::getModel('core/date')->gmtDate(),
            'status' => $this->getStatus(),
            'message' => $message
        );

        $this->setData('log', $log);

        if($save && $this->getId()){
            $this->save();
        }

    }

    public function addAdditionalInfo($key, $data)
    {
        if(is_object($data)){
            return $this;
        }

        $data = $this->getAdditional();
        if(!is_array($data)){
            $data = array();
        }

        $data[$key] = $data;

        $this->setAdditional($data);

        return $this;
    }

    public function initNewInstall()
    {
        $admin = Mage::getSingleton('admin/session')->getUser();

        $this->addAdditionalInfo('admin_id', $admin->getId());
        $this->addAdditionalInfo('admin_user', $admin->getUsername());

        return $this;
    }

    protected function _beforeSave()
    {
        if(!$this->getStatus()){
            $this->setStatus(self::DEFAULT_STATUS);
        }
    }

    public static function getConfigInstallers()
    {
        $collection = new Varien_Data_Collection();
        $configHelper = Mage::helper('gene_bluefoot/config');

        $installers = $configHelper->getInstallerConfig();
        $installerData = array();

        if($installers) {
            foreach ($installers->children() as $installer) {

                $installerId = $installer->getName();
                $installerObj = new Varien_Object();
                $installerObj->setId($installerId);
                foreach($installer->children() as $installerAttr){
                    $installerObj->setData($installerAttr->getName(), (string)$installerAttr);
                }

                $collection->addItem($installerObj);

            }
        }

        return $collection;
    }


}