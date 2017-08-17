<?php

/**
 * Class Gene_BlueFoot_Model_App_Wizard
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_App_Wizard extends Varien_Object
{
    protected $_firstStep = 'app_info';

    protected $_stepFormData = array();

    public function getSteps()
    {
        $steps = array(
            'app_info' => new Varien_Object(array(
                'title' => 'App info',
                'is_complete' => false,
                'block' => 'gene_bluefoot/adminhtml_setup_app_wizard_info',
                'data_model' => 'gene_bluefoot/app_wizard_step_info',
                'next_step' => 'content_types',
                'user_info' => 'Welcome to the Content App wizard. Please start by entering some basic information about your content app.'
            )),
            'content_types' => new Varien_Object(array(
                'title' => 'Content types',
                'is_complete' => false,
                'block' => 'gene_bluefoot/adminhtml_setup_app_wizard_types',
                'data_model' => 'gene_bluefoot/app_wizard_step_types',
                'user_info' => 'Content types are the building blocks of an app. They define a single unit of content that will be managed within your app and what fields are associated with.<br/> For example, If you were creating a Blog App you may have a content type of "Blog Post"',
                'next_step' => 'taxonomy'
            )),
            'taxonomy' => new Varien_Object(array(
                'title' => 'Taxonomy/Categorisation',
                'is_complete' => false,
                'block' => 'gene_bluefoot/adminhtml_setup_app_wizard_taxonomy',
                'user_info' => 'Taxonomies provide a way to categorise your content. A taxonomy is broken down into terms and content may have one or more terms associated with it.<br/>
                Examples of taxonomies may be Category, Author, Tags',
                'next_step' => 'review'
            )),
            /*'config' => new Varien_Object(array(
                'title' => 'Configuration',
                'is_complete' => false,
                'block' => 'gene_bluefoot/adminhtml_setup_app_wizard_config',
                'next_step' => 'review'
            )),*/

            'review' => new Varien_Object(array(
                'title' => 'Review',
                'is_complete' => false,
                'block' => 'gene_bluefoot/adminhtml_setup_app_wizard_review',
                'user_info' => 'Please review your options from the wizard. When you are happy click "Next Step" to continue.'
            )),
        );

        $currentStepId = $this->getCurrentStepId();
        $completedSteps = $this->getCompletedSteps();
        $previousStep = 'app_info';
        foreach($steps as $stepId => $step){
            if($stepId == $currentStepId){
                $step->setIsCurrent(true);
            }
            if(in_array($stepId, $completedSteps)){
                $step->setIsComplete(true);
            }

            $step->setPreviousStep($previousStep);

            $previousStep = $stepId;
        }

        return $steps;
    }

    /**
     * @param string $stepId
     * @param $data
     * @return $this
     */
    public function setStepData($stepId, $data)
    {
        $this->_stepFormData[$stepId] = $data;
        $this->getWizardSession()->setStepData($stepId, $data);
        return $this;
    }

    /**
     * @param $stepId
     * @return mixed
     */
    public function getStepData($stepId)
    {
        return $this->getWizardSession()->getStepData($stepId);
    }

    /**
     * @param null $currentStepId
     * @return Varien_Object|bool
     */
    public function nextStep($currentStepId = null)
    {
        if(is_null($currentStepId)){
            $currentStepId = $this->getCurrentStepId();
        }

        $currentStep = $this->getStep($currentStepId);
        $nextStepId = $currentStep->getNextStep();

        if(!$nextStepId){
            return false;
        }

        $nextStep = $this->getStep($nextStepId);

        if(!$nextStep){
            return false;
        }

        $this->addCompletedStep($currentStepId);

        $this->setCurrentStep($nextStepId);

        return $nextStep;
    }

    public function setCompletedSteps(array $stepIds)
    {
        $this->getWizardSession()->setCompletedSteps($stepIds);
        return $this;
    }

    public function removeCompletedStep($stepId)
    {
        $completedSteps = $this->getCompletedSteps();

        $key = array_search($stepId, $completedSteps);
        if($key !== false){
            unset($completedSteps[$key]);
        }

        $this->setCompletedSteps($completedSteps);
        return $this;
    }

    public function addCompletedStep($stepId)
    {
        $completedSteps = $this->getCompletedSteps();
        $completedSteps[] = $stepId;
        $this->setCompletedSteps($completedSteps);
        return $this;
    }

    /**
     * @return array
     */
    public function getCompletedSteps()
    {
        return $this->getWizardSession()->getCompletedSteps();
    }

    public function getCurrentStep($fallback = null)
    {
        $currentStepId = $this->getCurrentStepId($fallback);

        $currentStep = $this->getStep($currentStepId);
        return $currentStep;
    }

    /**
     * @param string $fallback
     * @return string|null
     */
    public function getCurrentStepId($fallback = null)
    {
        $session = $this->getWizardSession();
        $currentStepId = ($session->getCurrentStep() ? $session->getCurrentStep() : ($fallback ? $fallback : $this->_firstStep));

        return $currentStepId;
    }

    public function setCurrentStep($stepId)
    {
        if(is_object($stepId)){
            $stepId = $stepId->getId();
        }

        $this->getWizardSession()->setCurrentStep($stepId);

        return $this;
    }

    /**
     * @return Gene_BlueFoot_Model_App_Wizard_Session
     */
    public function getWizardSession()
    {
        return Mage::getSingleton('gene_bluefoot/app_wizard_session');
    }

    /**
     * @return Gene_BlueFoot_Model_App_Wizard_Session
     */
    public function initNewSession()
    {
        $this->clearSession();

        return $this->getWizardSession();
    }

    /**
     * @return $this
     */
    public function clearSession()
    {
        $session = $this->getWizardSession();
        $session->clear();
        return $this;
    }

    /**
     * @param $stepId
     * @return Varien_Object|null
     */
    public function getStep($stepId)
    {
        $steps = $this->getSteps();
        return array_key_exists($stepId, $steps) ? $steps[$stepId] : null;
    }

    public function validateStepData()
    {
        return true;
    }

    public function create()
    {
        $appModel = Mage::getModel('gene_bluefoot/app');

        $resource = $appModel->getResource();



        try{
            $resource->beginTransaction();

            //content types
            $contentTypeData = new Varien_Object($this->getStepData('content_type_data'));
            $contentTypes = $contentTypeData->getTypeData();

            if(!is_array($contentTypes) || is_array($contentTypes) && !count($contentTypes)){
                throw new Mage_Exception('No content type data exists');
            }

            foreach($contentTypes as $cTypeData){
                $typeModel = Mage::getModel("gene_bluefoot/type");
                $typeModel->initNewContentType();
                $setsData = (isset($cTypeData['sets_json']) && $cTypeData['sets_json'] != '') ? $cTypeData['sets_json'] : false;
                unset($cTypeData['sets_json']);

                $typeModel->addData($cTypeData);

                $typeModel->save();

                $contentTypeIds[] = $typeModel->getId();

                //Save groups and sets data
                if ($setsData) {
                    $setsData = Mage::helper('core')->jsonDecode($setsData);
                    $attributeSetName = $typeModel->getName() . ' {' . $typeModel->getIdentifier() .'}';
                    $setsData['attribute_set_name'] = $attributeSetName;

                    //if it's a new type we need to adjust the data as it will be using the skeleton attribute set and groups
                    $setsData = $typeModel->processNewSetData($setsData);


                    $attributeSet = $typeModel->getAttributeSet();
                    $attributeSet->organizeData($setsData);
                    $attributeSet->validate();
                    $attributeSet->save();
                }

            }

            //taxonomies
            $taxonomyData = new Varien_Object($this->getStepData('taxonomy'));
            $taxonomyIds = $taxonomyData->getTaxonomyIds() ? $taxonomyData->getTaxonomyIds() : array();

            $newTaxonomyData = $this->getStepData('taxonomy_data');
            $newTaxonomies = isset($newTaxonomyData['taxonomies']) ? $newTaxonomyData['taxonomies'] : false;

            if(is_array($newTaxonomies) && count($newTaxonomies)){
                foreach($newTaxonomies as $newTaxonomy){
                    $taxonomyModel = Mage::getModel('gene_bluefoot/taxonomy');
                    $taxonomyModel->addData($newTaxonomy);
                    $taxonomyModel->save();
                    $taxonomyIds[] = $taxonomyModel->getId();
                }
            }


            $appData = $this->getStepData('app_info');

            $appData['content_type_ids'] = $contentTypeIds;
            $appData['taxonomy_ids'] = $taxonomyIds;

            //Parent App
            $app = Mage::getModel('gene_bluefoot/app');

            $app->addData($appData);
            $app->updateRelatedEntitesOnSave();
            $app->save();

            $resource->commit();
        }catch (Mage_Exception $e){

            echo 'Exception: '.$e->getMessage();
            $resource->rollBack();
            die();
        }catch(Exception $e){
            echo 'Exception: '.$e->getMessage();
            $resource->rollBack();
            die();
        }


        return $app;

    }
}