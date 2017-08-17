<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Attribute
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Installer_Create_Tab_Attribute extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $attributeCollection = Mage::getResourceModel('gene_bluefoot/attribute_collection')
            ->addVisibleFilter();

        $fieldset = $form->addFieldset("gene_cms_installer_attribute_settings", array("legend" => Mage::helper("gene_bluefoot")->__("Settings")));

        $fieldset->addField('attr_note_s', 'note', array(
            'text'     => Mage::helper('gene_bluefoot')->__('Settings - To Do'),
        ));

        $fieldset = $form->addFieldset("gene_cms_installer_attribute_export", array("legend" => Mage::helper("gene_bluefoot")->__("Content Attributes"), 'class' => 'fieldset-wide'));

        foreach ($attributeCollection as $attr) {

            $attrDetails = $attr->toArray(array('attribute_code', 'frontend_input', 'frontend_input_renderer', 'template'));
            $attrDetailsArray = array();
            foreach($attrDetails as $detailKey => $detail){
                $detail = $this->escapeHtml($detail);
                if(strlen($detail) > 100){
                    $detail = substr($detail, 0, 100);
                    $detailKey .= '(trimmed)';
                }
                $attrDetailsArray[] = '['.$detailKey . '] => ' . $detail;
            }

            $fieldset->addField('content_block_' . $attr->getAttributeCode(), 'checkbox', array(
                'label' => $attr->getFrontend()->getLabel(),
                'name' => 'export[attributes][]',
                'value' => $attr->getAttributeCode(),
                'after_element_html' => '<br/><small><b>Details:</b> <br/>' . implode(', ', $attrDetailsArray) . '</small>'
            ));
        }


        return parent::_prepareForm();
    }

}