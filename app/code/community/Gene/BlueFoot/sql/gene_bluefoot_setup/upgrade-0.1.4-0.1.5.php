<?php
$installer = $this;


/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$entityTable = $installer->getTable('gene_bluefoot/entity');
$appTable = $installer->getTable('gene_bluefoot/content_app');
$termTable = $installer->getTable('gene_bluefoot/taxonomy_term');
$taxonomyTable = $installer->getTable('gene_bluefoot/taxonomy');
$termContentTable = $installer->getTable('gene_bluefoot/taxonomy_term_content');
$typeTable = $installer->getTable('gene_bluefoot/type');

if(!$installer->getConnection()->tableColumnExists($typeTable, 'app_id')){
    $installer->getConnection()
        ->addColumn($typeTable,
            'app_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned'  => true,
                'comment' => 'App ID',
                'after' => 'identifier'
            )
        );

    $installer->getConnection()->addForeignKey(
        $installer->getConnection()->getForeignKeyName($typeTable, 'app_id', $appTable, 'app_id'),
        $typeTable,
        'app_id',
        $appTable,
        'app_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
}

if(!$installer->getConnection()->tableColumnExists($taxonomyTable, 'app_id')){
    $installer->getConnection()
        ->addColumn($taxonomyTable,
            'app_id',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned'  => true,
                'comment' => 'App ID',
                'after' => 'taxonomy_id'
            )
        );


    $installer->getConnection()->addForeignKey(
        $installer->getConnection()->getForeignKeyName($taxonomyTable, 'app_id', $appTable, 'app_id'),
        $taxonomyTable,
        'app_id',
        $appTable,
        'app_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
}


if(!$installer->getConnection()->isTableExists($termContentTable)){
    $installer->createTermContentTable('gene_bluefoot/taxonomy_term_content');
}

if($installer->getConnection()->isTableExists($installer->getTable('gene_bluefoot/content_app_content_type'))){
    $installer->getConnection()->dropTable($installer->getTable('gene_bluefoot/content_app_content_type'));
}

if($installer->getConnection()->isTableExists($installer->getTable('gene_bluefoot/content_app_taxonomy'))){
    $installer->getConnection()->dropTable($installer->getTable('gene_bluefoot/content_app_taxonomy'));
}


$installer->endSetup();