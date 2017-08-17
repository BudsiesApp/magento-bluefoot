<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

if(!$installer->getConnection()->isTableExists($this->getTable('gene_bluefoot/stage_template'))){
    $templatesTable = $installer->getConnection()->newTable($this->getTable('gene_bluefoot/stage_template'))
        ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned' => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Template ID')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Name of template')
        ->addColumn('structure', Varien_Db_Ddl_Table::TYPE_TEXT, false, array(
            'nullable' => false
        ), 'The JSON page structure')
        ->addColumn('has_data', Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
            'nullable' => true
        ), 'Does this template contain data?')
        ->addColumn('preview', Varien_Db_Ddl_Table::TYPE_TEXT, false, array(
            'nullable' => true
        ), 'A preview of the page builder structure')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
        ), 'Updated At');

// Actually create the table
    $installer->getConnection()->createTable($templatesTable);
}


$installer->endSetup();