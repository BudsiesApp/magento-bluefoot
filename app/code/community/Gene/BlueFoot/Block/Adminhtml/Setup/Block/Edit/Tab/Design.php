<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Design
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Return the type data
     *
     * @return Gene_BlueFoot_Model_Type
     */
    public function getTypeData()
    {
        return Mage::registry("type_data");
    }

    protected function _getThemeBaseDir()
    {
        return Mage::getSingleton('core/design_package')->getBaseDir(array('_area' => 'frontend', '_type' => 'template', '_package' => 'base'));
    }

    /**
     * Returns an array of available templates from custom config
     *
     * @param string $type
     *
     * @return array
     */
    protected function _getPossibleTemplates($type = 'block')
    {
        $list = array();

        $configHelper = Mage::helper('gene_bluefoot/config');
        $blockTemplates = $configHelper->getBlockConfig('templates');

        if ($blockTemplates) {
            foreach ($blockTemplates->children() as $template) {
                $templateId = $template->getName();
                if (isset($template->file)) {
                    $list[$templateId] = $template->file;
                }
            }
        }

        return $list;
    }

    /**
     * Returns an array of available renderers from custom config
     *
     * @param string $type
     *
     * @return array
     */
    protected function _getPossibleRenderers($type = 'block')
    {
        $list = array();

        $configHelper = Mage::helper('gene_bluefoot/config');
        $renderers = $configHelper->getBlockConfig('renderers');

        if ($renderers) {
            foreach ($renderers->children() as $renderer) {
                $rendererId = $renderer->getName();
                if (isset($renderer->class)) {
                    $list[$rendererId] = $renderer->class;
                }
            }
        }

        return $list;
    }

    /**
     * Return the associated fields that can be potentially used for a preview
     *
     * @return array|bool
     */
    protected function _getPreviewFields()
    {
        $type = $this->getTypeData();
        if ($type->getId()) {
            $options = array(
                '' => Mage::helper("gene_bluefoot")->__('-- Please Select --')
            );

            /* @var $attribute Gene_BlueFoot_Model_Resource_Eav_Attribute */
            foreach ($type->getAllAttributes() as $attribute) {
                if (!$attribute->isStatic() && ($attribute->getBackendType() == 'varchar' || $attribute->getBackendType() == 'text')) {
                    $options[$attribute->getAttributeCode()] = $attribute->getFrontend()->getLabel();
                }
            }

            return $options;
        }

        return false;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("gene_cms_form_design_templates", array("legend" => Mage::helper("gene_bluefoot")->__("Templates (to override default)")));

        $templateFiles = $this->_getPossibleTemplates();
        if (count($templateFiles)) {
            $templateFiles = array_merge(
                array(
                    '' => Mage::helper("gene_bluefoot")->__('-- Please Select --')
                ),
                $templateFiles
            );
        } else {
            $templateFiles = array(
                '' => Mage::helper("gene_bluefoot")->__('No block templates available')
            );
        }

        $renderers = $this->_getPossibleRenderers();
        if (count($renderers)) {
            $renderers = array_merge(
                array(
                    '' => Mage::helper("gene_bluefoot")->__('-- Please Select --')
                ),
                $renderers
            );
        } else {
            $renderers = array(
                '' => Mage::helper("gene_bluefoot")->__('No block renderers available')
            );
        }

        $fieldset->addField("item_view_template", "select", array(
            "label"   => Mage::helper("gene_bluefoot")->__("Block Template"),
            "name"    => "item_view_template",
            'options' => $templateFiles,
        ));

        $fieldset->addField("renderer", "select", array(
            "label"   => Mage::helper("gene_bluefoot")->__("Renderer"),
            "name"    => "renderer",
            'options' => $renderers,
        ));

        $fieldset = $form->addFieldset("gene_cms_form_design_preview", array("legend" => Mage::helper("gene_bluefoot")->__("Preview")));

        $fieldset->addField("preview_field", "select", array(
            "label"   => Mage::helper("gene_bluefoot")->__("Preview Field"),
            "name"    => "preview_field",
            'options' => $this->_getPreviewFields(),
            'note'    => Mage::helper('gene_bluefoot')->__('The field to be used to display a simple preview of the content block to the user within the page builder.<br /><strong>Please note:</strong> the options in this select box will only be updated after save. This is also limited to text fields only.')
        ));

        if (Mage::getSingleton("adminhtml/session")->getTypeData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getTypeData());
            Mage::getSingleton("adminhtml/session")->setTypeData(null);
        } elseif (Mage::registry("type_data")) {
            $form->setValues(Mage::registry("type_data")->getData());
        }

        return parent::_prepareForm();
    }

}
