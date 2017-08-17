<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Renderer_Fieldset_Element
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{

    protected function _construct()
    {
        $this->setTemplate('gene/bluefoot/entity/form/renderer/fieldset/element.phtml');
    }

    public function getDataObject()
    {
        return $this->getElement()->getForm()->getDataObject();
    }


    public function getAttribute()
    {
        return $this->getElement()->getEntityAttribute();
    }

    public function getAttributeCode()
    {
        return $this->getAttribute()->getAttributeCode();
    }

    /**
     * @return bool
     */
    public function canDisplayUseDefault()
    {
        if ($attribute = $this->getAttribute()) {
            if (!$this->isScopeGlobal($attribute)
                && $this->getDataObject()
                && $this->getDataObject()->getId()
                && $this->getDataObject()->getStoreId()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function usedDefault()
    {
        $defaultValue = $this->getDataObject()->getAttributeDefaultValue($this->getAttribute()->getAttributeCode());
        return !$defaultValue;
    }

    /**
     * @return $this
     */
    public function checkFieldDisable()
    {
        if ($this->canDisplayUseDefault() && $this->usedDefault()) {
            $this->getElement()->setDisabled(true);
        }
        return $this;
    }


    /**
     * @return string
     */
    public function getScopeLabel()
    {
        $html = '';
        $attribute = $this->getElement()->getEntityAttribute();
        if (!$attribute || Mage::app()->isSingleStoreMode()) {
            return $html;
        }
        if ($this->isScopeGlobal($attribute)) {
            $html .= Mage::helper('gene_bluefoot')->__('[GLOBAL]');
        } elseif ($this->isScopeWebsite($attribute)) {
            $html .= Mage::helper('gene_bluefoot')->__('[WEBSITE]');
        } elseif ($this->isScopeStore($attribute)) {
            $html .= Mage::helper('gene_bluefoot')->__('[STORE VIEW]');
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getElementLabelHtml()
    {
        return $this->getElement()->getLabelHtml();
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getElement()->getElementHtml();
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isScopeGlobal($attribute)
    {
        return $attribute->getIsGlobal() == 1;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isScopeWebsite($attribute)
    {
        return $attribute->getIsGlobal() == 2;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isScopeStore($attribute)
    {
        return !$this->isScopeGlobal($attribute) && !$this->isScopeWebsite($attribute);
    }
}
