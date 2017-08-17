<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$attributeSetId = Mage::getModel('gene_bluefoot/taxonomy_term')->getDefaultAttributeSet()->getId();

//first check if attribute exists
//Design: Show Description
$attr = Mage::getResourceModel('gene_bluefoot/eav_attribute')
    ->loadByCode(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'show_description');

if(!$attr->getId()){
    $installer->addAttribute(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'show_description', array(
        'type'                       => 'int',
        'label'                      => 'Show Description',
        'required'                   => false,
        'input'                      => 'select',
        'source'                     => 'eav/entity_attribute_source_boolean',
        'backend'                    => '',
        'sort_order'                 => 20,
        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'user_defined'               => 0,
        'group'                      => ''
    ));

    $installer->addAttributeToSet(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, $attributeSetId, 'Design', 'show_description', 20);
}


//Design: Display Mode
$attr = Mage::getResourceModel('gene_bluefoot/eav_attribute')
    ->loadByCode(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'display_mode');

if(!$attr->getId()){
    $installer->addAttribute(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'display_mode', array(
        'type'                       => 'varchar',
        'label'                      => 'Display Mode',
        'required'                   => false,
        'input'                      => 'select',
        'source'                     => 'gene_bluefoot/taxonomy_term_attribute_source_displaymode',
        'backend'                    => '',
        'sort_order'                 => 30,
        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'user_defined'               => 0,
        'group'                      => ''
    ));

    $installer->addAttributeToSet(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, $attributeSetId, 'Design', 'display_mode', 30);
}


//Column Type
$attr = Mage::getResourceModel('gene_bluefoot/eav_attribute')
    ->loadByCode(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'column_type');

if(!$attr->getId()){
    $installer->addAttribute(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'column_type', array(
        'type'                       => 'varchar',
        'label'                      => 'Column Type',
        'required'                   => false,
        'input'                      => 'select',
        'source'                     => 'gene_bluefoot/taxonomy_term_attribute_source_columntype',
        'backend'                    => '',
        'sort_order'                 => 40,
        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'user_defined'               => 0,
        'group'                      => ''
    ));

    $installer->addAttributeToSet(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, $attributeSetId, 'Design', 'column_type', 40);
}


$installer->endSetup();