<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Tab_Taxonomy
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Edit_Tab_Taxonomy extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @var Gene_BlueFoot_Model_Taxonomy
     */
    protected $_taxonomy;

    protected $_taxonomyId;

    protected $_entityTermIds;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gene/bluefoot/entity/term/terms.phtml');
    }

    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
        }
        return $return;
    }

    public function setTaxonomy(Gene_BlueFoot_Model_Taxonomy $taxonomy)
    {
        $this->_taxonomy = $taxonomy;
        $this->_taxonomyId = $taxonomy->getId();
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy
     */
    public function getTaxonomy()
    {
        return $this->_taxonomy;
    }

    /**
     * @return array
     */
    public function getEntityTermIds()
    {
        if(is_null($this->_entityTermIds)){
            $currentEntity = $this->getCurrentEntity();
            $this->_entityTermIds = $currentEntity->getTaxonomyTermIds();
        }

        return $this->_entityTermIds;
    }

    /**
     * @return array
     */
    public function getEntityTaxonomyTermIds()
    {
        $entityTermIds = $this->getEntityTermIds();
        $taxonomyTermIds = $this->getTaxonomy()->getTermIds();

        $entityTaxonomyTermIds = array_intersect($entityTermIds, $taxonomyTermIds);

        return $entityTaxonomyTermIds;
    }

    /**
     * @return Gene_BlueFoot_Model_Entity
     */
    public function getCurrentEntity()
    {
        return Mage::registry('entity');
    }

    /**
     * Build data needed for frontend JS tree
     *
     * @param Gene_BlueFoot_Model_Taxonomy_Term $term
     * @param int $level
     * @return array
     */
    protected function _getTermData(Gene_BlueFoot_Model_Taxonomy_Term $term)
    {
        $entityTerms = $this->getEntityTaxonomyTermIds();
        $checked = false;
        if(is_array($entityTerms) && in_array($term->getId(), $entityTerms)){
            $checked = true;
        }

        $item = array(
            'text' => $term->getTitle(),
            'id' => $term->getId(),
            'store' => 0,
            'path' => $term->getPath(),
            'cls' => 'folder active-category',
            'allowDrop' => true,
            'allowDrag' => false,
            'expanded' => true,
            'checked' => $checked
        );

//        if ($this->isReadonly()) {
//            $item['disabled'] = true;
//        }

        return $item;
    }

    /**
     * Build a nested tree structure of data needed for JS tree
     *
     * @param $terms
     * @param int $parentId
     * @return array
     */
    protected function _buildNestedTree($terms, $parentId = 0)
    {
        $branch = array();

        foreach ($terms as $term) {
            if ($term->getParentId() == $parentId) {
                $children = $this->_buildNestedTree($terms, $term->getId());
                $node = $this->_getTermData($term);
                if ($children) {
                    $node['children'] = $children;
                }
                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Flat data for JS tree
     * (For non nested taxonomy)
     *
     * @param $terms
     * @return array
     */
    protected function _buildFlatTree($terms)
    {
        $branch = array();

        foreach ($terms as $term) {
            $node = $this->_getTermData($term);
            $branch[] = $node;

        }

        return $branch;
    }

    /**
     * @return string
     */
    public function getTreeJson()
    {
        $taxonomy = $this->getTaxonomy();
        $terms = $taxonomy->getTerms();

        if($taxonomy->getIsNestable()){
            $tree = $this->_buildNestedTree($terms);
        }else{
            $tree = $this->_buildFlatTree($terms);
        }

        $json = Mage::helper('core')->jsonEncode($tree);
        return $json;
    }
}