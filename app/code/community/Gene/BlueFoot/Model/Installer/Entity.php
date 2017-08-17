<?php

/**
 * Class Gene_BlueFoot_Model_Installer_Entity
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Installer_Entity extends Gene_BlueFoot_Model_Installer_Abstract
{
    public function validateEntity()
    {

    }

    public function createEntity($contentTypeId, $entityRawData)
    {
        if(!is_object($entityRawData)){
            $entityData = new Varien_Object($entityRawData);
        }else{
            $entityData = $entityRawData;
        }

        $taxonomyTerms = $entityData->getTaxonomyTerms();

        $contentType = Mage::getModel('gene_bluefoot/type')->load($contentTypeId);
        if(!$contentType->getId() || $contentType->getContentType() != 'content'){
            throw new Exception('Cannot create entity, No such content type exists with ID: ' . $contentTypeId);
        }

        $attributeSet = $contentType->getAttributeSet();
        if(!$attributeSet){
            throw new Exception('Cannot create entity, No attribute set assigned to content type');
        }

        $entityModel = Mage::getModel('gene_bluefoot/entity');
        $entityModel->setAttributeSetId($attributeSet->getId());

        $entityModel->addData($entityData->getData());

        $entityModel->save();
        $entityId = $entityModel->getId();

        return $entityModel;
    }

}