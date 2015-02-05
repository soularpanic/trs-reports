<?php
$installer = $this;
$installer->startSetup();

$orderItemTable = $installer->getTable('sales/order_item');
$indexList = $installer->getConnection()->getIndexList($orderItemTable);

$createdAtColumn = 'created_at';
$createdAtIndex = $installer->getIdxName($orderItemTable, $createdAtColumn);
if (!isset($indexList[$createdAtIndex])) {
    $installer->run("
      CREATE INDEX $createdAtIndex
      ON `{$orderItemTable}` ($createdAtColumn);
    ");
}

$productIdColumn = 'product_id';
$productIdIndex = $installer->getIdxName($orderItemTable, $productIdColumn);
if (!isset($indexList[$productIdIdx])) {
    $installer->run("
        CREATE INDEX $productIdIndex
        ON `{$orderItemTable}` ($productIdColumn);
    ");
}
$installer->endSetup();