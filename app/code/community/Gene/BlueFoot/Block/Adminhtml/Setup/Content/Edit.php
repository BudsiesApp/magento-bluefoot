<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "type_id";
        $this->_blockGroup = "gene_bluefoot";
        $this->_controller = "adminhtml_setup_content";
        
        $this->_updateButton("save", "label", Mage::helper("gene_bluefoot")->__("Save"));
        $this->_updateButton("delete", "label", Mage::helper("gene_bluefoot")->__("Delete"));

        $this->_updateButton('save', 'onclick', 'saveNormal();');

        $this->_addButton("saveandcontinue", array(
            "label" => Mage::helper("gene_bluefoot")->__("Save and Continue Edit"),
            "onclick" => "saveAndContinueEdit()",
            "class" => "save",
        ), -100);

        $appId = Mage::registry("type_data")->getContentApp()->getId();

        $this->_formScripts[] = "
            function setJsonField(){

                if(typeof TreePanels == 'undefined'){
                    return true;
                }

                TreePanels.rebuildTrees();
                var _validator = new Validation('set_prop_form', {onSubmit:false});
                if( !_validator.validate() ) {
                    return false;
                }
                var req = Ext.util.JSON.encode(editSet.req);
                $('sets_json').setValue(req);

                return true;
            }

            function saveNormal(){
                try{
                    if(setJsonField()){
                        editForm.submit();
                    }else{
                        return false;
                    }

                }catch(e){
                    alert('Error saving attributes. Error: ' + e.message);
                }
            }


            function saveAndContinueEdit(){
                try{
                    if(setJsonField()){
                        editForm.submit($('edit_form').action+'back/edit/');
                    }else{
                        return false;
                    }

                }catch(e){
                    alert('Error saving attributes. Error: ' + e.message);
                }
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry("type_data") && Mage::registry("type_data")->getId()) {
            return Mage::helper("gene_bluefoot")->__("Edit Content Type: '%s'", $this->htmlEscape(Mage::registry("type_data")->getName() . ' [' . Mage::registry("type_data")->getIdentifier() . ']'));
        } else {
            return Mage::helper("gene_bluefoot")->__("Add Content Type");
        }
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $id = Mage::registry("type_data")->getContentApp()->getId();

        if ($id) {
            return $this->getUrl('/genecms_setup_app/edit/id/' . $id, array('continue' => true, 'tab' => 'content_types'));
        }
        return $this->getUrl('*/*/*');
    }

}