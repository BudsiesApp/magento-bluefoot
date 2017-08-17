<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_entityTypeCode   = 'gene_bluefoot_entity';

    public function __construct()
    {
        parent::__construct();

        $this->setId('gene_cms_block_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort("type_id");
        $this->setDefaultDir("ASC");
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("gene_bluefoot/type")->getCollection();
        $collection->addContentTypeFilter('block');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $helper = Mage::helper('gene_bluefoot');

        $this->addColumn("type_id", array(
            "header" => $helper->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "type_id",
        ));

        $this->addColumn("icon", array(
            "header" => $helper->__("Icon"),
            "align" => "center",
            "width" => "50px",
            "index" => "icon_class",
            'renderer' => 'Gene_BlueFoot_Block_Adminhtml_Setup_Block_Grid_Renderer_Icon'
        ));

        $this->addColumn("name", array(
            "header" => $helper->__("Name"),
            "index" => "name",
        ));

        $this->addColumn("identifier", array(
            "header" => $helper->__("Identifier"),
            "index" => "identifier",
        ));

        $this->addColumn("description", array(
            "header" => $helper->__("Description"),
            "index" => "description",
        ));

        $this->addColumn("sort_order", array(
            "header" => $helper->__("Sort"),
            "align" => "center",
            "width" => "50px",
            "index" => "sort_order"
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
}