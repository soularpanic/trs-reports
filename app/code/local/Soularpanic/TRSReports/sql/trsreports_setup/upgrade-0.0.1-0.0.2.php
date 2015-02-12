<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('trsreports/excludedproduct')}` (
      `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      `report_id` INT(10) UNSIGNED NOT NULL,
      `product_id` INT(10) UNSIGNED NOT NULL,
      FOREIGN KEY (product_id)
        REFERENCES {$installer->getTable('catalog/product')}(entity_id)
        ON DELETE CASCADE
    ) ENGINE=INNODB;
");

$installer->endSetup();