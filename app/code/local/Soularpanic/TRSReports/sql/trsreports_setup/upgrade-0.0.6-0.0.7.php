<?php
/**
 * SKUs added to product line tables
 */
$installer = $this;
$installer->startSetup();

$installer->run("
  ALTER TABLE `{$installer->getTable('trsreports/product_tree')}`
    ADD COLUMN `sku` VARCHAR(255) NOT NULL;
");

$installer->endSetup();