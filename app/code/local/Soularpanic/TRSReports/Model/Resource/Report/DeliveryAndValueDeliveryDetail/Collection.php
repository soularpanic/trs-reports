<?php
class Soularpanic_TRSReports_Model_Resource_Report_DeliveryAndValueDeliveryDetail_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'AdvancedStock/StockMovement';

    public function __construct() {
        parent::__construct();
    }

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');
        $_helper->log("Delivery and Value Delivery Detail resource starting");
        $_toSql = "DATE_ADD('{$this->_to}', INTERVAL '23:59:59' HOUR_SECOND)";

        $customFilterData = $this->getCustomFilterData();
        $poId = $customFilterData->getPurchaseOrderNumber();

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
            ->where("sm_po_num = '$poId'")
            ->group("sm_product_id");


        $_helper->log("\nDelivery and Value Detail total delivered select:\n\n{$_totalDeliveredSelect->__toString()}");


//        $_lineItemsSelect = $_helper->_getNewSelect();
//        $_lineItemsSelect
        $_select = $this->getSelect();
        $_select
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
                "$_stockMovementAlias.sm_product_id = $_totalDeliveredAlias.product_id",
                [ 'total_delivered_qty' => 'delivered_qty',
                    'remaining_value' => "($_purchaseOrderProductAlias.pop_qty - delivered_qty) * $_purchaseOrderProductAlias.pop_price_ht" ])
            ->where("sm_po_num = '$poId'")
            ->where("sm_date < $_toSql");

        $_helper->log("\nDelivery and Value Detail line items select:\n\n{$_select->__toString()}");
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