<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form_Renderer_Group
 *
 * @author Dave Macaulay
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Block_Edit_Tab_Form_Renderer_Group extends Varien_Data_Form_Element_Select
{
    /**
     * Get the elements HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('select');
        $html = '<select style="width: 194px;" id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = array($value);
        }

        if ($values = $this->getValues()) {
            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html.= $this->_optionToHtml(array(
                        'value' => $key,
                        'label' => $option),
                        $value
                    );
                }
                elseif (is_array($option['value'])) {
                    $html.='<optgroup label="'.$option['label'].'">'."\n";
                    foreach ($option['value'] as $groupItem) {
                        $html.= $this->_optionToHtml($groupItem, $value);
                    }
                    $html.='</optgroup>'."\n";
                }
                else {
                    $html.= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html.= '</select>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Add an 'Add Group' button
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        $html .= '<button style="width: 82px;" type="button" name="new-group">' . Mage::helper('gene_bluefoot')->__('Add Group') . '</button>';
        $html .= '<div class="group-form" style="margin-top: 12px;display: none;">' . Mage::app()->getLayout()->createBlock('gene_bluefoot/adminhtml_setup_block_edit_tab_form_group_form')->toHtml() . '</div>';
        $html .= '<script type="text/javascript">
            document.observe("dom:loaded", function() {
                $$(".group-form input").each(function (element) {
                    element.setAttribute("disabled", "disabled");
                });
                Element.observe($$("[name=\'new-group\']").first(), "click", function (event) {
                    var groupButton = Event.element(event);
                    var groupForm = groupButton.next();
                    if (groupForm.visible()) {
                        groupForm.hide();
                        $$(".group-form input").each(function (element) {
                            element.setAttribute("disabled", "disabled");
                        });
                    } else {
                        groupForm.show();
                        groupForm.select("input").each(function (element) {
                            element.removeAttribute("disabled");
                        });

                        // Retrieve the submit button
                        var submitButton = groupButton.next().select("input[type=\'button\']").first();
                        Element.stopObserving(submitButton);
                        Element.observe(submitButton, "click", function (event) {
                            var data = Form.serialize(groupButton.next(), true);
                            new Ajax.Request(\'' . Mage::helper('adminhtml')->getUrl('*/*/createGroup') . '\', {
                                method: \'get\',
                                parameters: data,
                                onSuccess: function (response) {
                                    var json = response.responseJSON;
                                    if (json) {
                                        if (json.success == true) {
                                            var option = new Element(\'option\', {value: json.id}).update(data.name);
                                            var select = $(\'' . $this->getHtmlId() . '\');
                                            select.insert({
                                                bottom: option
                                            });
                                            select.setValue(json.id);
                                            groupForm.hide();
                                            groupForm.select("input[type=\'text\']").each(function (element) {
                                                element.setValue("");
                                                element.setAttribute("disabled", "disabled");
                                            });
                                        } else if (json.message) {
                                            alert(json.message);
                                        } else {
                                            alert(\''. Mage::helper('gene_bluefoot')->__('An issue has occured whilst trying to create the group.') . '\');
                                        }
                                    }
                                },
                                onFailure: function () {
                                    alert(\''. Mage::helper('gene_bluefoot')->__('An issue has occured whilst trying to create the group.') . '\');
                                }
                            });
                        });
                    }
                });
            });
        </script>';
        return $html;
    }

}