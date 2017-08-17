<?php
class Gene_BlueFoot_Helper_Eav extends Mage_Core_Helper_Abstract
{
    /**
     * Return default attribute backend model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    /**
     * @param string $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'multiselect'   => array(
                'backend_model'     => 'eav/entity_attribute_backend_array',
                'source_model'      => 'eav/entity_attribute_source_table'
            ),
            'image'   => array(
                'backend_model'     => 'gene_bluefoot/attribute_backend_image'
            ),
            'file'   => array(
                'backend_model'     => 'gene_bluefoot/attribute_backend_file'
            ),
            'gallery'   => array(
                'backend_model'     => 'gene_bluefoot/attribute_backend_gallery'
            ),
            'media_gallery'   => array(
                'backend_model'     => 'gene_bluefoot/attribute_backend_gallery'
            ),
            'entity_list'   => array(
                'backend_model'     => 'eav/entity_attribute_backend_array'
            ),
            'child_entity'   => array(
                'backend_model'     => 'eav/entity_attribute_backend_array',
                'source_model'      => 'gene_bluefoot/attribute_source_entity_child'
            ),
            'boolean'       => array(
                'source_model'      => 'eav/entity_attribute_source_boolean'
            )
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }
}