<?php
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$installer->startSetup();

$installer->addAttribute(
    'catalog_product',
    'is_archived',
    [
        'backend' => 'catalog/product_attribute_backend_boolean',
        'type' => 'varchar',
        'input' => 'select',
        'label' => 'Archived',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => 0
    ]);

$installer->endSetup();