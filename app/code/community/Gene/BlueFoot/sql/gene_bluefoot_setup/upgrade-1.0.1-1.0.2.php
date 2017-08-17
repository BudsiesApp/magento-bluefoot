<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$appTable = $installer->getTable('gene_bluefoot/content_app');
$entityTable = $installer->getTable('gene_bluefoot/entity');
$termTable = $installer->getTable('gene_bluefoot/taxonomy_term');
$taxonomyTable = $installer->getTable('gene_bluefoot/taxonomy');
$typeTable = $installer->getTable('gene_bluefoot/type');
$eavAttributeTable = $installer->getTable('eav/attribute_set');


$installer->getConnection()->addForeignKey(
    $installer->getConnection()->getForeignKeyName($termTable, 'taxonomy_id', $taxonomyTable, 'taxonomy_id'),
    $termTable,
    'taxonomy_id',
    $taxonomyTable,
    'taxonomy_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->getConnection()->addForeignKey(
    $installer->getConnection()->getForeignKeyName($typeTable, 'attribute_set_id', $eavAttributeTable, 'attribute_set_id'),
    $typeTable,
    'attribute_set_id',
    $eavAttributeTable,
    'attribute_set_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->getConnection()->addForeignKey(
    $installer->getConnection()->getForeignKeyName($entityTable, 'attribute_set_id', $eavAttributeTable, 'attribute_set_id'),
    $entityTable,
    'attribute_set_id',
    $eavAttributeTable,
    'attribute_set_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);


$installer->endSetup();