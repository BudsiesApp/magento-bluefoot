<?php
$installer = $this;


/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$tableName = $installer->getTable('gene_bluefoot/url_rewrite');
$appTable = $installer->getTable('gene_bluefoot/content_app');
$entityTable = $installer->getTable('gene_bluefoot/entity');
$termTable = $installer->getTable('gene_bluefoot/taxonomy_term');

if(!$installer->getConnection()->isTableExists($tableName)){
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('url_rewrite_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Rewrite Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default' => 0,
        ), 'Store Id')
        ->addColumn('id_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
        ), 'Id Path')
        ->addColumn('request_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
        ), 'Request Path')
        ->addColumn('target_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
        ), 'Target Path')
        ->addColumn('options', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
        ), 'Options')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
        ), 'Description')

        ->addColumn('app_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable'  => true,
        ), 'App Id')
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Entity Id')
        ->addColumn('taxonomy_term_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable'  => true,
        ), 'Taxonomy Term Id')

        ->addIndex(
            $installer->getIdxName($tableName, array('request_path', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
            array('request_path', 'store_id'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )

        ->addIndex(
            $installer->getIdxName($tableName, array('target_path', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
            array('target_path', 'store_id'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
        )

        ->addForeignKey($installer->getFkName($tableName, 'app_id', $appTable, 'app_id'),
            'app_id', $appTable, 'app_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->addForeignKey($installer->getFkName($tableName, 'entity_id', $entityTable, 'entity_id'),
            'entity_id', $entityTable, 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->addForeignKey($installer->getFkName($tableName, 'taxonomy_term_id', $termTable, 'entity_id'),
            'taxonomy_term_id', $termTable, 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ;

    $installer->getConnection()->createTable($table);
}



$installer->endSetup();