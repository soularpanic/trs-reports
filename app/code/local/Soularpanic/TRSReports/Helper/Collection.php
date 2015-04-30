<?php
class Soularpanic_TRSReports_Helper_Collection
    extends Soularpanic_TRSReports_Helper_Data {

    const DEFAULT_PRODUCT_LINE_ALIAS = "productLines";
    const DEFAULT_PRODUCT_ORDERS_ALIAS = "productOrders";

    public function getTableAlias($select, $tableName) {
        $tables = $select->getPart('from');
        $map = function($arr) { return $arr['tableName']; };
        $_tables = array_map($map, $tables);
        $alias = array_search($tableName, $_tables);

        return $alias;
    }

    public function getProductLinesSelect($alias = null) {
        $_alias = $alias ?: self::DEFAULT_PRODUCT_LINE_ALIAS;
        $_table = $this->getTable('catalog/product');

        $productNameAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
        $_productNameTable = $productNameAttr->getBackendTable();

        $select = $this->_getNewSelect();
        $select->from([ $_alias => $_table ],
            [ 'product_id' => 'entity_id',
                'product_sku' => 'sku' ])
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '{$productNameAttr->getId()}' and {$_productNameTable}.entity_id = {$_alias}.entity_id",
                [ 'product_name' => "{$_productNameTable}.value" ])
            ->joinLeft([ 'tppl' => $this->getTable('trsreports/product_piece_link') ],
                "$_alias.entity_id = tppl.product_id",
                [ ])
            ->joinLeft([ 'tpp' => $this->getTable('trsreports/product_piece_product') ],
                "tppl.pieced_product_id = tpp.entity_id",
                [ 'piece_id' => 'entity_id',
                    'piece_name' => 'name',
                    'piece_sku' => 'pieced_product_sku' ])
            ->joinLeft([ 'tptn' => $this->getTable('trsreports/product_tree_node') ],
                "$_alias.entity_id = tptn.product_id",
                [ ])
            ->joinLeft([ 'tpt' => $this->getTable('trsreports/product_tree') ],
                "tpt.entity_id = tptn.tree_id",
                [ "tree_id" => "entity_id",
                    "tree_name" => 'name',
                    "tree_sku" => "sku",
                    "line_name" => "(ifnull(tpt.name, ifnull(tpp.name, $_productNameTable.value)))",
                    "line_sku" => "(ifnull(tpt.sku, ifnull(tpp.pieced_product_sku, $_alias.sku)))" ])
            ->group('product_id');
        $this->log("Product Line select:\n".$select->__toString());
        return $select;
    }

    public function getProductOrders($from, $to, $alias = null) {
        $_alias = $alias ?: self::DEFAULT_PRODUCT_ORDERS_ALIAS;

        $select = $this->_getNewSelect();
        $select->from($this->getTable('sales/order_item'),
            [ 'product_id' => 'product_id',
                'total_qty_ordered' => "(sum(qty_ordered))",
                'time_in_days' => "TIMESTAMPDIFF(DAY, '{$from}', '{$to}')" ])
            ->where("created_at between '$from' and '$to'")
            ->group('product_id');

        $this->log("Product Orders select:\n".$select->__toString());
        return $select;
    }

    public function getProductInventory() {
        $this->log("getProductInventory -- start");
        $inventorySelect = $this->_getNewSelect();
        $inventorySelect
            ->from($this->getTable('cataloginventory/stock_item'),
                [ 'product_id',
                    'qty' => '(ifnull(qty, 0))' ]);

        $this->log("inventory select:".$inventorySelect->__toString());

        $suppliersSelect = $this->_getNewSelect();

        $suppliersSelect
            ->from(['pop' => $this->getTable('Purchase/OrderProduct')],
                ['product_id' => 'pop_product_id'])
            ->joinLeft(['po' => $this->getTable('Purchase/Order')],
                "pop.pop_order_num = po.po_num",
                [])
            ->joinLeft(['ps' => $this->getTable('Purchase/Supplier')],
                "po.po_sup_num = ps.sup_id",
                [ 'suppliers' => "(GROUP_CONCAT(DISTINCT ps.sup_name ORDER BY ps.sup_name ASC))"])
            ->group('pop.pop_product_id');

        $this->log("supplier select:".$suppliersSelect->__toString());

        $purchaseOrdersSelect = $this->_getNewSelect();
        $purchaseOrdersSelect
            ->from([ 'pps' => $this->getTable('Purchase/ProductSupplier') ],
                [ 'product_id' => 'pps_product_id' ])
            ->joinLeft([ 'ps' => $this->getTable('Purchase/Supplier') ],
                "ps.sup_id = pps.pps_supplier_num",
                [])
            ->joinLeft( ['pop' => $this->getTable('Purchase/OrderProduct') ],
                "pop.pop_product_id = pps.pps_product_id AND pop.pop_supplied_qty < pop.pop_qty",
                [ 'pop_order_num' => 'pop_order_num',
                    'pop_supplied_qty'    => "ifnull(pop_supplied_qty, 0)",
                    'pop_qty'           => "ifnull(pop_qty, 0)" ])
            ->joinLeft( [ 'po' => $this->getTable('Purchase/Order') ],
                "po.po_num = pop.pop_order_num AND po.po_status in ('waiting_for_delivery')",
                [ 'po_string' => "if(po.po_num is null, null, concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date))" ]);

        $productInventorySelect = $this->_getNewSelect();
        $productInventorySelect
            ->from([ 'inventory' => $inventorySelect ],
                [ 'product_id',
                    'qty' ])
            ->joinLeft([ 'suppliers' => $suppliersSelect ],
                "suppliers.product_id = inventory.product_id",
                [ "pop_order_num",
                    "suppliers" ])
            ->joinLeft([ 'purchase_orders' => $purchaseOrdersSelect ],
                "purchase_orders.product_id = inventory.product_id",
                [ 'pop_supplied_qty',
                    'pop_qty',
                    'po_string' ]);

        $this->log("Product Inventory Select:\n".$productInventorySelect->__toString());

        return $suppliersSelect;
    }

    public function getTable($tableCode) {
        return Mage::getResourceSingleton('catalog/product')->getTable($tableCode);
    }

    public function _getNewSelect() {
        return Mage::getSingleton('core/resource')->getConnection('core_read')->select();
    }
}