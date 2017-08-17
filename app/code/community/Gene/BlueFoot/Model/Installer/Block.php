<?php

/**
 * Class Gene_BlueFoot_Model_Installer_Block
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Installer_Block extends Gene_BlueFoot_Model_Installer_Abstract
{
    /**
     * @param $identifier
     * @param $blockDataRaw
     * @return Gene_BlueFoot_Model_Type
     * @throws Exception
     */
    public function createContentBlock($identifier, $blockDataRaw)
    {
        $blockData = new Varien_Object($blockDataRaw);
        $entityType = $this->_getEntityType();

        $blockData->unsetData('type_id');
        if(!$attributeData = $blockData->getAttributeData()){
            throw new Exception('No attribute data exists');
        }

        $blockGroupCode = $blockData->getGroup();
        $blockData->unsetData('group');

        $blockData->unsetData('attribute_data');

        $attributes = (isset($attributeData['attributes']) && is_array($attributeData['attributes'])) ? $attributeData['attributes'] : false;
        $attributeGroups = (isset($attributeData['groups'])  && is_array($attributeData['attributes'])) ? $attributeData['groups'] : false;

        if(!$attributes){
            throw new Exception('No attributes associated');
        }

        $model = Mage::getModel("gene_bluefoot/type");

        if($this->contentBlockExists($identifier)){
            if($this->_exceptionOnError){
                throw new Exception('Content block with identifier "'.$identifier.'" already exists.');
            }else{
                $this->_errors[$identifier][] = 'Content block with same identifier already exists.';
                return false;
            }

        }

        $missingAttributes = array();
        foreach($attributes as $blockAttr){
            if(!$this->attributeExists($blockAttr)){
                $missingAttributes[] = $blockAttr;
            }

            if(count($missingAttributes) > 0){
                if($this->_exceptionOnError){
                    throw new Exception('There are missing attributes required by this block. Attributes: ' . implode(', ', $missingAttributes));
                }else{
                    foreach($missingAttributes as $mAttr){
                        $this->_errors[$identifier][] = 'Required attribute ('.$mAttr.') not installed.';
                    }
                    return false;
                }
            }
        }


        try {
            $model->initNewBlockType();
            $model->setData($blockData->getData());
            if($this->isLiveMode()) {

                try {

                    $model->getResource()->beginTransaction();

                    if($blockGroupCode){
                        $blockGroupModel = Mage::getModel('gene_bluefoot/type_group');
                        $blockGroupModel->load($blockGroupCode, 'code');
                        if(!$blockGroupModel->getId()){
                            //if one doesn't exist we create it
                            $blockGroupModel->setCode($blockGroupCode);
                            $blockGroupModel->setName(uc_words($blockGroupCode));
                            $blockGroupModel->setSortOrder(99);
                            $blockGroupModel->setIcon('<i class="fa fa-chevron-down"></i>');
                            $blockGroupModel->save();
                        }

                        $model->setGroupId($blockGroupModel->getId());
                    }

                    $model->save();

                    $attributeSet = $model->getAttributeSet();

                    $newGroups = array();
                    foreach ($attributeGroups as $group) {

                        $groupAttributes = isset($group['attributes']) ? $group['attributes'] : array();
                        unset($group['attributes']);

                        $modelGroup = Mage::getModel('eav/entity_attribute_group');
                        $modelGroup->setData($group);
                        $modelGroup->setAttributeSetId($attributeSet->getId());


                        $attributeCodes = array();
                        foreach ($groupAttributes as $gAttribute) {
                            $attrCode = isset($gAttribute['attribute_code']) ? $gAttribute['attribute_code'] : false;
                            if ($attrCode) {
                                $attributeCodes[] = $attrCode;
                            }
                        }

                        if (count($attributeCodes)) {
                            $groupAttributesCollection = Mage::getModel('eav/entity_attribute')
                                ->getResourceCollection()
                                ->setCodeFilter($attributeCodes)
                                ->setEntityTypeFilter($attributeSet->getEntityTypeId())
                                ->load();

                            $modelAttributeArray = array();
                            foreach ($groupAttributesCollection as $gAttribute) {
                                $newAttribute = Mage::getModel('eav/entity_attribute')
                                    ->setId($gAttribute->getId())
                                    ->setAttributeSetId($attributeSet->getId())
                                    ->setEntityTypeId($attributeSet->getEntityTypeId())
                                    ->setSortOrder($gAttribute->getSortOrder());


                                $modelAttributeArray[] = $newAttribute;
                            }
                            $modelGroup->setAttributes($modelAttributeArray);
                            $newGroups[] = $modelGroup;
                        }

                    }


                    $attributeSet->setGroups($newGroups);
                    $attributeSet->save();

                    $model->validate();

                    $model->getResource()->commit();

                }catch (Exception $e){
                    $model->getResource()->rollBack();
                    throw $e;
                }

            }
        }catch (Exception $e){
            throw $e;
        }

        $this->_createdEntities[$identifier] = $model;

        return $model;
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function contentBlockExists($identifier)
    {
        $model = Mage::getModel("gene_bluefoot/type");
        $model->load($identifier, 'identifier');
        if($model->getId()){
            return true;
        }

        return false;
    }
}