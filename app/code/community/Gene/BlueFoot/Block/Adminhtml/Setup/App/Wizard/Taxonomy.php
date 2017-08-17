<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Taxonomy
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Taxonomy extends Mage_Adminhtml_Block_Widget_Form
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('gene/bluefoot/setup/app/wizard/taxonomy.phtml');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                "id" => "edit_form",
                "action" => $this->getUrl("*/*/saveStep", array("id" => $this->getRequest()->getParam("app_id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

//        $fieldset->addField('add_taxonomy', 'button', array(
//            'value' => 'Create New Taxonomy',
//            'onclick' => "appWizard.loadAdditional('".$this->getUrl('*/*/addTaxonomy')."');"
//        ));
        


        return parent::_prepareForm();
    }

    protected function _hasNewTaxonomies()
    {
        return (bool)$this->_getNewTaxonomies();
    }

    protected function _getNewTaxonomies()
    {
        if($wizardModel = Mage::registry('app_wizard')){

            $taxonomyData = $wizardModel->getStepData('taxonomy_data');
            if($taxonomyData && isset($taxonomyData['taxonomies'])){
                $taxonomies = $taxonomyData['taxonomies'];
                if(is_array($taxonomies)){
                    return $taxonomies;
                }
            }
        }

        return false;
    }
}