<?php
/**
 * Product Tree v1/DPOT/Product Pieces are migrated to their new home in the Product Piece tables.
 */
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$this->getTable('trsreports/product_piece_product')}` (
      `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL UNIQUE,
      `pieced_product_sku` VARCHAR(255) NOT NULL
    ) ENGINE=INNODB;

    CREATE TABLE `{$this->getTable('trsreports/product_piece_link')}` (
      `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      `product_id` INT(10) UNSIGNED NOT NULL,
      `pieced_product_id` INT(10) UNSIGNED NOT NULL,
      FOREIGN KEY (product_id)
        REFERENCES {$installer->getTable('catalog/product')}(entity_id)
        ON DELETE CASCADE,
      FOREIGN KEY (pieced_product_id)
        REFERENCES {$this->getTable('trsreports/product_piece_product')}(entity_id)
        ON DELETE CASCADE
    ) ENGINE=INNODB;

    INSERT INTO `{$this->getTable('trsreports/product_piece_product')}` (
        `entity_id`, `name`, `pieced_product_sku`
      )
      SELECT
        `entity_id`, `name`, `line_sku`
          FROM `{$this->getTable('trsreports/product_piece_product_deprecated')}`;

    INSERT INTO `{$this->getTable('trsreports/product_piece_link')}` (
        `entity_id`, `product_id`, `pieced_product_id`
      )
      SELECT
        `entity_id`, `product_id`, `line_id`
          FROM `{$this->getTable('trsreports/product_piece_link_deprecated')}`;

    DROP TABLE `{$this->getTable('trsreports/product_piece_link_deprecated')}`;
    DROP TABLE `{$this->getTable('trsreports/product_piece_product_deprecated')}`;
");

$installer->endSetup();