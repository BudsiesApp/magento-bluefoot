<?php

/**
 * Class Gene_BlueFoot_Model_Installer_Attribute
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Installer_Attribute extends Gene_BlueFoot_Model_Installer_Abstract
{

    public function validateAttribute($attributeCode, $attributeData)
    {
        if($this->attributeExists($attributeCode)){
            $this->_validationErrors[$attributeCode][] = 'Attribute already exists';
            return false;
        }

    }

    /**
     * @param $attributeCode
     * @param $attributeData
     * @throws Exception
     */
    public function createAttribute($attributeCode, $attributeData)
    {
        $entityType = $this->_getEntityType();

        unset($attributeData['attribute_id']);
        $attributeData['entity_type_id'] = $entityType->getId();

        $attribute = Mage::getModel('gene_bluefoot/attribute');

        if($this->attributeExists($attributeCode)){
            if($this->_exceptionOnError){
                throw new Exception('Attribute with code "'.$attributeCode.'" already exists');
            }else{
                $this->_errors[$attributeCode][] = 'Attribute already exists';
                return false;
            }
        }

        try{
            $relatedContentBlock = isset($attributeData['entity_allowed_block_type']) ? $attributeData['entity_allowed_block_type'] :false;
            if($relatedContentBlock && trim($relatedContentBlock)!=''){


                if(isset($attributeData['frontend_input']) && $attributeData['frontend_input'] == 'child_entity') {
                    $relatedContentBlockId = false;
                    if ($contentBlock = $this->contentBlockExists($relatedContentBlock)) {
                        $relatedContentBlockId = $contentBlock->getId();
                    } elseif ($contentBlock = $this->contentBlockCreated($relatedContentBlock)) {
                        $relatedContentBlockId = $contentBlock->getId();
                    } else {
                        throw new Exception('Attribute requires content block: "' . $relatedContentBlock . '"');
                    }

                    $attributeData['additional']['entity_allowed_block_type'] = $relatedContentBlockId;
                }
            }

            $attribute->setData($attributeData);

            if($this->isLiveMode()){
                $attribute->setEntityTypeId($entityType->getId());
                $attribute->save();
            }

            $this->_createdEntities[$attributeCode] = $attribute;
        }catch (Exception $e){
            if($this->_exceptionOnError) {
                throw $e;
            }else{
                $this->_errors[$attributeCode][] = 'Exception: ' . $e->getMessage();
                $this->_exceptions[$attributeCode][] = $e;
                return false;
            }
        }

        return $attribute;

    }

}