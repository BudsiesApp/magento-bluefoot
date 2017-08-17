<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_entityTypeCode   = 'gene_bluefoot_entity';

    public function __construct()
    {
        parent::__construct();

        $this->setId('gene_cms_attribute_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
            ->addVisibleFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _toHtml()
    {
        $html = '<ul class="messages"><li class="notice-msg">'.Mage::helper('gene_bluefoot')->__('Below shows a list of all the fields which can be used when defining a content type.').'</li></ul>';
        $html .= parent::_toHtml();

        return $html;
    }


    protected function _prepareColumns()
    {
        $helper = Mage::helper('gene_bluefoot');

        $this->addColumn('attribute_id', array (
            'header' => $helper->__('Attribute ID'),
            'width' => '80px',
            'type' => 'text',
            'filter_index' => 'main_table.attribute_id',
            'index' => 'attribute_id' ));


        $this->addColumn('frontend_input', array (
            'header' => $helper->__('Input Type'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'frontend_input' ));

        $this->addColumn('widget', array (
            'header' => $helper->__('Widget'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'widget' ));

        $this->addColumn('attribute_code', array(
            'header'=> $helper->__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=> $helper->__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label'
        ));

        $this->addColumn('is_required', array(
            'header'=>Mage::helper('eav')->__('Required'),
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => array(
                '1' => $helper->__('Yes'),
                '0' => $helper->__('No'),
            ),
            'align' => 'center',
        ));

        $this->addColumn('is_user_defined', array(
            'header'=> $helper->__('System'),
            'sortable'=>true,
            'index'=>'is_user_defined',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                '0' => $helper->__('Yes'),   // intended reverted use
                '1' => $helper->__('No'),    // intended reverted use
            ),
        ));


        $this->addColumn('is_global', array(
            'header' => $helper->__('Scope'),
            'sortable' => true,
            'index' => 'is_global',
            'type' => 'options',
            'options' => array(
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE => Mage::helper('gene_bluefoot')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => Mage::helper('gene_bluefoot')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => Mage::helper('gene_bluefoot')->__('Global'),
            ),
            'align' => 'center',
        ));

        $this->addColumn('action',
            array (
                'header' => $helper->__('Action'),
                'width' => '60px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                    array (
                        'caption' => $helper->__('Edit'),
                        'url' => array (
                            'base' => 'adminhtml/genecms_setup_attribute/edit' ),
                        'field' => 'attribute_id' ) ),
                'filter' => false,
                'sortable' => false,
                'index' => 'mcontent',
                'is_system' => true ));



        return $this;
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/genecms_setup_attribute/edit', array (
            'attribute_id' => $row->getAttributeId() ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/genecms_setup_attribute/index', array (
            '_current' => true ));
    }
}