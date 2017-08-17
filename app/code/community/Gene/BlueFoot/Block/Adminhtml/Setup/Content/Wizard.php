<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Content_Wizard extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "type_id";
        $this->_blockGroup = "gene_bluefoot";
        $this->_controller = "adminhtml_setup_content";
        $this->_mode = 'wizard';
        
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('back');

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'appWizard.reloadStep();',
            'class'     => 'back',
        ), -1);

        $this->_addButton("savecontenttype", array(
            "label" => Mage::helper("gene_bluefoot")->__("Save New Content Type"),
            "onclick" => "saveContentType()",
            "class" => "save",
        ), -100);

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


            function saveContentType(){
                try{
                    if(setJsonField()){
                        //content_type_form.submit($('edit_form').action+'back/edit/');
                        typeWizard.save('content_type_form');
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
        return Mage::helper("gene_bluefoot")->__("Add Content Type");
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array('continue' => true));
    }

}