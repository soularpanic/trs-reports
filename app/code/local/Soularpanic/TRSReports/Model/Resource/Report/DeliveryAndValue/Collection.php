<?php
class Soularpanic_TRSReports_Model_Resource_Report_DeliveryAndValue_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'AdvancedStock/StockMovement';

    public function __construct() {
        parent::__construct();
    }

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');
        $_helper->log("Delivery and Value resource starting {$this->_from} - {$this->_to}");
        $_fromSql = "'{$this->_from}'";
        $_toSql = "DATE_ADD('{$this->_to}', INTERVAL '23:59:59' HOUR_SECOND)";

        $_stockMovementAlias = 'stock_movements';
        $_purchaseOrderAlias = 'purchase_orders';
        $_purchaseOrderProductAlias = 'purchase_order_products';
        $_totalDeliveredAlias = 'total_delivered';

        $_totalDeliveredSelect = $_helper->_getNewSelect();
        $_totalDeliveredSelect
            ->from([ $this->getTable('AdvancedStock/StockMovement') ],
                [ 'product_id' => 'sm_product_id',
                    'purchase_order_id' => 'sm_po_num',
                    'delivered_qty' => 'sum(sm_qty)' ])
            ->where("sm_date < $_toSql")
            ->where("sm_po_num is not null")
            ->group('sm_po_num')
            ->group("sm_product_id");


        $_helper->log("\nDelivery and Value total delivered select:\n\n{$_totalDeliveredSelect->__toString()}");

        $_totalRemainingSelect = $_helper->_getNewSelect();
        $_totalRemainingSelect
            ->from([ $this->getTable('Purchase/OrderProduct') ],
                [ "purchase_order_id" => "pop_order_num",
                    "product_id" => "pop_product_id",
                    "ordered_qty" => "pop_qty",
                    "unit_price" => "pop_price_ht" ])
            ->joinLeft([ $_totalDeliveredAlias => $_totalDeliveredSelect ],
                "pop_order_num = $_totalDeliveredAlias.purchase_order_id and pop_product_id = $_totalDeliveredAlias.product_id",
                [ "delivered_qty" => "ifnull(delivered_qty, 0)" ]);

        $_helper->log("\nDelivery and Value total remaining select:\n\n{$_totalRemainingSelect->__toString()}");

        $_totalRemainingByPurchaseOrderSelect = $_helper->_getNewSelect();
        $_totalRemainingByPurchaseOrderSelect
            ->from($_totalRemainingSelect,
                [ "purchase_order_id",
                    "remaining_value" => "sum(if(ordered_qty - delivered_qty < 0, 0, (ordered_qty - delivered_qty) * unit_price))" ])
            ->group("purchase_order_id");

        $_helper->log("\nDelivery and Value total remaining by PO select:\n\n{$_totalRemainingByPurchaseOrderSelect->__toString()}");



        $_lineItemsSelect = $_helper->_getNewSelect();
        $_lineItemsSelect
            ->from([ $_stockMovementAlias => $this->getTable('AdvancedStock/StockMovement') ],
                [ "supplied_date" => "sm_date",
                    "supplied_qty" => "sm_qty" ])
            ->join([ $_purchaseOrderAlias => $this->getTable('Purchase/Order') ],
                "$_stockMovementAlias.sm_po_num = $_purchaseOrderAlias.po_num",
                [ "purchase_order_name" => "po_order_id",
                    "purchase_order_id" => "po_num"])
            ->joinLeft([ $_purchaseOrderProductAlias => $this->getTable('Purchase/OrderProduct') ],
                "$_stockMovementAlias.sm_po_num = $_purchaseOrderProductAlias.pop_order_num and $_stockMovementAlias.sm_product_id = $_purchaseOrderProductAlias.pop_product_id",
                [ "product_name" => "pop_product_name",
                    "product_id" => "pop_product_id",
                    "ordered_qty" => "pop_qty",
                    "unit_price" => "pop_price_ht",
                    "supplied_value" => "(pop_price_ht * sm_qty)"])
            ->joinLeft([ $_totalDeliveredAlias => $_totalDeliveredSelect ],
                "$_stockMovementAlias.sm_product_id = $_totalDeliveredAlias.product_id and $_stockMovementAlias.sm_po_num = $_totalDeliveredAlias.purchase_order_id",
                [ 'total_delivered_qty' => 'delivered_qty'
//                    'remaining_value' => "($_purchaseOrderProductAlias.pop_qty - delivered_qty) * $_purchaseOrderProductAlias.pop_price_ht" ])
                ])
            ->where("sm_po_num is not null")
//            ->where("sm_date < $_toSql");
            ->where("sm_date between $_fromSql and $_toSql");

        $_helper->log("\nDelivery and Value line items select:\n\n{$_lineItemsSelect->__toString()}");

        $_lineItemsAlias = "line_items";

        $_subSelect = $_helper->_getNewSelect();
        $_subSelect
            ->from([ $_lineItemsAlias => $_lineItemsSelect ],
                [ "supplied_dates" => "group_concat(distinct(supplied_date))",
                    "total_supplied_qty" => "sum(supplied_qty)",
                    "purchase_order_name",
                    "purchase_order_id",
                    "product_name",
                    "product_id",
                    "ordered_qty",
                    "unit_price",
                    "total_supplied_value" => "sum(supplied_value)"
//                    "remaining_delivery_value" => "remaining_value"])
        ])
            ->group("purchase_order_id")
            ->group("product_id");

        $_helper->log("\n\nAggregated Line Items SQL:\n\n{$_subSelect->__toString()}");

        $_subSelectAlias = 'alias';
        $_totalRemainingByPurchaseOrderSelectAlias = 'totalRemainingByPO';
        $_select = $this->getSelect();
        $_select->from([ $_subSelectAlias => $_subSelect ],
            [ "supplied_dates" => "concat_ws(',', supplied_dates)",
                "total_supplied_qty" => "sum(total_supplied_qty)",
                "purchase_order_name",
                "purchase_order_id",
                "total_delivery_value" => "sum(total_supplied_value)" ])
//                "balance_remaining" => "sum(remaining_delivery_value)" ])
            ->joinLeft([ $_totalRemainingByPurchaseOrderSelectAlias => $_totalRemainingByPurchaseOrderSelect ],
                "$_subSelectAlias.purchase_order_id = $_totalRemainingByPurchaseOrderSelectAlias.purchase_order_id",
                [ "remaining_value" ])
            ->group("purchase_order_id");


        $_helper->log("\nDelivery and Value SQL:\n\n{$_select->__toString()}");
    }

    protected function _applyTrsExclusions($reportCode, $productTable = null) {
        return $this;
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter() {
        return $this;
    }

}