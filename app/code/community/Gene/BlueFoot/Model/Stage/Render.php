<?php

/**
 * Handles all rendering of the front-end of the system
 *
 * Class Gene_BlueFoot_Model_Stage_Render
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Render extends Mage_Core_Model_Abstract
{
    /**
     * @var Gene_BlueFoot_Model_Resource_Entity_Collection
     */
    private $_loadedEntities;

    /**
     * @var Gene_BlueFoot_Model_Resource_Type_Collection
     */
    private $_loadedTypes;

    /**
     * @var array
     */
    private $_contentTypes = array();

    const DEFAULT_STRUCTURAL_RENDERER = 'gene_bluefoot/entity_block_structural_abstract';

    /**
     * Process and render HTML for the front-end
     *
     * @param $html
     *
     * @return mixed
     */
    public function render($html)
    {
        $pageBuilderSections = $this->_matchPageBuilder($html);

        // Verify we have sections to build
        if(!empty($pageBuilderSections)) {

            // Load an entire collection of content types
            $this->_loadedTypes = Mage::getResourceModel('gene_bluefoot/type_collection')
                ->addFieldToSelect('*');

            // Return the HTML built
            $renderedHtml = $this->renderSections($pageBuilderSections, $html);

            $renderedHtml = $this->_afterHtmlRender($renderedHtml);

            return $renderedHtml;
        }

        return $html;
    }

    /**
     * Render place holders in the page of page builder content
     *
     * @param            $html
     * @param bool|false $placeholderHtml
     *
     * @return mixed
     */
    public function renderPlaceholders($html, $placeholderHtml = false)
    {
        $pageBuilderSections = $this->_matchPageBuilder($html);

        // Verify we have sections to build
        if (!empty($pageBuilderSections)) {
            if ($placeholderHtml === false) {
                $placeholderHtml = '<div class="gene-bluefoot-content-placeholder">' . Mage::helper('gene_bluefoot')->__('Page Builder Content') . '</div>';
            }

            foreach ($pageBuilderSections as $section) {
                $html = str_replace($section['html'], $placeholderHtml, $html);
            }
        }

        return $html;
    }

    /**
     * Match page builder HTML content
     *
     * @param $html
     *
     * @return array
     */
    protected function _matchPageBuilder($html)
    {
        preg_match_all('/<!--' . Gene_BlueFoot_Model_Stage_Save::GENE_CMS_STRING . '="(.*?)"-->/', $html, $sections);

        // Convert the matches to an array which makes sense
        $pageBuilderSections = array();
        foreach($sections[0] as $key => $sectionHtml) {
            $pageBuilderSections[$key]['html'] = $sectionHtml;
        }
        foreach($sections[1] as $key => $json) {

            // Attempt to decode the json
            try {
                $pageBuilderSections[$key]['json'] = Mage::helper('core')->jsonDecode($json);
                $pageBuilderSections[$key]['cacheTag'] = md5($json);
            } catch(Zend_Json_Exception $e) {
                unset($pageBuilderSections[$key]);
            }
        }

        return $pageBuilderSections;
    }

    /**
     * Do any post processing to rendered html
     *
     * @param $html
     * @return mixed
     */
    protected function _afterHtmlRender($html)
    {
        self::restoreFormKey($html, Mage::getSingleton('core/session')->getFormKey());
        return $html;
    }

    /**
     * Replace form key placeholder with actual form keys
     *
     * @param $content
     * @param $formKey
     * @return bool
     */
    public static function restoreFormKey(&$content, $formKey)
    {
        if (!$content) {
            return false;
        }
        $replacementCount = 0;
        $content = str_replace(Gene_BlueFoot_Helper_Stage::getFormKeyMarker(), $formKey, $content, $replacementCount);
        return ($replacementCount > 0);
    }

    /**
     * Insert the required assets into the head
     *
     * @param                            $assets
     * @param \Mage_Page_Block_Html_Head $block
     */
    public function addAssets($assets, Mage_Page_Block_Html_Head $block)
    {
        //Check the assets and add them in by type
        if ($assets !== false) {
            foreach ($assets as $asset) {
                switch ($asset['type']) {
                    case 'css':
                        $block->addItem('skin_css', $asset['name']);
                        break;
                    case 'js':
                        $block->addJs($asset['name']);
                        break;
                    case 'skin_js':
                        $block->addItem('skin_js', $asset['name']);
                        break;
                    case 'js_css':
                        $block->addItem('js_css', $asset['name']);
                        break;
                }
            }
        }
    }

    /**
     * Return the cache key for the assets
     *
     * @param $contentTypes
     *
     * @return string
     */
    public function getAssetCacheKey($contentTypes)
    {
        $cacheKeyItems = array(
            Gene_BlueFoot_Model_Stage_Save::GENE_CMS_STRING,
            md5(implode($contentTypes, '-')),
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            Mage::app()->getStore()->isCurrentlySecure(),
            md5(implode(Mage::app()->getLayout()->getUpdate()->getHandles(), '-'))
        );

        return implode('_', $cacheKeyItems);
    }

    /**
     * Render each section
     *
     * @param $sections
     * @param $html
     *
     * @return mixed
     */
    public function renderSections($sections, $html)
    {
        // Loop through each section and start building
        foreach($sections as $section) {

            // Build the section html
            $sectionHtml = $this->buildSectionHtml($section['json']);

            // Check the section HTML was built
            if (!$sectionHtml) {
                if (Mage::getIsDeveloperMode()) {
                    $sectionHtml = Mage::helper('gene_bluefoot')->__('<div class="error"><h2>Page Builder Error</h2><p>We were unable to build the page builder element on this area of the page.</p></div>');
                } else {
                    $sectionHtml = '<!--GENE_CMS_ERROR-->';
                }
            }

            // Swap out the JSON for generated HTML
            $html = str_replace($section['html'], $sectionHtml, $html);
        }

        return $html;
    }

    /**
     * Build the section HTML
     *
     * @param array $json
     *
     * @return string
     */
    public function buildSectionHtml(array $json)
    {
        // Load all of the entities
        $this->_loadedEntities = $this->buildEntities($json);

        // Start our string
        $sectionHtml = '';

        // Verify we have some entities
        if(!empty($this->_loadedEntities)) {
            $this->buildElementHtmlFromArray($json, $sectionHtml);
        }

        // Replace all form keys before this gets cached/returned
        $sectionHtml = str_replace(Mage::getSingleton('core/session')->getFormKey(), Gene_BlueFoot_Helper_Stage::getFormKeyMarker(), $sectionHtml);

        return $sectionHtml;
    }

    /**
     * Return an entity, preferably from the built in initial load
     *
     * @param $entityId
     *
     * @return \Gene_BlueFoot_Model_Entity|\Varien_Object
     */
    public function getEntity($entityId)
    {
        if($loaded = $this->_loadedEntities->getItemByColumnValue('entity_id', $entityId)) {
            return $loaded;
        }

        return Mage::getModel('gene_bluefoot/entity')->load($entityId);
    }

    /**
     * Build up an array of entities from entity ID's
     *
     * @param $config
     *
     * @return array
     * @throws \Mage_Core_Exception
     */
    public function buildEntities($config)
    {
        // If the configuration is a string convert it
        if(is_string($config)) {
            $config = Mage::helper('core')->jsonDecode($config);
        }

        return $this->retrieveEntities($this->getEntityIds($config));
    }

    /**
     * Get entity ID's
     *
     * @param $config
     *
     * @return array
     */
    public function getEntityIds($config)
    {
        $entityIds = array();
        $this->getEntityFields($config, 'entityId', $entityIds);
        return array_unique($entityIds);
    }

    /**
     * Get the various content types
     *
     * @param $config
     *
     * @return array
     */
    public function getEntityContentTypes($config)
    {
        $types = array();
        $this->getEntityFields($config, 'contentType', $types);
        return array_unique($types);
    }

    /**
     * Get a field from all entities
     *
     * @param $config
     * @param $field
     * @param $result
     *
     * @return array
     */
    public function getEntityFields($config, $field, &$result)
    {
        foreach($config as $element) {
            if(isset($element['entityId'])) {
                $result[] = $element[$field];

                // Retrieve the entities ID's for any children items
                if(isset($element['children']) && is_array($element['children'])) {
                    foreach($element['children'] as $name => $children) {
                        $this->getEntityFields($children, $field, $result);
                    }
                }
            } else {
                // Retrieve the entities ID's for any children items
                if(isset($element['children']) && is_array($element['children'])) {
                    $this->getEntityFields($element['children'], $field, $result);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve entities by ID
     *
     * @param $entityIds
     *
     * @return \Gene_BlueFoot_Model_Resource_Entity_Collection
     * @throws \Mage_Core_Exception
     */
    public function retrieveEntities($entityIds)
    {
        // They should be unique, but just in case
        $entityIds = array_unique($entityIds);

        // Retrieve all the entities
        $entities = Mage::getResourceModel('gene_bluefoot/entity_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', $entityIds);

        return $entities;
    }

    /**
     * Build up the element HTML
     *
     * @param array $json
     * @param       $html
     *
     * @throws \Mage_Core_Exception
     */
    public function buildElementHtmlFromArray(array $json, &$html)
    {
        // Loop through each element
        foreach($json as $index => $element) {
            $html .= $this->buildElementHtml($element);
        }
    }

    /**
     * Build each elements HTML
     *
     * @param $element
     *
     * @return string
     * @throws \Mage_Core_Exception
     */
    public function buildElementHtml($element)
    {
        // Detect the type
        if(isset($element['type'])) {
            return $this->buildStructuralHtml($element);
        } else if(isset($element['contentType'])) {
            return $this->buildEntityHtml($element);
        }

        return '';
    }

    /**
     * Build a structural element
     *
     * @param $element
     *
     * @return string
     */
    public function buildStructuralHtml($element)
    {
        $elementConfig = Mage::helper('gene_bluefoot/config')->getConfig('structural/' . $element['type']);
        if($elementConfig) {

            $elementTemplate = isset($elementConfig->template) ? (string) $elementConfig->template: '';

            // If the structural type doesn't have a template we cannot render it
            if(!isset($elementConfig->template) || isset($elementConfig->template) && empty($elementTemplate)) {
                return '<!-- STRUCTURAL ELEMENT HAS NO TEMPLATE: '.$element['type'].' -->';
            }

            // Determine the renderer we're going to use
            $renderer = self::DEFAULT_STRUCTURAL_RENDERER;
            if(isset($elementConfig->renderer) && !empty($elementConfig->renderer)) {
                $renderer = $elementConfig->renderer;
            }

            $block = Mage::app()->getLayout()->createBlock((string) $renderer);
            if($block) {
                $block->setTemplate((string) $elementConfig->template);
                if(isset($element['formData']) && !empty($element['formData'])) {
                    $block->setData('form_data', $element['formData']);
                }
            } else {
                return '<!-- STRUCTURAL ELEMENT CANNOT LOAD BLOCK: '.$element['type'].' -->';
            }

            // Build the child HTML
            if(isset($element['children'])) {
                $childHtml = '';
                $this->buildElementHtmlFromArray($element['children'], $childHtml);
                $block->setData('rendered_child_html', $childHtml);
            }

            return $block->toHtml();
        }

        return '';
    }

    /**
     * Basic entity rendering
     *
     * @param $element
     *
     * @return string
     * @throws \Exception
     */
    public function buildEntityHtml($element)
    {
        if(isset($element['entityId'])) {

            $this->_contentTypes[] = $element['contentType'];

            // Query the cache for the block entity
            $cacheTag = 'ENTITY_' . $element['entityId'];
            if (Mage::app()->useCache('gene_block_cache') && false !== ($cacheData = Mage::app()->getCacheInstance()->load($this->getCacheKey($cacheTag)))) {
                return $cacheData;
            }

            // Build the block
            if($block = $this->buildEntityBlock($element)) {
                // Run the blocks HTML through the renderer again, start a new instance due to class variables
                $blockHtml = Mage::getModel('gene_bluefoot/stage_render')->render($block->toHtml(), false);

                // Store in the cache if the cache is enabled
                if(Mage::app()->useCache('gene_block_cache')) {
                    Mage::app()->getCacheInstance()->save($blockHtml, $this->getCacheKey($cacheTag), Mage::app()->getCacheInstance()->getTagsByType('gene_block_cache'), 604800);
                }

                return $blockHtml;
            }
        }

        return '';
    }

    /**
     * Build the block for the entity
     *
     * @param $element
     *
     * @return bool|mixed
     */
    public function buildEntityBlock($element)
    {
        $entity = $this->getEntity($element['entityId']);
        if(!$entity->getId()){
            return false;
        }

        // Pass over any form data to the entity
        if (isset($element['formData']) && !empty($element['formData'])) {
            foreach ($element['formData'] as $key => $value) {
                $entity->setData($key, $value);
            }
        }

        /* @var $frontend Gene_BlueFoot_Model_Entity_Frontend_Block */
        $frontend = $entity->getFrontend();

        if($block = $frontend->getRenderBlock()) {
            $block->setTemplate($frontend->getViewTemplate());
            $block->setStructure($element);

            return $block;
        }

        return false;
    }

    /**
     * Return the cache key for the section
     *
     * @param $cacheTag
     *
     * @return string
     */
    public function getCacheKey($cacheTag)
    {
        $cacheKeyItems = array(
            Gene_BlueFoot_Model_Stage_Save::GENE_CMS_STRING,
            $cacheTag,
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            Mage::app()->getStore()->isCurrentlySecure()
        );

        return implode('_', $cacheKeyItems);
    }
}