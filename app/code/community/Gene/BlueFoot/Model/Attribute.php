<?php

/**
 * Class Gene_BlueFoot_Model_Attribute
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute extends Mage_Eav_Model_Attribute
{
    const MODULE_NAME = 'Gene_ExpertCms';
    protected $_eventPrefix = 'expertcms';
    protected $_eventObject = 'attribute';

    public function _construct()
    {
        $this->_init('gene_bluefoot/attribute');
    }

    protected function _beforeSave()
    {
        if(!$this->getAdditional()){
            $this->setAdditional(array());
        }

        // Reset the data model before processing
        $this->setData('data_model', '');

        $attrWidget = $this->getData('widget');
        // Detect if a widget is being used and assign the correct frontend_input
        if (!empty($attrWidget)) {
            $widget = Mage::getModel('gene_bluefoot/stage_widget_source')->getWidgetConfigFromAlias($attrWidget);
            if (isset($widget) && !empty($widget)) {
                if (isset($widget->input_type)) {
                    $this->setData('frontend_input',(string) $widget->input_type);
                }
                if (isset($widget->data_model)) {
                    $this->setData('data_model', $widget->data_model);
                }
            } else {
                throw new Mage_Exception('Unable to load widget ' . $attrWidget);
            }
        }

        return parent::_beforeSave();
    }

    public function getAdditional()
    {
        if(!$this->getData('additional')){

            if($serializedData = @unserialize($this->getAdditionalData())){
                $this->setData('additional', $serializedData);
            }
        }

        return $this->getData('additional');
    }

    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve source model
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * @param string $input
     * @return string
     */
    public function getBackendTypeByInput($input)
    {
        switch($input)
        {
            case 'entity_list':
            case 'child_entity':
                $field = 'text';
                break;
            default:
                $field = parent::getBackendTypeByInput($input);
        }

        return $field;
    }
}