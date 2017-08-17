<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Entity
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Entity extends Gene_BlueFoot_Model_Resource_Abstract
{
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes   = array();

    protected $_contentTermTable;

    protected function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType(Gene_BlueFoot_Model_Entity::ENTITY);
        $this->setConnection(
            $resource->getConnection('gene_bluefoot_read'),
            $resource->getConnection('gene_bluefoot_write')
        );

        $this->_contentTermTable = $this->getTable('gene_bluefoot/taxonomy_term_content');
    }


    public function afterLoad()
    {
        $this->getEntityType();
        return $this;
    }


    public function _afterSave(Varien_Object $object)
    {
        $this->_saveTaxonomyTerm($object);
        return parent::_afterSave($object);
    }

    /**
     * Do checks before saving the entity
     *
     * @param \Varien_Object $object
     *
     * @return \Mage_Eav_Model_Entity_Abstract
     */
    public function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);

        // Does the entity have a featured image?
        if ($featuredImage = $object->getData('featured_image')) {

            // Quick check to see if the string contains http
            if (is_string($featuredImage) && strpos($featuredImage, 'http') !== false) {
                $imagePath = $this->retrieveFeaturedImage($featuredImage);
                if ($imagePath) {
                    $object->setData('featured_image', $imagePath);
                }
            }

        }

        return $this;
    }

    /**
     * Retrieve a featured image
     *
     * @param $imageUrl
     *
     * @return null
     * @throws \Exception
     */
    public function retrieveFeaturedImage($imageUrl)
    {
        $url = parse_url($imageUrl);
        if (isset($url['host']) && isset($url['path'])) {
            $image = $this->_downloadImage($imageUrl);
            if ($image) {
                $ioFile = new Varien_Io_File();
                $ioFile->setAllowCreateFolders(true);
                if ($ioFile->checkAndCreateFolder(Mage::helper('gene_bluefoot/config')->getUploadDir())) {
                    $pathInfo = pathinfo($url['path']);
                    if (isset($pathInfo['dirname'])) {
                        $ioFile->checkAndCreateFolder(Mage::helper('gene_bluefoot/config')->getUploadDir() . $pathInfo['dirname']);
                    }
                    $ioFile->open(array('path' => Mage::helper('gene_bluefoot/config')->getUploadDir() . $pathInfo['dirname']));

                    // Attempt to write the image
                    if ($ioFile->filePutContent($pathInfo['basename'], $image)) {
                        return $url['path'];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Download an image from a remote server
     *
     * @param $imageUrl
     *
     * @return null|string
     */
    protected function _downloadImage($imageUrl)
    {
        try{
            // Retrieve the image
            $http = new Varien_Http_Client($imageUrl);
            $response = $http->request(Zend_Http_Client::GET);
            if ($response->getStatus() == 200) {
                return $response->getBody();
            }
        }catch(exception $e){
            return null;
        }

        return null;
    }

    /**
     * Retrieve taxonomy term ids
     *
     * @param Gene_BlueFoot_Model_Entity $entity
     * @return array
     */
    public function getTaxonomyTermIds($entity)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->_contentTermTable, 'term_id')
            ->where('content_id = ?', (int)$entity->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * Save taxonomy term relations
     *
     * @param Varien_Object $object
     * @return Gene_BlueFoot_Model_Resource_Entity
     */
    protected function _saveTaxonomyTerm(Varien_Object $object)
    {
        if (!$object->hasTaxonomyTermIds()) {
            return $this;
        }
        $termIds = $object->getTaxonomyTermIds();
        $oldTermIds = $this->getTaxonomyTermIds($object);

        $object->setIsChangedTerms(false);

        $insert = array_diff($termIds, $oldTermIds);
        $delete = array_diff($oldTermIds, $termIds);




        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $termId) {
                if (empty($termId)) {
                    continue;
                }
                $data[] = array(
                    'term_id' => (int)$termId,
                    'content_id'  => (int)$object->getId(),
                    'position'    => 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->_contentTermTable, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $termId) {
                $where = array(
                    'content_id = ?'  => (int)$object->getId(),
                    'term_id = ?' => (int)$termId,
                );

                $write->delete($this->_contentTermTable, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $object->setAffectedTermIds(array_merge($insert, $delete));
            $object->setIsChangedTerms(true);
        }

        return $this;
    }

}
