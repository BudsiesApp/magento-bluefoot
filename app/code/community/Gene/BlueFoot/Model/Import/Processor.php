<?php

/**
 * Class Gene_BlueFoot_Model_Import_Processor
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Import_Processor extends Varien_Object
{
    protected $_dataSets = array();

    protected $_errors = array();

    /**
     * Store the created apps
     *
     * @var array
     */
    protected $_apps = array();

    public function addDataSet(Gene_BlueFoot_Model_Migration_Dataset_Interface $dataSet, $identifier = null)
    {
        if (is_null($identifier)) {
            $identifier = uniqid('dataset-');
        }
        $this->_dataSets[$identifier] = $dataSet;

        return $this;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return array
     */
    public function getAllDataSets()
    {
        return $this->_dataSets;
    }

    /**
     * @param $identifier
     *
     * @return Gene_BlueFoot_Model_Migration_Dataset_Interface|bool
     */
    public function getDataSet($identifier)
    {
        return array_key_exists($identifier, $this->_dataSets) ? $this->_dataSets[$identifier] : false;
    }

    public function runProcess()
    {
        $dataSets = $this->getAllDataSets();
        foreach ($dataSets as $dataSetKey => $dataSet) {

            /**
             * @var $dataSet Gene_BlueFootMigration_Model_Dataset
             */

            $allDataCollections = $dataSet->getDatasetCollection();

            foreach ($allDataCollections as $dataCollectionType => $dataCollection) {
                /**
                 * @var $dataCollection Gene_BlueFootMigration_Model_Dataset_Collection
                 */
                switch ($dataCollectionType) {
                    case 'app':
                        $this->processAppDataCollection($dataCollection);
                        break;
                    default:
                        throw new Exception('Cannot process data collection type: ' . $dataCollectionType);
                }

            }
        }

        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_Installer
     */
    protected function _getInstaller()
    {
        return Mage::getModel('gene_bluefoot/installer');
    }

    /**
     * Return an array of created apps
     *
     * @return array
     */
    public function getApps()
    {
        return $this->_apps;
    }

    public function processAppDataCollection($dataCollection)
    {
        $installer = $this->_getInstaller();
        $installer->setLiveMode(true);
        $appInstaller = $installer->getTypeInstaller('app');
        /**
         * @var $appInstaller Gene_BlueFoot_Model_Installer_App
         */

        $entityInstaller = $installer->getTypeInstaller('entity');
        /**
         * @var $entityInstaller Gene_BlueFoot_Model_Installer_Entity
         */

        $dataSetEntities = array();
        $count = 0;

        foreach ($dataCollection->getItems() as $dataAppItem) {

            try {

                $resource = Mage::getSingleton('core/resource')->getConnection('core_write');
                $resource->beginTransaction();

                $count++;
                /**
                 * @var $dataAppItem Gene_BlueFootMigration_Model_Dataset_App
                 */

                $contentTypeEntities = array();

                //first create APP
                $app = $appInstaller->createApp(uniqid(), $dataAppItem->getData());

                $appName = $app->getTitle();
                $appFriendlyName = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($appName));

                $appId = $app->getId();

                $contentTypes = $dataAppItem->getContentTypes();
                foreach ($contentTypes as $contentTypeItem) {
                    /**
                     * @var $contentTypeItem Gene_BlueFootMigration_Model_Dataset_App_Contenttype
                     */

                    $contentTypeItem->setAppId($appId);

                    $typeEntities = $contentTypeItem->getEntities();

                    $contentTypeIdentifier = $contentTypeItem->getIdentifier();
                    if (!$contentTypeIdentifier) {
                        $contentTypeIdentifier = $contentTypeItem->getCode() ? $contentTypeItem->getCode() : $appFriendlyName . '-' . preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($contentTypeItem->getTitle()));
                        $contentTypeItem->setIdentifier($contentTypeIdentifier);
                    }
                    if ($contentTypeItem->getTitle()) {
                        $contentTypeItem->setName($contentTypeItem->getTitle());
                    }

                    if (!$contentTypeItem->getAttributeData()) {
                        $contentTypeItem->setUseDefaultAttributes(true);
                    }

                    $contentType = $appInstaller->createContentType($contentTypeIdentifier, $contentTypeItem);

                    if (!$contentType) {
                        continue;
                        //throw new Exception('Failed to create content type');
                    }

                    $contentTypeEntities[$contentType->getId()] = $typeEntities;
                }


                $taxonomies = $dataAppItem->getTaxonomies();
                $taxonomyNames = array();
                $installedTaxonomyTerms = array();
                $taxonomyTermNames = array();
                foreach ($taxonomies as $taxonomyItem) {
                    /**
                     * @var $taxonomyItem Gene_BlueFootMigration_Model_Dataset_App_Taxonomy
                     */

                    $taxonomyItem->setAppId($appId);

                    $taxonomyModel = $appInstaller->createTaxonomy($taxonomyItem);
                    if (!$taxonomyModel) {
                        continue;
                    }

                    $installedTerms = is_array($taxonomyModel->getInstalledTerms()) ? $taxonomyModel->getInstalledTerms() : array();
                    $installedTaxonomyTerms[$taxonomyModel->getId()] = $installedTerms;
                    $taxonomyNames[$taxonomyModel->getId()] = $taxonomyModel->getTitle();

                    foreach ($installedTerms as $newTerm) {
                        $taxonomyTermNames[$taxonomyModel->getId()][$newTerm->getId()] = $newTerm->getTitle();
                    }

                }

                foreach ($contentTypeEntities as $contentTypeId => $typeEntities) {
                    foreach ($typeEntities as $contentEntity) {
                        /**
                         * @var $contentEntity Gene_BlueFootMigration_Model_Dataset_App_Contenttype_Entity
                         */

                        $taxonomyTerms = $contentEntity->getTaxonomyTerms();
                        $entityTermIds = array();
                        foreach ($taxonomyTerms as $_taxonomyName => $entityTerms) {
                            $taxonomyId = array_search($_taxonomyName, $taxonomyNames);
                            if ($taxonomyId) {
                                $existingTaxonomyTerms = isset($installedTaxonomyTerms[$taxonomyId]) ? $installedTaxonomyTerms : false;
                                if (is_array($existingTaxonomyTerms) && count($existingTaxonomyTerms)) {
                                    $termSearch = isset($taxonomyTermNames[$taxonomyId]) ? $taxonomyTermNames[$taxonomyId] : array();
                                    foreach ($entityTerms as $entityTerm) {
                                        if ($termId = array_search($entityTerm, $termSearch)) {
                                            $entityTermIds[] = $termId;
                                        }
                                    }
                                }

                            }

                        }

                        $contentEntity->setTaxonomyTermIds($entityTermIds);

                        $entityModel = $entityInstaller->createEntity($contentTypeId, $contentEntity);

                    }
                }

                if ($appInstaller->hasErrors()) {
                    $this->_errors = $appInstaller->getErrors();
                    throw new Gene_BlueFoot_Model_Import_Processor_Exception_General('Errors occurred during App installation.');
                }

                $resource->commit();

                // Add the app into the class
                $this->_apps[$app->getId()] = $app;

            } catch (Exception $e) {
                $count--;
                $resource->rollBack();
                throw $e;
            }

            return $count;
        }
    }
}