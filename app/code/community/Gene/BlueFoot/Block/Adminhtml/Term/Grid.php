<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("taxonomyTermGrid");
        $this->setDefaultSort("path");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy
     */
    protected function _getCurrentTaxonomy()
    {
        return Mage::registry('current_taxonomy');
    }

    protected function _prepareCollection()
    {
        $currentTaxonomy = $this->_getCurrentTaxonomy();

        $collection = Mage::getModel("gene_bluefoot/taxonomy_term")->getCollection();
        $collection->addAttributeToSelect(array('title', 'url_key', 'status'), 'left');
        $collection->addAttributeToFilter('taxonomy_id', array('eq' => $currentTaxonomy->getId()));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("entity_id", array(
            "header" => Mage::helper("gene_bluefoot")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "entity_id",
        ));

        $this->addColumn("title", array(
            "header" => Mage::helper("gene_bluefoot")->__("Title"),
            "index" => "title",
            'renderer' => 'Gene_BlueFoot_Block_Adminhtml_Term_Grid_Renderer_Title'
        ));



        $this->addColumn("path", array(
            "header" => Mage::helper("gene_bluefoot")->__("Path"),
            "index" => "path",
        ));


        $this->addColumn("url_key", array(
            "header" => Mage::helper("gene_bluefoot")->__("Url Key"),
            "index" => "url_key",
        ));


        $this->addColumn("is_active", array(
            "header" => Mage::helper("gene_bluefoot")->__("Status"),
            'width' => '100px',
            "index" => "is_active",
        ));


        $this->addColumn('action',
            array (
                'header' => Mage::helper('gene_bluefoot')->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                    array (
                        'caption' => Mage::helper('gene_bluefoot')->__('Edit'),
                        'url' => array (
                            'base' => 'adminhtml/genecms_taxonomyterm/edit' ),
                        'field' => 'id' ) ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true ));

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
}