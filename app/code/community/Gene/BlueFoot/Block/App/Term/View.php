<?php

/**
 * Class Gene_BlueFoot_Block_App_Term_View
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_App_Term_View extends Gene_BlueFoot_Block_App_Abstract
{

    protected $_entityCollection;
    protected $_pager;
    /**
     * Builds meta data and breadcrumbs
     * @return $this
     */
    protected function _prepareLayout()
    {

        $app = $this->getCurrentApp();
        $currentTerm = $this->getCurrentTerm();

        if(Mage::registry('bluefoot_page_type') == 'term_view') {
            //Build the breadcrumbs
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            /**
             * @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs
             */
            $breadcrumbs->addCrumb('home', array('label' => Mage::helper('cms')->__('Home'), 'title' => Mage::helper('cms')->__('Go to Home Page'), 'link' => Mage::getBaseUrl()));
            if ($app) {
                $breadcrumbs->addCrumb('bluefoot_app', array('label' => $app->getTitle(), 'title' => $app->getTitle(), 'link' => $app->getAppUrl()));
            }

            if ($parentTerm = $currentTerm->getParentTerm()) {
                $this->_buildTermBreadcrumb($parentTerm, $breadcrumbs, 1);
            }

            $breadcrumbs->addCrumb('bluefoot_app_entity_term', array('label' => $currentTerm->getTitle(), 'title' => $currentTerm->getTitle()));

            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {

                $title = $currentTerm->getMetaTitle() ? $currentTerm->getMetaTitle() : $currentTerm->getPathTitle(' - ', true);
                if ($title) {
                    $headBlock->setTitle($title);
                }
                $keyword = $currentTerm->getMetaKeyword();
                if ($keyword) {
                    $headBlock->setKeywords($keyword);
                } else {
                    $headBlock->setKeywords($currentTerm->getTitle());
                }
                $description = $currentTerm->getMetaDescription();
                if ($description) {
                    $headBlock->setDescription(($description));
                }
            }

            if(!$this->_pager){

                $pagerOptions = array(5=>5,10=>10,25=>25,'all'=>'all');
                //try and get the pager options if set on an app
                $perPage = (int)$app->getViewOptions()->getPaginationPerPage();

                //If we have set a per page on the app then generate 4 pager options based on this instead of using the default (eg: if is 4 will be 4=>4 8->8 12->12 all->all)
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

            //Used to ensure other blocks do not rerun this
            Mage::register('bluefoot_layout_view_initialised', 1, true);

        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Recursive function used for building nested term breadcrumbs
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @param Mage_Page_Block_Html_Breadcrumbs $breadcrumbs
     * @param int $level
     * @return $this
     */
    protected function _buildTermBreadcrumb(Gene_BlueFoot_Model_Taxonomy_Term $term, Mage_Page_Block_Html_Breadcrumbs $breadcrumbs, $level = 0)
    {
        if($parentTerm = $term->getParentTerm()){
            $this->_buildTermBreadcrumb($parentTerm, $breadcrumbs, $level+1);
        }
        $breadcrumbs->addCrumb('bluefoot_app_entity_term_' . $level, array('label' => $term->getTitle(), 'title' => $term->getTitle(), 'link' => $term->getTermUrl()));

        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy_Term
     */
    public function getCurrentTerm()
    {
        return Mage::registry('current_genecms_term');
    }

    /**
     * @param null $term
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Term_Collection
     * @throws Mage_Core_Exception
     */
    public function getChildTerms($term = null)
    {
        $termId = $this->getCurrentTerm()->getId();
        if(is_object($term)){
            $termId = (int)$term->getId();
        }elseif(is_numeric($term)){
            $termId = (int)$term;
        }

        if(!is_int($termId)){
            $termId = 0;
        }

        $terms = Mage::getModel('gene_bluefoot/taxonomy_term')->getCollection();
        $terms->setStoreId(Mage::app()->getStore()->getId());
        $terms->addAttributeToSelect('*');
        $terms->addFieldToFilter('status', 1);
        $terms->addFieldToFilter('parent_id', $termId);
        $terms->setOrder('position', 'ASC');

        return $terms;
    }


    /**
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @param null $type
     * @param null $count
     * @return bool|Gene_BlueFoot_Model_Resource_Entity_Collection
     */
    public function getTermEntityCollection(Gene_BlueFoot_Model_Taxonomy_Term $term, $type = null, $count = null)
    {
        return $this->_getEntityCollection($type, $count, $term);
    }

    /**
     * @param null $type
     * @return Gene_BlueFoot_Model_Resource_Entity_Collection|bool
     * @throws Mage_Core_Exception
     */
    public function getEntityCollection($type = null, $count = null, $term = null)
    {
        if(is_null($type) && is_null($count) && $this->_entityCollection){
            return $this->_entityCollection;
        }

        $contentCollection = $this->_getEntityCollection($type, $count, $term);

        if(is_null($type) && is_null($count)){
            $this->_entityCollection = $contentCollection;
        }

        return $contentCollection;
    }

    /**
     * @param null $type
     * @param null $count
     * @param null $term
     * @return Gene_BlueFoot_Model_Resource_Entity_Collection
     * @throws Mage_Core_Exception
     */
    protected function _getEntityCollection($type = null, $count = null, $term = null)
    {
        $app = $this->getCurrentApp();
        $contentTypeIds = $app->getContentTypeIds();

        $typeFilter = false;

        if($type instanceof Varien_Object && $type->getId()){
            $typeFilter = $type->getId();
        }elseif(is_int($type)){
            $typeFilter = $type;
        }

        if(!($term instanceof Gene_BlueFoot_Model_Taxonomy_Term)){
            $term = $this->getCurrentTerm();
        }

        $contentCollection = $term->getContentCollection();
        $contentCollection->setStoreId(Mage::app()->getStore()->getId());
        $contentCollection->addAttributeToSelect('*');

        if($typeFilter){
            //$contentCollection->addFieldToFilter('type_id', $typeFilter);
            $types = Mage::getModel('gene_bluefoot/type')->getCollection()->addFieldToFilter('type_id', array('eq' => $typeFilter));
            $attributeSetIds = $types->getColumnValues('attribute_set_id');
            $contentCollection->addFieldToFilter('attribute_set_id', array('in' => $attributeSetIds));
        }

        $contentCollection->addIsActiveFilter();

        //add published_date DESC as default order
        $contentCollection->setOrder('published_date', 'DESC');

        if(is_numeric($count)){
            $contentCollection->setPageSize($count);
            $contentCollection->setCurPage(1);
        }

        return $contentCollection;
    }

    /**
     * @return string
     */
    public function getLatestTitle()
    {
        return $this->__('Latest from ' . $this->getCurrentTerm()->getTitle());
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy
     */
    public function getTaxonomy()
    {
        $term = $this->getCurrentTerm();
        $taxonomy = $term->getTaxonomy();

        return $taxonomy;
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Collection|bool
     */
    public function getTaxonomies()
    {
        $app = $this->getCurrentApp();
        $term = $this->getCurrentTerm();
        if($term && $term->getId()){
            $taxonomyIds = array($term->getTaxonomy()->getId());
        }else{
            $taxonomyIds = $app->getTaxonomyIds();
        }

        if(is_array($taxonomyIds) && count($taxonomyIds)){
            $taxonomies = Mage::getModel('gene_bluefoot/taxonomy')->getCollection();
            $taxonomies->addFieldToFilter('taxonomy_id', array('in' => $taxonomyIds));

            return $taxonomies;
        }

        return false;
    }

    /**
     * Function to get the title on a taxonomy page
     * @return string
     */
    public function getPageTitle()
    {
        // Check if tasxonmies should be prefixed with the taxonomy title
        if (Mage::getStoreConfig('bluefoot_app/taxonomies/prefix_titles')) {
            return $this->getCurrentTerm()->getTaxonomy()->getTitle() . ' - ' . $this->getCurrentTerm()->getTitle();
        } else {
            return $this->getCurrentTerm()->getTitle();
        }

    }

    /**
     * @return mixed
     */
    public function getPageDescription()
    {
        return $this->getCurrentTerm()->getDescription();
    }

    /**
     * @return bool
     */
    public function getListView()
    {
        $type = $this->getCurrentTerm()->getColumnType();

        if($type == 'list') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getGridView()
    {
        $type = $this->getCurrentTerm()->getColumnType();

        if($type == 'grid') {
            return true;
        }

        return false;
    }


}