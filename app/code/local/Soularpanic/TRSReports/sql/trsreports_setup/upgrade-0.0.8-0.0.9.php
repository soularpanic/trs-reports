<?php
$installer = $this;
$installer->startSetup();

$orderItemTable = $installer->getTable('sales/order_item');
$indexList = $installer->getConnection()->getIndexList($orderItemTable);

$productIdColumn = 'product_id';
$createdAtColumn = 'created_at';
$qtyOrderedColumn = 'qty_ordered';
$createdAtIndex = $installer->getIdxName($orderItemTable, [$productIdColumn, $createdAtColumn, $qtyOrderedColumn]);
if (!isset($indexList[$createdAtIndex])) {
    $installer->run("
      CREATE INDEX $createdAtIndex
      ON `{$orderItemTable}` ($productIdColumn, $createdAtColumn, $qtyOrderedColumn);
    ");
}

$installer->endSetup();