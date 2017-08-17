<?php
/**
 * Class Gene_BlueFoot_Model_Installer_App
 *
 * @author Mark Wallman <mark@gene.co.uk>
 *
 */
class Gene_BlueFoot_Model_Installer_App extends Gene_BlueFoot_Model_Installer_Abstract
{

    public function validateApp(Varien_Object $appData)
    {
        $existingApp = Mage::getModel('gene_bluefoot/app');
        $existingApp->load($appData->getTitle(), 'title');
        if($existingApp->getId()){
            if($this->_exceptionOnError){
                throw new Mage_Exception('Content app with name "'.$appData->getTitle().'" already exists.');
            }else{
                $this->_errors[$appData->getTitle()] = 'Content app with name "'.$appData->getTitle().'" already exists.';
                return false;
            }
        }

        $existingApp->load($appData->getUrlPrefix(), 'url_prefix');
        if($existingApp->getId()){
            if($this->_exceptionOnError){
                throw new Mage_Exception('Content app with url prefix "'.$appData->getUrlPrefix().'" already exists.');
            }else{
                $this->_errors[$appData->getTitle()] = 'Content app with url prefix "'.$appData->getUrlPrefix().'" already exists.';
                return false;
            }
        }

        return true;
    }

    public function createApp($identifier, $appRawData)
    {
        if(!is_object($appRawData)){
            $appData = new Varien_Object($appRawData);
        }else{
            $appData = $appRawData;
        }

        $appData->unsetData('app_id');
        $appData->unsetData('content_types');
        $appData->unsetData('taxonomies');

        if($viewOptions = $appData->getViewOptionsSerialized()){
            $viewOptions = @unserialize($viewOptions);
            if($viewOptions && is_array($viewOptions)){
                $appData->setViewOptions($viewOptions);
            }
        }

        try{
            if(!$this->validateApp($appData)){
                //return false;
                throw new Exception('App data failed validation');
            }
        }catch (Exception $e){
            throw $e;
        }

        $appModel = Mage::getModel('gene_bluefoot/app');
        $appModel->setData($appData->getData());
        if($this->isLiveMode()){
            $appModel->save();
        }

        $this->_createdEntities['apps'][] = $appModel;

        return $appModel;
    }

    /**
     * @param $identifier
     * @param $appRawData
     * @return Gene_BlueFoot_Model_App
     * @throws Exception
     */
    public function createFullApp($identifier, $appRawData)
    {
        $createdContentTypes = array();
        $appData = new Varien_Object($appRawData);
        $appData->unsetData('app_id');

        if($viewOptions = $appData->getViewOptionsSerialized()){
            $viewOptions = @unserialize($viewOptions);
            if($viewOptions && is_array($viewOptions)){
                $appData->setViewOptions($viewOptions);
            }
        }

        $contentTypesData = is_array($appData->getContentTypes()) ? $appData->getContentTypes() : array();
        $appData->unsetData('content_types');

        $taxonomies = is_array($appData->getTaxonomies()) ? $appData->getTaxonomies() : array();
        $appData->unsetData('taxonomies');

        try{
            if(!$this->validateApp($appData)){
                //return false;
            }
        }catch (Exception $e){
            throw $e;
        }


        $appModel = Mage::getModel('gene_bluefoot/app');

        $createdContentTypes = array();
        $createdTaxonomies = array();

        try {
            $appModel->setData($appData->getData());



            if($this->isLiveMode()){
                $appModel->getResource()->beginTransaction();
                $appModel->save();
            }

            foreach($contentTypesData as $contentType){

                $contentType['app_id'] = $appModel->getId();

                $contentTypeIdentifier = isset($contentType['identifier']) ? $contentType['identifier'] : false;
                if(!$contentTypeIdentifier){
                    throw new Exception('App content type has no identifier specified');
                }
                $createdContentTypes[] = $this->createContentType($contentTypeIdentifier, $contentType);
            }

            foreach($taxonomies as $taxonomy){
                $taxonomy['app_id'] = $appModel->getId();
                $createdTaxonomies[] = $this->createTaxonomy($taxonomy);
            }

            //all or nothing
            if(count($this->getErrors())){
                return false;
            }

            if($this->isLiveMode()) {
                $appModel->getResource()->commit();
            }

        }catch (Exception $e){
            if($this->isLiveMode()) {
                //all or nothing
                $appModel->getResource()->rollBack();
            }
            throw $e;
        }

        $this->_createdEntities = array();

        $this->_createdEntities['apps'] = array($appModel);
        $this->_createdEntities['content_types'] = $createdContentTypes;
        $this->_createdEntities['taxonomies'] = $createdTaxonomies;


        return $appModel;

    }

    /**
     * @param $taxonomyRawData
     * @return Gene_BlueFoot_Model_Taxonomy
     * @throws Exception
     */
    public function createTaxonomy($taxonomyRawData)
    {
        if(!is_object($taxonomyRawData)) {
            $taxonomyData = new Varien_Object($taxonomyRawData);
        }else{
            $taxonomyData = $taxonomyRawData;
        }

        $taxonomy = Mage::getModel('gene_bluefoot/taxonomy');

        $createdTerms = array();
        $terms = $taxonomyData->getTerms();

        $taxonomyData->unsetData('terms');

        $taxonomy->setData($taxonomyData->getData());

        $taxonomy->save();

        if((is_array($terms) || $terms instanceof Varien_Data_Collection) && count($terms)){
            foreach($terms as $term){
                $createdTerms[] = $this->createTaxonomyTerm($taxonomy->getId(), $term);
            }
        }

        $taxonomy->setData('installed_terms', $createdTerms);

        return $taxonomy;
    }

