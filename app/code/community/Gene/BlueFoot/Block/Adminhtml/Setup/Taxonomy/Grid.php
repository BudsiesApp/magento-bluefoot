<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Taxonomy_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('gene_cms_taxonomy_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort("taxonomy_id");
        $this->setDefaultDir("ASC");
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("gene_bluefoot/taxonomy")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $helper = Mage::helper("gene_bluefoot");

        $this->addColumn("taxonomy_id", array(
            "header" => $helper->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "taxonomy_id",
        ));

        $this->addColumn("title", array(
            "header" =>$helper->__("Title"),
            "index" => "title",
        ));

        $this->addColumn("term_url_prefix", array(
            "header" => $helper->__("Term Url Prefix"),
            "index" => "term_url_prefix",
        ));

        $this->addColumn("type", array(
            "header" => $helper->__("Type"),
            'width' => '200px',
            "index" => "type",
        ));

        $this->addColumn("is_active", array(
            "header" => $helper->__("Status"),
            'width' => '100px',
            "index" => "is_active",
        ));

        $this->addColumn('terms',
            array (
                'header' => $helper->__('Terms'),
                'width' => '100px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                    array (
                        'caption' => $helper->__('View Terms'),
                        'url' => array (
                            'base' => 'adminhtml/genecms_taxonomyterm/index' ),
                        'field' => 'taxonomy' ) ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true ));

        $this->addColumn('add_terms',
            array (
                'header' => $helper->__('Terms'),
                'width' => '100px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                    array (
                        'caption' => $helper->__('Add Term'),
                        'url' => array (
                            'base' => 'adminhtml/genecms_taxonomyterm/add' ),
                        'field' => 'taxonomy' ) ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true ));

        $this->addColumn('action',
            array (
                'header' => $helper->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                    array (
                        'caption' => $helper->__('Edit'),
                        'url' => array (
                            'base' => 'adminhtml/genecms_setup_taxonomy/edit' ),
                        'field' => 'id' ) ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
}