<?php

/**
 * Handles the saving of a structure posted from the admin panel, the JS framework creates a new input above the
 * original text area. These are then captured by a general pre-dispatch event which in turn hands the data to this
 * class for processing
 *
 * Class Gene_BlueFoot_Model_Stage_Save
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Save extends Mage_Core_Model_Abstract
{
    const GENE_CMS_STRING = 'GENE_CMS';

    protected $_globalFields = null;


    protected $_storeId = false;

    /**
     * Save the structures of a page via post
     *
     * @param $structures
     *
     * @return $this
     * @throws \Exception
     */
    public function saveStructures($structures)
    {
        // Loop through each form
        foreach($structures as $elementName => $content) {

            // We don't want nasty errors happening here
            try {

                // Try and parse the page structure
                $pageStructure = $this->decodeStructure($content);

                // Only attempt if the json decode was a success
                if ($pageStructure) {

                    // Create the correct structure
                    $this->createStructure($pageStructure);

                    // Json encode it for storage
                    $jsonData = $this->encodeStructure($pageStructure);

                    // Change the element name
                    $this->updatePost($elementName, $jsonData);
                }

            } catch (Exception $e) {

                // If we're in developer mode help out the developers
                if(Mage::getIsDeveloperMode()) {
                    throw $e;
                }

                // Forward session message to admin
                Mage::getSingleton('adminhtml/session')->addError('Unable to store page structure for element:' . $elementName);
            }

        }

        return $this;
    }

    /**
     * Add HTML comments around the code
     *
     * @param $structure
     *
     * @return string
     */
    public static function encodeStructure($structure)
    {

        // If this is an array and it actually has some content, add the CMS string to the page
        if (is_array($structure)) {

            if (isset($structure[0]['children'])) {
                $json = Mage::helper('core')->jsonEncode($structure);
                return '<!--' . self::GENE_CMS_STRING . '="' . $json . '"-->';
            }
        } else {
            return '<!--' . self::GENE_CMS_STRING . '="' . $structure . '"-->';
        }

        return false;
    }

    /**
     * Decode the structure
     *
     * @param $structure
     *
     * @return mixed
     */
    public function decodeStructure($structure)
    {
        try {
            // Currently no extra processing is required
            return Mage::helper('core')->jsonDecode($structure);
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Create the entity from the element
     *
     * @param $element
     * @param $storeId
     *
     * @return mixed
     * @throws \Exception
     */
    public function createEntityFromElement($element, $storeId)
    {
        // We only create an entity if we have some data
        if(isset($element['formData']) && !empty($element['formData'])) {

            $attributeSet = Mage::getModel('gene_bluefoot/type')->load($element['contentType'], 'identifier');
            if ($attributeSet) {

                // Format the form data
                $formData = $element['formData'];
                $formData['attribute_set_id'] = $attributeSet->getAttributeSetId();

                // Create our entity with the correct attribute set id
                $entity = Mage::getModel('gene_bluefoot/entity');
                if (isset($element['entityId'])) {
                    $entity->load($element['entityId']);
                } else if (isset($element['formData']['entityId'])) {
                    $entity->load($element['formData']['entityId']);
                }

                // Add it into the entity
                if (!empty($formData)) {
                    $entity->setData($formData);
                }

                // Set the store ID of the entity
                if ($storeId !== false) {
                    $entity->setStoreId($storeId);
                }

                // Save the create!
                if ($entity->save()) {
                    return $entity;
                }
            }
        }

        return false;
    }

    /**
     * Return the global fields that aren't saved within an entity
     *
     * @return array|null
     */
    public function getGlobalFields()
    {
        if (is_null($this->_globalFields)) {
            $this->_globalFields = array();
            $config = Mage::helper('gene_bluefoot/config')->getConfig('global_fields');
            foreach ($config as $fields) {
                foreach ($fields as $field) {
                    $this->_globalFields[] = (string) $field->code;
                }
            }
        }
        return $this->_globalFields;
    }

    /**
     * Create the entities and update the structure with pointers
     *
     * @param $elements
     */
    public function createStructure(&$elements)
    {
        // Handle the extra element
        $extra = array_filter($elements, function ($element) { return (isset($element['type']) && $element['type'] == 'extra'); });
        if ($extra && !empty($extra)) {
            unset($elements[key($extra)]);
            $extraItem = current($extra);
            $this->handleExtra($extraItem);

            if (isset($extraItem['storeId']) && !empty($extraItem['storeId'])) {
                $this->_storeId = $extraItem['storeId'];
            }
        }

        // Loop through the elements
        foreach($elements as &$element) {

            // If the element has a content type we need to create a new entity
            if(isset($element['contentType'])) {
                if ($fields = $this->getGlobalFields()) {
                    $storeInJson = array();
                    foreach ($fields as $field) {
                        if (isset($element['formData'][$field])) {
                            $storeInJson[$field] = $element['formData'][$field];
                        }
                    }
                }
                $entity = $this->createEntityFromElement($element, $this->_storeId);
                if($entity && $entity->getId()) {
                    $element['entityId'] = $entity->getId();
                    // Remove all form data
                    unset($element['formData']);

                    if (isset($storeInJson)) {
                        // Restore any fields that will be stored in the form data
                        $element['formData'] = $storeInJson;
                    }
                }


                // If it has any children we need to run this method again
                if(isset($element['children']) && is_array($element['children'])) {
                    foreach($element['children'] as $name => &$children) {
                        $this->createStructure($element['children'][$name]);
                    }
                }

            } else {
                // If it has any children we need to run this method again
                if(isset($element['children']) && is_array($element['children'])) {
                    $this->createStructure($element['children']);
                }
            }
        }
    }

    /**
     * Handle any extra data
     *
     * @param $element
     */
    protected function handleExtra($element)
    {
        if (isset($element['deleted']) && is_array($element['deleted']) && !empty($element['deleted'])) {
            $entities = Mage::getResourceModel('gene_bluefoot/entity_collection')
                ->addFieldToFilter('entity_id', array('in' => $element['deleted']));

            if ($this->_storeId) {
                $entities->setStoreId($this->_storeId);
            }

            if ($entities->getSize()) {
                $entities->delete();
            }
        }

        Mage::dispatchEvent('gene_bluefoot_handle_extra', array('element' => $element));
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     * @see http://laravel.com
     */
    public function set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Update the post data in the request
     *
     * @param $elementName
     * @param $data
     */
    public function updatePost($elementName, $data)
    {
        // Place back in our square brackets
        $elementName = str_replace(array('[',']'), array('.',''), $elementName);

        // Use "borrowed" Laravel function
        $this->set($_POST, $elementName, $data);
    }

}