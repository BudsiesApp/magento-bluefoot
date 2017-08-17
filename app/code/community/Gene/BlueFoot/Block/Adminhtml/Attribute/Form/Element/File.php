<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_File
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_File extends Varien_Data_Form_Element_File
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media') . $this->getValue();


        }
        return $url;
    }

    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . $url;
            }

            $html = '<a href="' . $url . '" target="_new">'
                . $url
                . '</a><br/>';

        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->_getDeleteCheckbox();

        return $html;
    }


    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('core')->__('Delete File');
            $html .= '<br/><span class="">';
            $html .= '<input type="checkbox"'
                . ' name="' . parent::getName() . '[delete]" value="1" class="checkbox"'
                . ' id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"': '')
                . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"'
                . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
}