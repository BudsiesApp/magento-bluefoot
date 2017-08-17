<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Url_Rewrite_Collection
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Url_Rewrite_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gene_bluefoot/url_rewrite');
    }


    /**
     * Filter collections by stores
     *
     * @param mixed $store
     * @param bool $withAdmin
     * @return Gene_BlueFoot_Model_Resource_Url_Rewrite_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!is_array($store)) {
            $store = array(Mage::app()->getStore($store)->getId());
        }
        if ($withAdmin) {
            $store[] = 0;
        }

        $this->addFieldToFilter('store_id', array('in' => $store));

        return $this;
    }

    /**
     *  Add filter by cms entity Id
     *
     * @param int $entityId
     * @return Gene_BlueFoot_Model_Resource_Url_Rewrite_Collection
     */
    public function filterAllByEntityId($entityId)
    {
        $this->getSelect()
            ->where('id_path = ?', "entity/{$entityId}")
            ->orWhere('id_path LIKE ?', "entity/{$entityId}/%");

        return $this;
    }


    /**
     *  Add filter by cms App Id
     *
     * @param int $appId
     * @return Gene_BlueFoot_Model_Resource_Url_Rewrite_Collection
     */
    public function filterAllByAppId($appId)
    {
        $this->getSelect()
            ->where('id_path = ?', "app/{$appId}")
            ->orWhere('id_path LIKE ?', "app/{$appId}/%");

        return $this;
    }

    /**
     * Add filter by all taxonomy terms
     *
     * @return Gene_BlueFoot_Model_Resource_Url_Rewrite_Collection
     */
    public function filterAllByTaxonomyTerm()
    {
        $this->getSelect()
            ->where('id_path LIKE ?', "term/%");
        return $this;
    }
}
