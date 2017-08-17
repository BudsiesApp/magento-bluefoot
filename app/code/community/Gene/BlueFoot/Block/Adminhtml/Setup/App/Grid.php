<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('gene_cms_app_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort("app_id");
        $this->setDefaultDir("ASC");
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("gene_bluefoot/app")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $helper = Mage::helper("gene_bluefoot");

        $this->addColumn("app_id", array(
            "header" => $helper->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "app_id",
        ));

        $this->addColumn("title", array(
            "header" =>$helper->__("Title"),
            "index" => "title",
        ));

        $this->addColumn("internal_description", array(
            "header" => $helper->__("Description"),
            "index" => "internal_description",
        ));

        $this->addColumn("url_prefix", array(
            "header" => $helper->__("URL Prefix"),
            "index" => "url_prefix",
        ));

        //@todo add renderers for columns
//        $this->addColumn("content_types", array(
//            "header" => $helper->__("Content Types"),
//            "index" => "content_types",
//        ));
//
//        $this->addColumn("taxonomies", array(
//            "header" => $helper->__("Taxonomies"),
//            "index" => "taxonomies",
//        ));


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
                            'base' => 'adminhtml/genecms_setup_app/edit' ),
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