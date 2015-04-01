<?php
/**
 * Product Tree v2 tables are created
 */
$installer = $this;
$installer->startSetup();

$installer->run("
  CREATE TABLE `{$installer->getTable('trsreports/product_tree')}` (
    `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) UNIQUE NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=INNODB;

  CREATE TABLE `{$installer->getTable('trsreports/product_tree_link')}` (
    `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `product_id` INT(10) UNSIGNED NOT NULL,
    `tree_id` INT(10) UNSIGNED NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id)
      REFERENCES {$installer->getTable('catalog/product')}(entity_id)
      ON DELETE CASCADE,
    FOREIGN KEY (tree_id)
      REFERENCES {$installer->getTable('trsreports/product_tree')}(entity_id)
      ON DELETE CASCADE
  ) ENGINE=INNODB;
");

$installer->endSetup();