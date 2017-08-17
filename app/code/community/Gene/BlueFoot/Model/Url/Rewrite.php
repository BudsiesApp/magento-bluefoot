<?php

/**
 * Class Gene_BlueFoot_Model_Url_Rewrite
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Url_Rewrite extends Mage_Core_Model_Abstract implements Mage_Core_Model_Url_Rewrite_Interface
{
    const TYPE_ENTITY = 1;
    const TYPE_APP  = 2;
    const TYPE_TERM  = 3;


    protected function _construct()
    {
        $this->_init('gene_bluefoot/url_rewrite');
    }

    /**
     * Load rewrite information for request
     * If $path is array - we must load possible records and choose one matching earlier record in array
     *
     * @param   mixed $path
     * @return  Gene_BlueFoot_Model_Url_Rewrite
     */
    public function loadByRequestPath($path)
    {
        $this->setId(null);
        $this->_getResource()->loadByRequestPath($this, $path);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    public function loadByIdPath($path)
    {
        $this->setId(null)->load($path, 'id_path');
        return $this;
    }

    public function hasOption($key)
    {
        $optArr = explode(',', $this->getOptions());

        return array_search($key, $optArr) !== false;
    }

    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
        return $this;
    }
}