    /**
     * @param $taxonomyId
     * @param $termRawData
     * @return Gene_BlueFoot_Model_Taxonomy_Term
     * @throws Exception
     */
    public function createTaxonomyTerm($taxonomyId, $termRawData)
    {
        if(!is_object($termRawData)){
            $termData = new Varien_Object($termRawData);
        }else{
            $termData = $termRawData;
        }

        $childTerms = $termData->getChildren();
        $termData->unsetData('children');
        $termData->unsetData('store_id');

        $entityModel = Mage::getModel('gene_bluefoot/taxonomy_term');
        /**
         * @var $entityModel Gene_BlueFoot_Model_Taxonomy_Term
         */

        $entityModel->setData($termData->getData());
        $entityModel->setTaxonomyId($taxonomyId);

        $entityModel->validate();
        $entityModel->save();
        $termId = $entityModel->getId();

        if(is_array($childTerms) || $childTerms instanceof Varien_Data_Collection){
            foreach($childTerms as $childTerm){
                if(is_object($childTerm)){
                    $childTerm->setParentId($termId);
                }elseif(is_array($childTerm)){
                    $childTerm['parent_id'] = $termId;
                }else{
                    continue;
                }
                $this->createTaxonomyTerm($taxonomyId, $childTerm);
            }
        }

        return $entityModel;
    }

    /**
     * @param $identifier
     * @param $blockDataRaw
     * @return Gene_BlueFoot_Model_Type
     * @throws Exception
     */
    public function createContentType($identifier, $typeDataRaw)
    {
        if(!is_object($typeDataRaw)){
            $typeData = new Varien_Object($typeDataRaw);
        }else{
            $typeData = $typeDataRaw;
        }

        $entityType = $this->_getEntityType();
        $model = Mage::getModel("gene_bluefoot/type");
        $model->initNewContentType();

        $typeData->unsetData('type_id');
        $attributeData = $typeData->getAttributeData();
        if(!$attributeData){
            if(!$typeData->getUseDefaultAttributes()){
                if($this->_exceptionOnError){
                    throw new Exception('No attribute data associated with content type');
                }else{
                    $this->_errors[$identifier] = 'No attribute data associated with content type';
                    return false;
                }
            }

        }

        $typeData->unsetData('attribute_data');

        $attributes = (isset($attributeData['attributes']) && is_array($attributeData['attributes'])) ? $attributeData['attributes'] : false;
        $attributeGroups = (isset($attributeData['groups'])  && is_array($attributeData['attributes'])) ? $attributeData['groups'] : false;

        if(!$attributes && !$typeData->getUseDefaultAttributes()){
            throw new Exception('No attributes associated');
        }

        if($this->contentTypeExists($identifier)){
            if($this->_exceptionOnError){
                throw new Mage_Exception('Content type with identifier "'.$identifier.'" already exists.');
            }else{
                $this->_errors[$identifier] = 'Content type with identifier "'.$identifier.'" already exists.';
                return false;
            }

        }

        try {
            $model->initNewContentType();
            $model->addData($typeData->getData());
            if($this->isLiveMode()){

                try {

                    $model->getResource()->beginTransaction();
                    $model->save();

                    $attributeSet = $model->getAttributeSet();

                    if($typeData->getUseDefaultAttributes()){
                        //create content type using default attribute set
                        $attributeSet->initFromSkeleton($model->getDefaultAttributeSet()->getId());
                    }else{

                        //create attribute set and groups based on data
                        $newGroups = array();
                        foreach ($attributeGroups as $group) {

                            $groupAttributes = isset($group['attributes']) ? $group['attributes'] : array();
                            unset($group['attributes']);

                            $modelGroup = Mage::getModel('eav/entity_attribute_group');
                            $modelGroup->setData($group);
                            $modelGroup->setAttributeSetId($attributeSet->getId());


                            $attributeCodes = array();
                            $attributeSorts = array();
                            $lastAttrSort = 0;
                            $largestAttrSort = 0;

                            foreach ($groupAttributes as $gAttribute) {
                                $lastAttrSort++;
                                $attrCode = isset($gAttribute['attribute_code']) ? $gAttribute['attribute_code'] : false;
                                $attrSort = isset($gAttribute['sort_order']) ? $gAttribute['sort_order'] : $lastAttrSort;
                                if ($attrCode) {
                                    $attributeCodes[] = $attrCode;
                                    $attributeSorts[$attrCode] = $attrSort;
                                    $lastAttrSort = $attrSort;
                                    if($largestAttrSort < $attrSort){
                                        $largestAttrSort = $attrSort;
                                    }
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
                                    $gAttrSort = isset($attributeSorts[$gAttribute->getAttributeCode()]) ? $attributeSorts[$gAttribute->getAttributeCode()] : $largestAttrSort;
                                    $newAttribute = Mage::getModel('eav/entity_attribute')
                                        ->setId($gAttribute->getId())
                                        ->setAttributeSetId($attributeSet->getId())
                                        ->setEntityTypeId($attributeSet->getEntityTypeId())
                                        ->setSortOrder($gAttrSort);

                                    $modelAttributeArray[] = $newAttribute;
                                }
                                $modelGroup->setAttributes($modelAttributeArray);
                                $newGroups[] = $modelGroup;
                            }

                        }

                        $attributeSet->setGroups($newGroups);
                    }

                    $attributeSet->save();

                    $model->validate();

                    $model->getResource()->commit();

                }catch (Exception $e){
                    $model->getResource()->rollBack();
                    throw $e;
                }
            }

        }catch (Exception $e){
            if($this->_exceptionOnError){
                throw $e;
            }else{
                $this->_errors[$identifier] = $e->getMessage();
                return false;
            }
        }

        $this->_createdEntities[$identifier] = $model;

        return $model;
    }


}