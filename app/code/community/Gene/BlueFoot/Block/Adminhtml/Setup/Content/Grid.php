<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_entityTypeCode   = 'gene_bluefoot_entity';

    public function __construct()
    {
        parent::__construct();

        $this->setId('gene_cms_content_type_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort("type_id");
        $this->setDefaultDir("ASC");
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("gene_bluefoot/type")->getCollection();
        $collection->addContentTypeFilter('content');
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


        $this->addColumn("name", array(
            "header" => $helper->__("Name"),
            "index" => "name",
        ));

        $this->addColumn("singular_name", array(
            "header" => $helper->__("Singular Name"),
            "index" => "singular_name",
        ));

        $this->addColumn("plural_name", array(
            "header" => $helper->__("Plural Name"),
            "index" => "plural_name",
        ));

        $this->addColumn("identifier", array(
            "header" => $helper->__("Identifier"),
            "index" => "identifier",
        ));

        $this->addColumn("url_prefix", array(
            "header" => $helper->__("Url Prefix"),
            "index" => "url_prefix",
        ));

        $this->addColumn("description", array(
            "header" => $helper->__("Description"),
            "index" => "description",
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
}