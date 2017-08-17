<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_entityTypeCode   = 'gene_bluefoot_entity';

    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(false);

        $this->setId('gene_cms_setup_installer_grid');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        $installables = Gene_BlueFoot_Model_Install::getConfigInstallers();

        $this->setCollection($installables);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $helper = Mage::helper('gene_bluefoot');

        $this->addColumn('Name', array(
            'header' => $helper->__('Name'),
            'width' => '200px',
            'type' => 'text',
            'sortable' => false,
            'filter' => false,
            'index' => 'title',
            'column_css_class' => 'bold'
        ));

        $this->addColumn('Description', array(
            'header' => $helper->__('Description'),
            'sortable' => false,
            'filter' => false,
            'index' => 'description'
        ));

        $this->addColumn('Installer File', array(
            'header' => $helper->__('Installer File'),
            'sortable' => false,
            'filter' => false,
            'index' => 'file'
        ));

        $this->addColumn('action',
            array (
                'header' => $helper->__('Action'),
                'width' => '100px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                    array (
                        'caption' => $helper->__('Install'),
                        'url' => array (
                            'base' => 'adminhtml/genecms_setup_installer/mockImport' ),
                        'field' => 'installer_code' ) ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true ));



        return $this;
    }

}