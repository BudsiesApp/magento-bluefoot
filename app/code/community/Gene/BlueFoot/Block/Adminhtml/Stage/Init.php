<?php

/**
 * Build up the initial configuration the stage needs to be built, this contains basic information regarding
 * ID's associated with the mustache templates, alongside other values that are quick to generate and needed before
 * the full config call is made
 *
 * Class Gene_BlueFoot_Block_Adminhtml_Stage_Init
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Stage_Init extends Mage_Core_Block_Template
{
    /**
     * Return the initial configuration
     *
     * @return string
     */
    public function getConfig()
    {
        $config = new Varien_Object();
        $config->addData(array(
            'encode_string' => Gene_BlueFoot_Model_Stage_Save::GENE_CMS_STRING,
            'stage_template' => '#gene-bluefoot-stage-template',
            'template_template' => '#gene-bluefoot-stage-template-controls',
            'panel_template' => '#gene-bluefoot-stage-panel-template',
            'row_template' => '#gene-bluefoot-row-template',
            'column_template' => '#gene-bluefoot-column-template',
            'option_template' => '#gene-bluefoot-option-template',
            'entity_template' => '#gene-bluefoot-entity',
            'entity_preview_template' => '#gene-bluefoot-entity-preview',
            'configure_template' => '#gene-bluefoot-stage-configure-template',
            'alert_template' => '#gene-bluefoot-alert',
            'form_key' => Mage::getSingleton('core/session')->getFormKey(),
            'init_button_class' => '.init-gene-bluefoot',
            'config_url' => Mage::helper('adminhtml')->getUrl('*/stage/config'),
            'data_update_url' => Mage::helper('adminhtml')->getUrl('*/stage/dataUpdate'),
            'template_save_url' => Mage::helper('adminhtml')->getUrl('*/stage/saveTemplate'),
            'template_delete_url' => Mage::helper('adminhtml')->getUrl('*/stage/deleteTemplate'),
            'template_pin_url' => Mage::helper('adminhtml')->getUrl('*/stage/pinTemplate'),
            'columns' => 6,
            'template_selection_grid_template' => '#gene-bluefoot-template-selection-grid',

            /* Define the different column options to be given in the UI */
            'column_options' => array(
                1 => 'One',
                2 => 'Two',
                3 => 'Three',
                4 => 'Four',
                6 => 'Six'
            ),

            /* Allowed sizes have to be at 3 decimal places */
            'allowed_sizes' => array(
                '0.167' => '1/6',
                '0.250' => '1/4',
                '0.333' => '1/3',
                '0.500' => '1/2',
                '0.666' => '2/3',
                '0.750' => '3/4',
                '0.825' => '5/6',
                '1.000' => '1'
            ),

            /* Some sizes do not map correctly, so manually specify them */
            'actual_css_size' => array(
                '0.167' => '16.666666667'
            )
        ));

        // Include our plugin information
        $config->setData('plugins', Mage::getModel('gene_bluefoot/stage_plugin')->getJsPlugins());

        // Fire event to allow extra data to be passed to the stage
        Mage::dispatchEvent('gene_bluefoot_stage_build_config', array('config' => $config));

        return $config->toJson();
    }
}