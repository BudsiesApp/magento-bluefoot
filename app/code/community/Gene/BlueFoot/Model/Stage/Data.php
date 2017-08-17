<?php

/**
 * Class Gene_BlueFoot_Model_Stage_Data
 *
 * Interface between Block content types and the staging system
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Data extends Varien_Object
{
    protected $_initAttributeFlag = false;
    protected $_attributeData = array();

    protected $_initBlockFlag = false;
    protected $_contentBlockData = array();

    protected $_attributeCodes = null;

    /**
     * @return array
     */
    public function getAllDataAsArray()
    {
        $stageData = array(
            'contentTypeGroups' => Mage::getResourceModel('gene_bluefoot/type_group_collection')->toPageBuilderArray(),
            'contentTypes' => $this->getAllBlockData(),
            'structural' => $this->getStructuralConfig(),
            'templates' => $this->getTemplateData(),
            'globalFields' => $this->getGlobalFields()
        );

        return $stageData;
    }

    /**
     * Return all global fields
     *
     * @return mixed
     */
    public function getGlobalFields()
    {
        $fields = Mage::helper('gene_bluefoot/config')->getConfig('global_fields');
        return $fields->asArray();
    }

    /**
     * Return the template data
     *
     * @return array
     */
    public function getTemplateData()
    {
        $templates = Mage::getResourceModel('gene_bluefoot/stage_template_collection');
        $templates->setOrder('pinned','DESC');
        if ($templates->getSize()) {
            $templateData = array();
            foreach ($templates as $template) {
                $templateData[] = array(
                    'id' => $template->getId(),
                    'name' => $template->getData('name'),
                    'preview' => $template->getData('preview'),
                    'structure' => $template->getData('structure'),
                    'pinned' => (bool) $template->getData('pinned')
                );
            }
            return $templateData;
        }

        return array();
    }

    /**
     * Return the structural config
     *
     * @return array|bool|string
     */
    public function getStructuralConfig()
    {
        $structuralConfig = Mage::helper('gene_bluefoot/config')->getConfig('structural');
        if($structuralConfig) {
            $structuralArray = $structuralConfig->asArray();
            foreach($structuralArray as &$structural) {
                // The page builder doesn't care about these values
                unset($structural['renderer'], $structural['template']);
                if(isset($structural['name'])) {
                    $structural['name'] = Mage::helper('gene_bluefoot')->__($structural['name']);
                }
                foreach($structural['fields'] as &$field) {
                    if(isset($field['label'])) {
                        $field['label'] = Mage::helper('gene_bluefoot')->__($field['label']);
                    }
                    if(isset($field['source_model'])) {
                        $sourceModel = $field['source_model'];
                        try {
                            $model = Mage::getModel($sourceModel);
                            if(method_exists($model, 'toOptionArray')) {
                                $field['options'] = $model->toOptionArray();
                            }
                        } catch (Exception $e) {
                            $field['error'] = Mage::helper('gene_bluefoot')->__('Unable to load source model: %s', $e->getMessage());
                        }
                        unset($field['source_model']);
                    }
                }
            }

            return $structuralArray;
        }

        return false;
    }

    /**
     * Remove the fields from the main entity table
     *
     * @param $fields
     *
     * @return mixed
     */
    public function cleanseData($fields)
    {
        // We don't want this data
        $removeField = array('entity_type_id', 'identifier', 'created_at', 'updated_at', 'is_active', 'attribute_set_id');

        // Loop through and remove the fields
        foreach($removeField as $key) {
            unset($fields[$key]);
        }

        return $fields;
    }

    /**
     * Return the entity config
     *
     * @param $entityIds
     *
     * @return array
     */
    public function getEntityConfig($entityIds)
    {
        // They should be unique, but just in case
        $entityIds = array_unique($entityIds);

        // Retrieve all the entities
        $entities = Mage::getResourceModel('gene_bluefoot/entity_collection');

        // Detect if we're on a specific store view and only load the entities from that specific store
        if ($storeId = Mage::app()->getRequest()->getParam('storeId')) {
            $entities->setStoreId($storeId);
        }

        $entities->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', $entityIds);

        $entityData = new Varien_Data_Collection();

        if($entities->getSize()) {
            // Build up the entities
            foreach ($entities as $entity) {
                $obj = new Varien_Object();
                $obj->setData( $this->cleanseData($entity->getData()) );
                $obj->setData('preview_view', $this->buildPreviewView($entity));
                $entityData->addItem( $obj );
            }
        }

        Mage::dispatchEvent('gene_bluefoot_build_config_entities', array('entities' => $entityData));

        // Convert $entityData to an array
        $entityArray = array();
        foreach($entityData as $entity) {
            $entityArray[ $entity->getEntityId() ] = $entity->getData();
        }

        return $entityArray;
    }

    /**
     * Build up the data required to build the preview view within the page builder
     *
     * @param \Gene_BlueFoot_Model_Entity $entity
     *
     * @return array
     */
    public function buildPreviewView(Gene_BlueFoot_Model_Entity $entity)
    {
        $previewView = array();
        foreach ($entity->getData() as $key => $value) {
            // Determine whether or not we can loads this entities attribute
            if ($entity->getResource()->getAttribute($key)) {
                $previewView[$key] = $entity->getResource()->getAttribute($key)->getFrontend()->getValue($entity);

                // Does this particular attribute have a data model?
                if ($dataModel = $entity->getResource()->getAttribute($key)->getDataModel($entity)) {
                    if (method_exists($dataModel, 'asJson')) {
                        $previewView[$key] = $dataModel->asJson();
                    }
                }
            } else {
                $previewView[$key] = $value;
            }
        }
        return $previewView;
    }

    /**
     * Create a temporary entity, and use it to load the data models
     *
     * @param $contentType
     * @param $data
     * @param $fields
     *
     * @return bool
     */
    public function buildDataModelUpdate($contentType, $data, $fields)
    {
        $attributeSet = Mage::getModel('gene_bluefoot/type')->load($contentType, 'identifier');
        if ($attributeSet) {
            // Format the form data
            $formData = $data;
            $formData['attribute_set_id'] = $attributeSet->getAttributeSetId();

            // Create our entity with the correct attribute set id
            $entity = Mage::getModel('gene_bluefoot/entity');
            $entity->setData($formData);

            return $this->getDataModelValues($entity, $fields);
        }

        return false;
    }

    /**
     * Return the data model values for a number of fields
     *
     * @param \Gene_BlueFoot_Model_Entity $entity
     * @param                        $fields
     *
     * @return array
     */
    public function getDataModelValues(Gene_BlueFoot_Model_Entity $entity, $fields)
    {
        $dataModelValues = array();

        foreach ($fields as $field) {
            $dataModelValues[$field] = $entity->getData($field);

            // Determine whether or not we can loads this entities attribute
            if ($entity->getResource()->getAttribute($field)) {
                // Does this particular attribute have a data model?
                if ($dataModel = $entity->getResource()->getAttribute($field)->getDataModel($entity)) {
                    if (method_exists($dataModel, 'asJson')) {
                        $dataModelValues[$field] = $dataModel->asJson();
                    }
                }
            }
        }

        return $dataModelValues;
    }

    /**
     * @return array|null
     */
    public function getAttributeCodes()
    {
        if(is_null($this->_attributeCodes)){
            $this->_attributeCodes = array();
            $this->_buildAttributeData();
        }
        return $this->_attributeCodes;
    }

    /**
     * @param bool $forceRebuild
     * @return array
     */
    public function getAllAttributeData($forceRebuild = false)
    {
        if(!$this->_initAttributeFlag || $forceRebuild){
            $this->_buildAttributeData();
        }

        return $this->_attributeData;
    }

    /**
     * @param $attributeCode
     * @return mixed
     */
    public function getAttributeData($attributeCode)
    {
        $allAttrData = $this->getAllAttributeData();
        if(array_key_exists($attributeCode, $allAttrData)){
            return $allAttrData[$attributeCode];
        }
    }

    /**
     * Build the attribute data if not built already and flatten
     */
    protected function _buildAttributeData()
    {
        $this->_attributeData = array();
        $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
            ->addVisibleFilter();

        foreach($collection as $attr){
            $this->_attributeData[$attr->getAttributeCode()] = $this->_flattenAttributeData($attr);
            $this->_attributeCodes[$attr->getId()] = $attr->getAttributeCode();
        }
        $this->_initAttributeFlag = true;
    }

    /**
     * @param bool $forceRebuild
     * @return array
     */
    public function getAllBlockData($forceRebuild = false)
    {
        if(!$this->_initBlockFlag || $forceRebuild){
            $this->_buildBlockData();
        }

        return $this->_contentBlockData;
    }

    /**
     * @return $this
     */
    protected function _buildBlockData()
    {
        $this->_contentBlockData = array();

        $contentBlocksCollection = Mage::getModel('gene_bluefoot/type')->getCollection()->setOrder('sort_order',  'asc');
        $contentBlocksCollection->addContentTypeFilter('block');

        foreach($contentBlocksCollection as $contentBlock){
            $this->_contentBlockData[$contentBlock->getIdentifier()] = $this->_flattenBlockData($contentBlock);
        }

        $this->_initBlockFlag = true;

        return $this;
    }

    /**
     * @param Gene_BlueFoot_Model_Type $block
     * @return array
     * @throws Exception
     */
    protected function _flattenBlockData(Gene_BlueFoot_Model_Type $block)
    {
        $allAttributeSetIds = Mage::getModel('gene_bluefoot/type')->getCollection()->getColumnValues('attribute_set_id');
        $attributeSetId = $block->getAttributeSet()->getId();

        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            //->addFieldToFilter('attribute_set_id', array('in' => $allAttributeSetIds))
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder();

        $attributeGroups = array();
        foreach($groups as $group){
            $collection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
                ->addVisibleFilter()
                ->setAttributeGroupFilter($group->getId())
                ->setAttributeSetFilter($attributeSetId);

            $attributeIds = $collection->getAllIds();

            foreach($attributeIds as $attrId){
                $attributeGroups[$attrId] = $group->getAttributeGroupName();
            }

        }

        $attributes = $block->getAllAttributes();

        $attributeCodes = array();
        $fields = array();
        foreach($attributes as $cBAttr){
            $cBAttrCode = $cBAttr->getAttributeCode();
            if($attrData = $this->getAttributeData($cBAttrCode)){
                $attrGroup = isset($attributeGroups[$attrData['attribute_id']]) ?  $attributeGroups[$attrData['attribute_id']] : 'General';
                $attrData['group'] = $attrGroup;
                $attributeCodes[] = $cBAttrCode;

                $eventName = 'gene_bluefoot_build_config_attach_field_' . $attrData['type'] . (isset($attrData['widget']) ? '_' . str_replace("/", "_", $attrData['widget']) : '');
                Mage::dispatchEvent($eventName, array('field' => $attrData));

                $fields[$cBAttrCode] = $attrData;
            }
        }

        $data = array(
            'code' => $block->getIdentifier(),
            'name' => $block->getName(),
            'icon' => '<i class="' . $block->getIconClass() . '"></i>',
            'color' => '#444', /* @todo remove from JS */
            'color_theme' => $this->getColorTheme('#444'), /* @todo remove from JS */
            'contentType' => '',
            'group' => ($block->getGroupId() ? $block->getGroupId() : 'general'),
            'fields' => $fields,
            'fields_list' => $attributeCodes,
            'visible' => ($block->getShowInPageBuilder() ? true : false)
        );

        // Do we have a preview template for this content block?
        if ($previewTemplate = $this->getPreviewTemplate($block)) {
            $data['preview_template'] = $previewTemplate;
        }

        // Does the content block have a preview field?
        if ($previewField = $block->getPreviewField()) {
            $data['preview_field'] = $previewField;
        }

        return $data;
    }

    /**
     * Return the renderable preview template for the admin
     *
     * @param \Gene_BlueFoot_Model_Type $contentBlock
     *
     * @return string
     */
    public function getPreviewTemplate(Gene_BlueFoot_Model_Type $contentBlock)
    {
        if ($templateIdentifier = $contentBlock->getItemViewTemplate()) {
            $templatePath = false;

            // First check to see if the content block has a 'preview_template' set. Otherwise use the front-end template
            if ($adminPath = (string) Mage::helper('gene_bluefoot/config')->getBlockConfig('templates/' . $templateIdentifier . '/preview_template')) {
                $templatePath = $adminPath;
            } else if ($frontendPath = (string) Mage::helper('gene_bluefoot/config')->getBlockConfig('templates/' . $templateIdentifier . '/file')) {
                $templatePath = $frontendPath;
            }

            // Do we have a template path to attempt to load?
            if ($templatePath) {
                try {
                    // Create an instance of the block using the current admin theme fallback system
                    /* @var $block Mage_Core_Block_Template */
                    $block = Mage::app()->getLayout()->createBlock('core/template')->setTemplate($templatePath);
                    if ($block) {
                        return $block->toHtml();
                    }
                } catch (Exception $e) {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Send a color theme based on the content types color
     *
     * @param $hex
     *
     * @return string
     */
    protected function getColorTheme($hex)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));

        $contrast = sqrt(
            $r * $r * .241 +
            $g * $g * .691 +
            $b * $b * .068
        );

        if($contrast > 190){
            return 'dark';
        } else {
            return 'light';
        }
    }

    /**
     * @param Gene_BlueFoot_Model_Attribute $attr
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _flattenAttributeData(Gene_BlueFoot_Model_Attribute $attr)
    {
        $options = array();
        if($attr->usesSource()){
            $options = $attr->getSource()->getAllOptions();
        }

        // Assign the type for later manipulation
        $type = $attr->getFrontend()->getInputType();

        $data = array(
            'attribute_id' => $attr->getId(),
            'code' => $attr->getAttributeCode(),
            'type' => $type,
            'label' => $attr->getFrontend()->getLabel(),
            'is_global' => $attr->getIsGlobal(),
            'group' => 'General' //TODO
        );

        // Only pass options if they aren't empty
        if (!empty($options)) {
            $data['options'] = $options;
        }

        if ($attr->getNote()) {
            $data['note'] = $attr->getNote();
        }

        // Pass over if the attribute is required
        if ($attr->getIsRequired()) {
            $data['required'] = true;
        }

        // Inform the front-end if this field has a data model
        if ($attr->getDataModel()) {
            $data['data_model'] = true;
        }

        $childType = false;
        if($type == 'child_entity'){
            if($sourceModel = $attr->getSource()){
                if(method_exists($sourceModel, 'getAllowedType')){
                    $childTypeModel = $sourceModel->getAllowedType();
                    if($childTypeModel){
                        $childType = $childTypeModel->getIdentifier();
                    }
                }
            }
        }

        // Handle different types
        switch($type) {
            case 'boolean':
                $data['type'] = 'select';
                $data['options'] = array(
                    array('value' => 0, 'label' => Mage::helper('gene_bluefoot')->__('No')),
                    array('value' => 1, 'label' => Mage::helper('gene_bluefoot')->__('Yes'))
                );
            break;
            case 'multiselect':
                $data['type'] = 'select';
                $data['multiple'] = true;
            break;
            case 'textarea':
                if($attr->getIsWysiwygEnabled()) {
                    $data['type'] = 'widget';
                    $data['widget'] = 'wysiwyg';
                }
            break;
            case 'image':
            case 'file':
            case 'upload':
                $data['type'] = 'widget';
                $data['widget'] = 'upload';
                break;

            case 'child_entity':
                $data['type'] = 'widget';
                $data['widget'] = 'child_block';
                $data['child_block_type'] = $childType;
                break;

        }

        // If the attribute has a widget assigned to it ensure it renders on the front-end
        if($widget = $attr->getData('widget')) {
            $data['type'] = 'widget';
            $data['widget'] = $widget;
        }

        return $data;
    }

}