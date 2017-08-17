<?php

/**
 * Class Gene_BlueFoot_Helper_Version
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Helper_Version extends Mage_Core_Helper_Abstract
{
    /**
     * Does the system need an update?
     */
    const NEEDS_UPDATE_XML_PATH = 'gene_bluefoot/version/needs_update';

    /**
     * Have we checked the version recently?
     */
    const CHECKED_VERSION_XML_PATH = 'gene_bluefoot/version/checked_version';

    /**
     * Check the version of the module against the master
     */
    public function checkVersion()
    {
        // Retrieve the last time the system checked the version
        $checkedTime = Mage::getStoreConfig(self::CHECKED_VERSION_XML_PATH);

        // Only do a check if we haven't checked in a week
        if ($checkedTime <= strtotime('-1 week')) {

            // Check the version checker URL
            $request = new Varien_Http_Client('https://www.bluefootcms.com/version/');
            $request->setParameterGet('version', (string) Mage::getConfig()->getModuleConfig('Gene_BlueFoot')->version);

            // Requires domain to validate licence
            $request->setParameterGet('domain', Mage::getBaseUrl());

            // Perform a request to the version server
            $response = $request->request(Zend_Http_Client::GET);

            // Was the request a success?
            if ($response->getStatus() == 200) {
                $body = Mage::helper('core')->jsonDecode($response->getBody());
                if (isset($body['success'])) {
                    if ((!isset($body['latest']) || isset($body['latest']) && $body['latest'] != true)) {
                        // Set the flag that we need to update
                        Mage::app()->getConfig()->saveConfig(self::NEEDS_UPDATE_XML_PATH, 1);
                    } else {
                        Mage::app()->getConfig()->saveConfig(self::NEEDS_UPDATE_XML_PATH, 0);
                    }
                }
            }

            // We've checked the version
            Mage::app()->getConfig()->saveConfig(self::CHECKED_VERSION_XML_PATH, time());
        }
    }
}