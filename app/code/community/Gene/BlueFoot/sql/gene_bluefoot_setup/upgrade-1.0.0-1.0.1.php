<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$stageTemplateTable = $installer->getTable('gene_bluefoot/stage_template');

if(!$installer->getConnection()->tableColumnExists($stageTemplateTable, 'pinned')){
    $installer->getConnection()
        ->addColumn($stageTemplateTable,
            'pinned',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => false,
                'comment' => 'Whether the template is pinned/favourited'
            )
        );

}
$installer->endSetup();