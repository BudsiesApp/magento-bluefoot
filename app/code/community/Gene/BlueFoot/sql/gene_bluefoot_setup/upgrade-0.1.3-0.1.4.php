<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

if($installer->getConnection()->isTableExists('gene_cms_app')) {

    // Rename all tables from gene_cms* to gene_bluefoot*
    $installer->run('RENAME TABLE
    gene_cms_app TO gene_bluefoot_app,
    gene_cms_app_content_type TO gene_bluefoot_app_content_type,
    gene_cms_app_taxonomy TO gene_bluefoot_app_taxonomy,
    gene_cms_eav_attribute TO gene_bluefoot_eav_attribute,
    gene_bluefoot_entity TO gene_bluefoot_entity,
    gene_bluefoot_entity_char TO gene_bluefoot_entity_char,
    gene_bluefoot_entity_datetime TO gene_bluefoot_entity_datetime,
    gene_bluefoot_entity_decimal TO gene_bluefoot_entity_decimal,
    gene_bluefoot_entity_int TO gene_bluefoot_entity_int,
    gene_bluefoot_entity_text TO gene_bluefoot_entity_text,
    gene_bluefoot_entity_type TO gene_bluefoot_entity_type,
    gene_bluefoot_entity_varchar TO gene_bluefoot_entity_varchar,
    gene_cms_install TO gene_bluefoot_install,
    gene_cms_taxonomy TO gene_bluefoot_taxonomy,
    gene_cms_taxonomy_term TO gene_bluefoot_taxonomy_term,
    gene_cms_taxonomy_term_char TO gene_bluefoot_taxonomy_term_char,
    gene_cms_taxonomy_term_datetime TO gene_bluefoot_taxonomy_term_datetime,
    gene_cms_taxonomy_term_decimal TO gene_bluefoot_taxonomy_term_decimal,
    gene_cms_taxonomy_term_int TO gene_bluefoot_taxonomy_term_int,
    gene_cms_taxonomy_term_text TO gene_bluefoot_taxonomy_term_text,
    gene_cms_taxonomy_term_varchar TO gene_bluefoot_taxonomy_term_varchar,
    gene_cms_url_rewrite TO gene_bluefoot_url_rewrite,
    gene_cms_stage_template TO gene_bluefoot_stage_template;');

}


$installer->endSetup();