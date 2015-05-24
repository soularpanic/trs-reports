<?php
$installer = $this;
$installer->startSetup();

$installer->run("
   CREATE TABLE `{$installer->getTable('trsreports/daily_metric')}` (
      `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
      `product_id` INT(10) UNSIGNED NOT NULL,
      `yesterday_end_of_day_inventory` INT,
      `today_start_of_day_inventory` INT,
      `average_rate` FLOAT,
      `average_rate_weight` INT,
      FOREIGN KEY (product_id)
        REFERENCES {$installer->getTable('catalog/product')}(entity_id)
        ON DELETE CASCADE
    ) ENGINE=INNODB;
");

$installer->endSetup();