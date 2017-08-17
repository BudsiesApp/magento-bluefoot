<?php

require_once(Mage::getModuleDir('controllers','Gene_BlueFoot') . DS . 'Adminhtml' . DS . 'StageController.php');

/**
 * Class Gene_BlueFoot_Adminhtml_Stage_Widget_SearchController
 *
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Stage_Widget_SearchController extends Gene_BlueFoot_Adminhtml_StageController
{

    protected $_labelField = 'name';
    /**
     * Perform a search within the specified context
     * @return string JSON formatted results
     */
    public function searchAction()
    {
        // Get the collection based on the search context
        switch(Mage::app()->getRequest()->getParam('context')) {
            case 'product':
            default:
                $collection = $this->getProductCollection();
                break;

            case 'category':
                $collection = $this->getCategoryCollection();
                break;

            // Static blocks
            case 'staticblock':
                $collection = $this->getStaticBlockCollection();
                break;

            // App Entity blocks
            case 'app_entity':
                $this->_labelField = 'title';
                $collection = $this->getAppEntityCollection();
                break;
        }

        // Limit the collection
        $collection->getSelect()->limit(99);

        // Convert the search results into JSON
        if( !isset($collection) || $collection->getSize() == 0 ) {
            return $this->returnJson(array('results' => 0));
        }

        $results = array();
        $tree = false;
        // If its a category we add the tree to the label
        if (Mage::app()->getRequest()->getParam('context') == 'category') {
            $tree = $this->_getCategoryTree($collection);
        }

        foreach($collection as $match) {

            if ($match->getData($this->_labelField) && $match->getData("entity_id")) {
                // We deal with categories differently
                if ($tree && $this->_getCategoryTreeLabel($tree, $match)) {
                    $label = $this->_getCategoryTreeLabel($tree, $match);
                    $results[] = array("label" => $label, "id" => $match->getData("entity_id"));
                } else {
                    $results[] = array("label" => $match->getData($this->_labelField), "id" => $match->getData("entity_id"));
                }

            }
        }

        return $this->returnJson($results);
    }

    /**
     * Retrieve the taxonomy terms collection and turn it into a json object
     * @return string JSON formatted results
     */
    public function selectTaxonomyAction()
    {
        $ids = $this->getTaxonomy();

        $results = array();

        if ($ids) {

            foreach ($ids as $id) {

                $model = Mage::getModel('gene_bluefoot/taxonomy')->load($id);
                $taxonomyTerms = $model->getTerms();

                // Convert the search results into JSON
                if (!isset($taxonomyTerms) || $taxonomyTerms->getSize() == 0) {
                    return $this->returnJson(array('results' => 0));
                }

                foreach ($taxonomyTerms as $term) {
                    $results[] = array(
                        'parent' => $model->getContentApp()->getTitle() . ' - ' . $model->getTitle(),
                        'taxonomyid' => $model->getId(),
                        'label' => $term->getTitle(),
                        'id' => $term->getId()
                    );
                }
            }
            return $this->returnJson($results);
        }

        return false;
    }

    /**
     * Retrieve the taxonomy collection and turn it into a json object
     * @return string JSON formatted results
     */
    public function selectContentAppAction()
    {
        $collection = $this->getTaxonomyCollection();

        $taxonomyArray = array();

        foreach($collection as $match) {
            $taxonomyArray[] = array(
                'content_app' => $match->getContentApp()->getTitle(),
                'taxonomy_title' => $match->getTitle(),
                'taxonomy_id' => $match->getId()
            );
        };

        return $this->returnJson($taxonomyArray);
    }

    /**
     * User's search term
     * @return string The search term
     */
    protected function getTerm()
    {
        return Mage::app()->getRequest()->getParam('term');
    }

    /**
     * Chosen taxonomy
     * @return mixed
     */
    protected function getTaxonomy()
    {
        $param = Mage::app()->getRequest()->getParam('taxonomy_ids');

        if (isset($param[0]) && $param[0] == 0) {
            return false;
        }

        return $param;
    }

    /**
     * Chosen content app
     * @return mixed
     */
    protected function getAppId()
    {
        return Mage::app()->getRequest()->getParam('app_id');
    }

    /**
     * Build a product collection based on the search term
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getProductCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('entity_id', 'sku', 'name'))
            ->addAttributeToFilter(
                array(
                    array('attribute'=> 'sku','like' => '%' . $this->getTerm() . '%'),
                    array('attribute'=> 'name','like' => '%' . $this->getTerm() . '%'),
                )
            )
            ->addAttributeToFilter(
                'status',
                array('eq' => 1)
            )
            ->addAttributeToFilter(
                'visibility',
                array(
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                )
            );

        return $collection;
    }

    /**
     * Build a category collection based on the search term
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function getCategoryCollection()
    {
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect(array('entity_id', 'name'))
            ->addAttributeToFilter(
                array(
                    array('attribute'=> 'name','like' => '%' . $this->getTerm() . '%'),
                )
            );

        return $collection;
    }

    /**
     * Build a static block collection based on the search term
     * @return Mage_Catalog_Model_Product
     */
    protected function getStaticBlockCollection()
    {
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addFieldToSelect('block_id', 'entity_id')
            ->addFieldToSelect('title', 'name')
            ->addFieldToFilter(
                array('title', 'identifier'),
                array(
                    array('like' => '%' . $this->getTerm() . '%'),
                    array('like' => '%' . $this->getTerm() . '%'),
                )
            )
            ->addFieldToFilter('is_active', array('eq' => 1));

        return $collection;
    }


    /**
     * Build a collection of taxonomy terms based on taxonomy
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function getTaxonomyTermCollection()
    {
        $collection = Mage::getModel('gene_bluefoot/taxonomy_term')->getCollection()
           ->addFieldToFilter('taxonomy_id', $this->getTaxonomy());

        return $collection;
    }

    /**
     * Build a collection of entities based on title
     * @return mixed
     */
    protected function getAppEntityCollection()
    {
        $collection = Mage::getModel('gene_bluefoot/entity')->getCollection()
            ->addAttributeToSelect(array('entity_id', 'title'))
            ->addAttributeToFilter(
                array(
                    array('attribute'=> 'title','like' => '%' . $this->getTerm() . '%'),
                )
            );

        return $collection;
    }

    /**
     * Build a collection of taxonomies based on content app
     * @return mixed
     */
    protected function getTaxonomyCollection()
    {
        $collection = Mage::getModel('gene_bluefoot/taxonomy')->getCollection()
            ->addFieldToFilter('app_id', $this->getAppId());

        return $collection;
    }

    /**
     * As this controller is used in multiple locations access is always required
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Function to build all a collection of all the categories needed for the tree
     *
     * @param bool|false $collection
     *
     * @return array
     */
    protected function _getCategoryTree($collection = false)
    {
        $pathArray = array();
        if ($collection) {

            $collectionArray = array();
            // Create an array of all the unique path items in the collection
            foreach ($collection as $collectionItem) {
                $treeItems = explode('/', $collectionItem->getPath());
                foreach($treeItems as $item) {
                    $collectionArray[] = $item;
                }
            }
            // Create a collection from all the path items
            if (!empty($collectionArray)) {
                $pathCollection = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToSelect('name', 'id')
                    ->addIdFilter($collectionArray);


                if ($pathCollection->getSize()) {
                    // For each path item in the category match it to the category in the collection and add it to the label
                    foreach($pathCollection as $path) {
                        $pathArray[$path->getId()] = $path->getName();
                    }
                }
                unset($pathCollection);
            }
        }
        return $pathArray;
    }


    /**
     * Function to build up the label for  a category
     *
     * @param array $pathArray
     * @param bool|false $category
     * @return bool|string
     */
    protected function _getCategoryTreeLabel($pathArray = array(), $category = false)
    {
        if (!empty($pathArray) && $category) {

            // We start at 2 to ignore the base category
            $categoryPaths = explode('/', $category->getPath());
            array_shift($categoryPaths);

            // Only run the function if the category is nested.
            if (!empty($categoryPaths) && count($categoryPaths) > 1) {

                $label = '';
                // add the name of each category to the label
                $i = 0; foreach($categoryPaths as $path) {
                    $i++;
                    if ($i > 1) {
                        $label .= ' > ';
                    }
                    $label .= $pathArray[$path];
                }
                return $label;
            }
        }
        return false;
    }
}