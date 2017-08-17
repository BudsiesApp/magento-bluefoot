<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Grid
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('genecms_grid');
        $this->setUseAjax(false);

        // Does this grid have a custom order?
        if($this->getTypeFilter() && $this->getTypeFilter()->getAdminGridOrder()) {
            $this->setDefaultSort($this->getTypeFilter()->getAdminGridOrder());
            $this->setDefaultDir(($this->getTypeFilter()->getAdminGridOrderDir() == 0 ? Zend_Db_Select::SQL_DESC : Zend_Db_Select::SQL_ASC));
        } else {
            $this->setDefaultSort('entity_id');
            $this->setDefaultDir('ASC');
        }

        $this->setSaveParametersInSession(true);
    }

    /**
     * Return the type filter from the database
     *
     * @return Gene_BlueFoot_Model_Type
     */
    public function getTypeFilter()
    {
        return Mage::registry('type_filter');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gene_bluefoot/entity')->getCollection();
        $collection->addAttributeToSelect('*');

        $filterType = $this->getTypeFilter();
        if(is_object($filterType)){
            $attributeSetId = $filterType->getAttributeSetId();
            $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Preparation of the requested columns of the grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $filterType = $this->getTypeFilter();

        $this->addColumn('entity_id', array (
            'header' => 'Content Entity ID',
            'width' => '80px',
            'type' => 'text',
            'index' => 'entity_id' ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('gene_bluefoot/entity')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        if(!$filterType){
            $this->addColumn('set_name',
                array(
                    'header'=> Mage::helper('gene_bluefoot')->__('Content Type'),
                    'width' => '130px',
                    'index' => 'attribute_set_id',
                    'type'  => 'options',
                    'options' => $sets,
                ));
        }

        $this->addColumn('title', array (
            'header' => 'Title',
            'index' => 'title' ));

        $this->addColumn('identifier', array (
            'header' => 'Internal Identifier',
            'index' => 'identifier' ));

        // Include any "Display in Grid" attributes
        if($filterType) {

            // Load the attributes from the collection
            $attributes = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter()
                ->setAttributeSetFilter($filterType->getAttributeSet()->getId())
                ->addSetInfo();

            // Record whether this content type has an identifier
            $hasIdentifier = false;

            // Iterate through
            foreach($attributes as $attribute) {

                // Change the flag if we find the attribute
                if($attribute->getAttributeCode() == 'identifier') {
                    $hasIdentifier = true;
                }

                // Only include those attributes which are set to display in grid
                if($attribute->getDisplayInGrid()) {

                    // Build up our basic column data
                    $columnData = array(
                        'header' => $attribute->getFrontendLabel(),
                        'index' => $attribute->getAttributeCode(),
                        'type' => $attribute->getFrontendInput()
                    );

                    // Include the format for date types
                    if($attribute->getFrontendInput() == 'date') {
                        $columnData['format'] = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                    }
                    if($attribute->getFrontendInput() == 'datetime') {
                        $columnData['format'] = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                    }

                    // Add in the column
                    $this->addColumn($attribute->getAttributeCode(), $columnData);
                }
            }

            // If we have no identifier don't include it on the grid
            if($hasIdentifier === false) {
                $this->removeColumn('identifier');
            }

        }

        $this->addColumn('is_active',
            array (
                'header' => Mage::helper('gene_bluefoot')->__('Active'),
                'index' => 'is_active',
                'type' => 'options',
                'width' => '70px',
                'options' => array (
                    0 => Mage::helper('gene_bluefoot')->__('No'),
                    1 => Mage::helper('gene_bluefoot')->__('Yes') ) ));

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
                            'base' => 'adminhtml/genecms_entity/edit' ),
                        'field' => 'entity_id' ) ),
                'filter' => false,
                'sortable' => false,
                'index' => 'entity',
                'is_system' => true ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/genecms_entity/edit', array (
            'entity_id' => $row->getId() ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/genecms_entity/index', array (
            '_current' => true ));
    }
}