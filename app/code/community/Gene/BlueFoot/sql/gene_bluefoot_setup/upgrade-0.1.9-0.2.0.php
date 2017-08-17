<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$typeTable = $installer->getTable('gene_bluefoot/type');
if(!$installer->getConnection()->tableColumnExists($typeTable, 'preview_field')) {
    $installer->getConnection()
        ->addColumn($typeTable,
            'preview_field',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'The field to be shown as a preview if no preview template is defined',
                'after' => 'url_key_prefix'
            )
        );
}

$installer->endSetup();