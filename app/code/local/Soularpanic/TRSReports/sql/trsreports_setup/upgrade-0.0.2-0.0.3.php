<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$this->getTable('trsreports/product_piece_product_deprecated')}` (
      `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL UNIQUE,
      `line_sku` VARCHAR(255) NOT NULL
    ) ENGINE=INNODB;

    CREATE TABLE `{$this->getTable('trsreports/product_piece_link_deprecated')}` (
      `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      `product_id` INT(10) UNSIGNED NOT NULL,
      `line_id` INT(10) UNSIGNED NOT NULL,
      FOREIGN KEY (product_id)
        REFERENCES {$installer->getTable('catalog/product')}(entity_id)
        ON DELETE CASCADE,
      FOREIGN KEY (line_id)
        REFERENCES {$this->getTable('trsreports/product_piece_product_deprecated')}(entity_id)
        ON DELETE CASCADE
    ) ENGINE=INNODB;
");

$installer->endSetup();