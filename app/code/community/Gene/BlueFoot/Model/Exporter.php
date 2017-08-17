<?php

/**
 * Class Gene_BlueFoot_Model_Exporter
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Exporter extends Varien_Object
{

    protected $_exports = array();

    protected $_errors = array();

    protected $_typeGroups = array();

    /**
     * @param $type
     * @param $ids
     * @param null $config
     */
    public function addExport($type, $ids, $config = null)
    {
        $this->_exports[$type] = new Varien_Object(array(
            'export_ids' => $ids,
            'type' => $type,
            'config' => $config
        ));
    }

    /**
     * @param string $type
     * @return Varien_Object
     * @throws Exception
     */
    public function getExport($type)
    {
        if(!isset($this->_exports[$type])){
            return false;
        }
        return $this->_exports[$type];
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _initialise()
    {
        foreach($this->_exports as $exportType => $export) {

            switch ($exportType) {
                case 'content_blocks':
                    $this->_initialiseBlockExport($export);
                    break;
                case 'content_types':
                    break;
                case 'apps':
                    $this->_initialiseAppExport($export);
                    break;
                case 'attributes':
                    break;
                default:
                    throw new Exception('Unrecognised Export type : ' . $exportType);
            }
        }

        return $this;
    }

    protected function _initialiseAppExport(Varien_Object $export)
    {
        $attributeCodes = array();
        $exportConfig = $export->getConfig();

        if($exportConfig->getIncludeContentTypes() && $exportConfig->getIncludeContentAttributes()){
            $appIds = $export->getExportIds();
            $contentTypes = Mage::getModel('gene_bluefoot/type')->getCollection()->addContentTypeFilter('content')
                ->addFieldToFilter('app_id', array('in' => $appIds));

            foreach($contentTypes as $contentType){
                $typeAttributes = $contentType->getVisibleAttributes();
                if($typeAttributes && $typeAttributes->getSize()){
                    foreach($typeAttributes as $attr){
                        $attributeCodes[] = $attr->getAttributeCode();
                    }
                }
            }

            $attributeCodes = array_unique($attributeCodes);

            if(count($attributeCodes)){
                $attributeExport = $this->getExport('attributes');
                if(!$attributeExport){
                    $attributeExport = new Varien_Object(array(
                        'type' => 'attributes',
                        'export_ids' => $attributeCodes,
                        'config' => new Varien_Object()
                    ));
                }else{
                    $existingIds = $attributeExport->getExportIds();

                    $attributeExport->setExportIds(array_merge($existingIds, $attributeCodes));
                }

                $this->addExport('attributes', $attributeExport->getExportIds(), $attributeExport->getConfig());
            }
        }

    }


    /**
     * @param Varien_Object $export
     * @return $this
     * @throws Exception
     */
    protected function _initialiseBlockExport(Varien_Object $export)
    {
        $exportConfig = $export->getConfig();
        $attributes = array();
        if($exportConfig->getIncludeBlockAttributes()){
            if($blockIds = $export->getExportIds()){
                $blockCollection = Mage::getModel('gene_bluefoot/type')->getCollection()->addContentTypeFilter('block')
                    ->addFieldToFilter('identifier', array('in' => $blockIds));

                foreach($blockCollection as $block){
                    $blockAttributes = $block->getAllAttributes();
                    if($blockAttributes && is_array($blockAttributes)){
                        $attributes = array_merge(array_keys($blockAttributes), $attributes);
                    }
                }
            }
        }

        if(count($attributes)){
            $attributeExport = $this->getExport('attributes');
            if(!$attributeExport){
                $attributeExport = new Varien_Object(array(
                    'type' => 'attributes',
                    'export_ids' => $attributes,
                    'config' => new Varien_Object()
                ));
            }else{
                $existingIds = $attributeExport->getExportIds();

                $attributeExport->setExportIds(array_merge($existingIds, $attributes));
            }

            $this->addExport('attributes', $attributeExport->getExportIds(), $attributeExport->getConfig());

        }

        return $this;

    }

    public function exportAsJson()
    {
        $data = $this->_getExportData();
        return json_encode($data);
    }

    public function exportAsArray()
    {
        $data = $this->_getExportData();
        return (is_array($data) ? $data : array());
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getExportData()
    {
        $this->_initialise();

        $data = array(
            '_time' => date('U')
        );

        foreach($this->_exports as $exportType => $export){
            switch($exportType){
                case 'attributes':
                    $data['attributes'] = $this->exportFieldData($export);
                    break;
                case 'content_blocks':
                    $data['content_blocks'] = $this->exportEntityTypeData($export);
                    break;
                case 'apps':
                    $data['content_apps'] = $this->exportAppData($export);
                    break;

                case 'content_types':

                    break;
                default:
                    throw new Exception('Unrecognised Export type : ' . $exportType);
            }
        }


        return $data;
    }

    public function exportAppData(Varien_Object $export)
    {
        $entityType = $this->_getEntityType();
        $data = array();
        $exportIds = $export->getExportIds();
        $config = $export->getConfig();

        $contentApps = Mage::getModel('gene_bluefoot/app')->getCollection();
        $contentApps->addFieldToFilter('app_id', array('in' => $exportIds));

        foreach($contentApps as $app){
            $appData = $app->getData();
            $appData = $this->_refineAppData($appData);

            $appData['content_types'] = array();

            if($config->getIncludeContentTypes()) {
                $contentTypes = $app->getContentTypes();
                foreach ($contentTypes as $contentType) {
                    /**
                     * @var $contentType Gene_BlueFoot_Model_Type
                     */

                    $contentTypeData = $this->_buildContentTypeData($contentType);
                    $appData['content_types'][] = $contentTypeData;
                }
            }

            $appData['taxonomies'] = array();

            if($config->getIncludeTaxonomies()){
                $taxonomies = $app->getTaxonomies();
                foreach($taxonomies as $taxonomy){
                    $taxonomyData = $this->_buildTaxonomyData($taxonomy);
                    $appData['taxonomies'][] = $taxonomyData;
                }
            }


            $data[] = $appData;
        }

        return $data;
    }

    public function _buildTaxonomyData($taxonomy)
    {
        $taxonomyData = $taxonomy->getData();
        $taxonomyData = $this->_refineTaxonomyData($taxonomyData);

        return $taxonomyData;
    }


    /**
     * @param Varien_Object $export
     * @return array
     */
    public function exportEntityTypeData(Varien_Object $export)
    {
        $data = array();
        $exportIds = $export->getExportIds();

        $contentTypes = Mage::getModel('gene_bluefoot/type')->getCollection();
        $contentTypes->addContentTypeFilter('block')
            ->addFieldToFilter('identifier', array('in' => $exportIds));

        foreach($contentTypes as $typeModel){

            $typeData = $this->_buildContentTypeData($typeModel);
            $data[] = $typeData;

        }

        return $data;
    }

    protected function _getTypeGroup($groupId)
    {
        if(isset($this->_typeGroups[$groupId])){
            return $this->_typeGroups[$groupId];
        }

        $typeGroup = Mage::getModel('gene_bluefoot/type_group');
        $typeGroup->load($groupId);
        if($typeGroup->getId()){
            $this->_typeGroups[$groupId] = $typeGroup;
            return $typeGroup;
        }

        return false;
    }

    protected function _buildContentTypeData(Gene_Bluefoot_Model_Type $contentType)
    {
        $typeData = $contentType->getData();

        $typeGroupId = $contentType->getGroupId();
        if($typeGroupId){
            if($typeGroup = $this->_getTypeGroup($typeGroupId)){
                $typeData['group'] = $typeGroup->getCode();
            }
        }

        $typeData = $this->_refineTypeData($typeData);

        $attributeData = array('attributes' => array(), 'groups' => array());

        $attributeSet = $contentType->getAttributeSet();

        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($attributeSet->getId())
            ->setSortOrder()
            ->load();

        foreach($groups as $group){
            $groupAttributesCollection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter()
                ->setAttributeGroupFilter($group->getId())
                ->setOrder('sort_order', 'ASC')
                ->load();

            $groupData = array(
                'attribute_group_name' => $group->getAttributeGroupName(),
                'sort_order' => $group->getSortOrder(),
                'default_id' => $group->getDefaultId(),
                'attributes' => array()
            );
            $groupAttributes = array();

            foreach($groupAttributesCollection as $attribute){
                $groupAttributes[] = array(
                    'attribute_code' => $attribute->getAttributeCode(),
                    'sort_order' => $attribute->getSortOrder()
                );
                $attributeData['attributes'][] = $attribute->getAttributeCode();
            }

            $groupData['attributes'] = $groupAttributes;
            $attributeData['groups'][] = $groupData;
        }

        $typeData['attribute_data'] = $attributeData;

        return $typeData;
    }

    protected function _buildAttributeData(Gene_BlueFoot_Model_Attribute $attribute)
    {
        $entityType = $this->_getEntityType();
        $attId = $attribute->getId();

        if($attribute->getEntityType() != $entityType){
            throw new Exception('Attribute ' . $attId . ' does not exist within the Gene BlueFoot scope');
        }

        $data = $attribute->getData();

        $data = $this->_refineAttData($data);

        if($attribute->usesSource() && $attribute->getSourceModel() == 'eav/entity_attribute_source_table'){

            $options = array('value'=> array(), 'order' => array(), 'delete' => array());

            $source = $attribute->getSource();
            $rawOptions = $source->getAllOptions();

            $optionCounter = 0;
            foreach($rawOptions as $rawOpt){
                $optionKey = 'option_' . $optionCounter;
                if(isset($rawOpt['value']) && $rawOpt['value'] != ''){
                    if($attribute->getDefaultValue() == $rawOpt['value']){
                        $data['default'][] = $optionKey;
                    }
                    $optValue = $rawOpt['label'];
                    $options['value'][$optionKey][0] = $optValue;
                    $options['order'][$optionKey] = '';
                    $options['delete'][$optionKey] = '';
                    $optionCounter++;
                }

            }

            $data['option'] = $options;
        }

        $labels = $attribute->getStoreLabels();
        $labels[0] = $attribute->getFrontend()->getLabel();

        $data['frontend_label'] = $labels;

        return $data;
    }

    /**
     * @param Varien_Object $export
     * @return array
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function exportFieldData(Varien_Object $export)
    {
        $fieldData = array();

        $exportIds = $export->getExportIds();
        $exportIds = array_unique($exportIds);

        if(!empty($exportIds)) {
            $attributes = Mage::getModel('gene_bluefoot/attribute')->getCollection();
            $attributes->addVisibleFilter()
                ->addFieldToFilter('attribute_code', $exportIds);


            foreach ($attributes as $attributeObject) {
                $data = $this->_buildAttributeData($attributeObject);
                $fieldData[] = $data;
            }
        }

        return $fieldData;
    }


    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        return Mage::helper('gene_bluefoot')->getEntityType();
    }

    /**
     * @param array $data
     * @return array
     */
    protected function _refineAttData(array $data)
    {
        unset($data['default_value']);
        unset($data['attribute_id']);
        unset($data['entity_type_id']);



        $data['entity_allowed_block_type'] = false;
        if(isset($data['additional_data']) && !is_array($data['additional_data'])){
            $unSerialisedData = @unserialize($data['additional_data']);
            if($unSerialisedData && is_array($unSerialisedData)){
                $relatedBlockId = isset($unSerialisedData['entity_allowed_block_type']) ? $unSerialisedData['entity_allowed_block_type'] : false;
                if($relatedBlockId){
                    $block = Mage::getModel('gene_bluefoot/type')->load($relatedBlockId);
                    if($block->getId()){
                        $data['entity_allowed_block_type'] = $block->getIdentifier();
                    }
                }

                unset($unSerialisedData['entity_allowed_block_type']);
                $data['additional_data'] = $unSerialisedData;
            }else{
                $data['additional_data'] = array();
            }

        }

        return $data;
    }


    /**
     * @param array $data
     * @return array
     */
    protected function _refineAppData(array $data)
    {
        unset($data['app_id']);
        unset($data['description']);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function _refineTypeData(array $data)
    {
        unset($data['type_id']);
        unset($data['group_id']);
        unset($data['app_id']);
        unset($data['attribute_set_id']);
        unset($data['attribute_set']);
        unset($data['attribute_set']);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function _refineTaxonomyData(array $data)
    {
        unset($data['taxonomy_id']);
        unset($data['app_id']);
        unset($data['taxonomy_terms']);
        unset($data['taxonomy_term_ids']);
        unset($data['term_ids']);

        return $data;
    }
}