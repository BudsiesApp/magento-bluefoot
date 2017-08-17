<?php

/**
 * Class Gene_BlueFoot_Block_App_View
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_App_View extends Gene_BlueFoot_Block_App_Abstract
{
    protected $_entityCollection;

    protected $_pager;

    /**
     *
     * Builds meta data and breadcrumbs
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $app = $this->getCurrentApp();

        if(Mage::registry('bluefoot_page_type') == 'app_view') {

            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            /**
             * @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs
             */
            $breadcrumbs->addCrumb('home', array('label' => Mage::helper('cms')->__('Home'), 'title' => Mage::helper('cms')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));
            $breadcrumbs->addCrumb('bluefoot_app', array('label' => $app->getTitle(), 'title' => $app->getTitle()));

            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {

                $title = $app->getMetaTitle() ? $app->getMetaTitle() : $app->getTitle();
                if ($title) {
                    $headBlock->setTitle($title);
                }
                $keyword = $app->getMetaKeywords();
                if ($keyword) {
                    $headBlock->setKeywords($keyword);
                }
                $description = $app->getMetaDescription();
                if ($description) {
                    $headBlock->setDescription(($description));
                }
            }


            if(!$this->_pager){

                $pagerOptions = array(5=>5,10=>10,25=>25,'all'=>'all');
                //try and get the pager options if set on an app
                $perPage = (int)$app->getViewOptions()->getPaginationPerPage();
                if($perPage > 0){
                    $pagerOptions = array();
                    for($i =1; $i<4; $i++){
                        $perPage = $perPage*$i;
                        $pagerOptions[$perPage] = $perPage;
                    }
                }

                //initialise pager
                $this->_pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
                $this->_pager->setAvailableLimit($pagerOptions);
                $collection = $this->getEntityCollection();
                $this->_pager->setCollection($this->getEntityCollection());
                $this->setChild('pager', $this->_pager);
                $collection->load();
            }



            //Used to ensure other blocks do not re build
            Mage::register('bluefoot_layout_view_initialised', 1, true);
        }

        return parent::_prepareLayout();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param null $type
     * @return Gene_BlueFoot_Model_Resource_Entity_Collection|bool
     * @throws Mage_Core_Exception
     */
    public function getEntityCollection($type = null, $count = null)
    {
        if(is_null($type) && is_null($count) && $this->_entityCollection){
            return $this->_entityCollection;
        }

        $app = $this->getCurrentApp();
        $contentTypeIds = $app->getContentTypeIds();

        $typeFilter = false;

        if ($type instanceof Varien_Object && $type->getId()) {
            $typeFilter = $type->getId();
        } elseif (is_int($type)) {
            $typeFilter = $type;
        }

        if (is_array($contentTypeIds) && count($contentTypeIds)) {
            $collection = Mage::getModel('gene_bluefoot/entity')->getCollection();
            $collection->setOrder('published_date', 'DESC');
            $collection->setStoreId(Mage::app()->getStore()->getId());
            $collection->addContentTypeFilter('content');

            $types = Mage::getModel('gene_bluefoot/type')->getCollection()->addFieldToFilter('type_id', array('in' => $contentTypeIds));
            if ($typeFilter) {
                $types->addFieldToFilter('type_id', $typeFilter);
            }
            $attributeSetIds = $types->getColumnValues('attribute_set_id');
            $collection->addFieldToFilter('attribute_set_id', array('in' => $attributeSetIds));

            $collection->addAttributeToSelect('*');

            $collection->addIsActiveFilter();

            if (is_numeric($count)) {
                $collection->setPageSize($count);
                $collection->setCurPage(1);
            }

            if(is_null($type) && is_null($count)){
                $this->_entityCollection = $collection;
            }


            return $collection;

        }

        return false;
    }

    /**
     * @return string
     */
    public function getLatestTitle()
    {
        return $this->__('Latest from the ' . $this->getCurrentApp()->getTitle());
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Collection|bool
     */
    public function getTaxonomies()
    {
        $app = $this->getCurrentApp();
        $depth = Mage::getStoreConfig('bluefoot_app/sidebar/category_depth');

        $taxonomyIds = $app->getTaxonomyIds();
        if(is_array($taxonomyIds) && count($taxonomyIds)){
            $taxonomies = Mage::getModel('gene_bluefoot/taxonomy')->getCollection();
            $taxonomies->addFieldToFilter('taxonomy_id', array('in' => $taxonomyIds));

            return $taxonomies;
        }

        return false;
    }

    /**
     * @param Gene_BlueFoot_Model_Taxonomy $taxonomy
     * @param int $count
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection|mixed
     */
    public function getTaxonomyTerms(Gene_BlueFoot_Model_Taxonomy $taxonomy, $count = null)
    {
        $taxonomy->setStoreId(Mage::app()->getStore()->getId());
        $terms = $taxonomy->getTerms();
        $terms->addAttributeToFilter('status', 1);

        if(is_numeric($count)){
            $terms->setPageSize($count);
            $terms->setCurPage(1);
        }

        return $terms;
    }

    /**
     * @param Gene_BlueFoot_Model_Taxonomy $taxonomy
     * @param null $count
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection
     */
    public function getParentTaxonomyTerms(Gene_BlueFoot_Model_Taxonomy $taxonomy, $count = null)
    {
        $terms = $this->getTaxonomyTerms($taxonomy, $count);
        $terms->addFieldToFilter('parent_id', 0);

        return $terms;
    }

    /**
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @param null $count
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection
     * @throws Mage_Core_Exception
     */
    public function getChildTerms(Gene_BlueFoot_Model_Taxonomy_Term $term, $count = null)
    {
        $terms = $term->getChildTerms();
        $terms->setStoreId(Mage::app()->getStore()->getId());
        $terms->addAttributeToSelect('*');
        $terms->addAttributeToFilter('status', 1);
        $terms->setOrder('position', 'ASC');

        if(is_numeric($count)){
            $terms->setPageSize($count);
            $terms->setCurPage(1);
        }

        return $terms;
    }

    /**
     * Get content types associated with APP
     * @return Gene_BlueFoot_Model_Resource_Type_Collection
     */
    public function getContentTypes()
    {
        $app = $this->getCurrentApp();
        $contentTypeIds = $app->getContentTypeIds();

        if(is_array($contentTypeIds) && count($contentTypeIds)){
            $contentTypes = parent::getContentTypes();
            $contentTypes->addFieldToFilter('type_id', array('in' => $contentTypeIds));
            return $contentTypes;
        }

        return false;
    }

    /**
     * is a list view
     * @return bool
     */
    public function getListView()
    {
        $type = $this->getCurrentApp()->getViewOptions()->getColumnType();

        if($type == 'list') {
            return true;
        }

        return false;
    }

    /**
     * Is a grid view
     * @return bool
     */
    public function getGridView()
    {
        $type = $this->getCurrentApp()->getViewOptions()->getColumnType();

        if($type == 'grid') {
            return true;
        }

        return false;
    }
}