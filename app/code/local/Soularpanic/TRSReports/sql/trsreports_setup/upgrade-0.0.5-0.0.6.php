<?php

$installer = $this;
$installer->startSetup();

$installer->run("
  CREATE TABLE `{$this->getTable('trsreports/product_tree')}` (
    `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO INCREMENT,
    `name` VARCHAR(255) NOT NULL UNIQUE,

  ) ENGINE=INNODB;

  CREATE TABLE `{$table->getTable('trsreports/product_tree_link')}` (
    `entity_id` INT(10) UNSIGNED PRIMARY KEY AUTO INCREMENT,
    `product_id` INT(10) UNSIGNED NOT NULL,
    `tree_id` INT(10) UNSIGNED NOT NULL
  ) ENGINE=INNODB;
");

$installer->endSetup();