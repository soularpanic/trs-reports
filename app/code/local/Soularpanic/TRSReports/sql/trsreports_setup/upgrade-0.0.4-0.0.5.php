<?php
/**
 * Create archived attribute for products.  Archived products do not show up in almost all
 * admin-side product grids in order to reduce clutter.
 */
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