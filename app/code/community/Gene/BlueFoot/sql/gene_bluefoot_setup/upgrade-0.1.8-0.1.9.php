<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$groupTable = $installer->getTable('gene_bluefoot/type_group');

if(!$installer->getConnection()->tableColumnExists($groupTable, 'sort_order')) {
    $installer->getConnection()
        ->addColumn($groupTable,
            'sort_order',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => '0',
                'unsigned' => true,
                'comment' => 'Sort Order'
            )
        );
}

$typeTable = $installer->getTable('gene_bluefoot/type');

if(!$installer->getConnection()->tableColumnExists($typeTable, 'sort_order')) {
    $installer->getConnection()
        ->addColumn($typeTable,
            'sort_order',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => '0',
                'unsigned' => true,
                'comment' => 'Sort Order'
            )
        );
}

$installer->endSetup();