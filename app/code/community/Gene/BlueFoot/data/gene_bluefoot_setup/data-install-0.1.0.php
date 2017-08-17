<?php
$installer = $this;

$installer->startSetup();

$installables = Gene_BlueFoot_Model_Install::getConfigInstallers();
$setupInstallers = array();
foreach($installables as $key => $install){
    if($install->getSetupInstaller() && !$install->getInstallerRun()){
        $setupInstallers[$install->getId()] = $install;
    }
}


foreach($setupInstallers as $sInstallerId => $sInstaller){
    try{
        $bluefootInstaller = Gene_BlueFoot_Model_Installer::initConfigInstaller($sInstallerId);
        $handler = $bluefootInstaller->getFileHandler();
        $bluefootInstaller->setLiveMode();
        $bluefootInstaller->setExceptionOnError(false);
        $bluefootInstaller->installFromHandle($handler);

        //mark as installed
    }catch (Exception $e){

    }
}

$installer->endSetup();