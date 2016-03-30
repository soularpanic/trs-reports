<?php
class Soularpanic_TRSReports_Model_Resource_Report_ProductMargins_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'AdvancedStock/StockMovement';

    public function __construct() {
        parent::__construct();
    }

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');
        $_helper->log("Delivery and Value resource starting {$this->_to}");
        $_fromSql = "'".Mage::helper('trsreports/SqlDate')->convertFromDate($this->_from)."'";
        $_toSql = "'".Mage::helper('trsreports/SqlDate')->convertToDate($this->_to)."'";

        $_transactionAlias = 'transactions';
        $_orderAlias = 'orders';
        $_orderItemAlias = 'orderItems';
        $_newestPOAlias = 'newestPO';
        $_poAlias = 'PO';
        $_unitCostAlias = 'unitCost';
        $_orderHistoryAlias = 'history';

        $_newestPOSelect = $_helper->_getNewSelect();
        $_newestPOSelect
            ->from([ $this->getTable('Purchase/OrderProduct') ],
                [ 'product_id' => 'pop_product_id',
                    'purchase_order_id' => 'max(pop_order_num)' ])
            ->group('pop_product_id');

        $_helper->log("\nNewest PO select:\n\n".$_newestPOSelect->__toString());


        $_unitCostSelect = $_helper->_getNewSelect();
        $_unitCostSelect
            ->from([ $_poAlias => $this->getTable('Purchase/OrderProduct') ],
                [ 'product_id' => 'pop_product_id',
                    'unit_price' => 'pop_price_ht'])
            ->joinLeft([ $_newestPOAlias => $_newestPOSelect ],
                "$_poAlias.pop_product_id = $_newestPOAlias.product_id and $_poAlias.pop_order_num = $_newestPOAlias.purchase_order_id",
                [ ])
            ->where("$_newestPOAlias.product_id is not null");

        $_helper->log("\nUnit Cost select:\n\n".$_unitCostSelect->__toString());

        $_select = $this->getSelect();
        if ($this->_curPage == 1) {
            $_select
                ->from([ $_transactionAlias => $this->getTable('sales/payment_transaction') ],
                    [ "txn_id",
                        "txn_date" => "created_at" ])
                ->joinLeft([ $_orderAlias => $this->getTable('sales/order') ],
                    "$_transactionAlias.order_id = $_orderAlias.entity_id",
                    [ "order_id" => 'increment_id' ])
                ->joinLeft([ $_orderItemAlias => $this->getTable('sales/order_item') ],
                    "$_orderAlias.entity_id = $_orderItemAlias.order_id",
                    [ "item_id",
                        "sku",
                        "name",
                        "qty_ordered",
                        "qty_refunded",
                        "shipping_cost" => "($_orderAlias.shipping_amount * $_orderItemAlias.weight * $_orderItemAlias.qty_ordered / $_orderAlias.weight)" ])
                ->joinLeft([ $_orderHistoryAlias => $this->getTable('sales/order_status_history') ],
                    "$_orderAlias.entity_id = $_orderHistoryAlias.parent_id and $_orderHistoryAlias.comment like 'Refunded amount of % online.%'",
                    [ "refund_date" => "created_at" ])
                ->joinLeft([ $_unitCostAlias => $_unitCostSelect ],
                    "$_orderItemAlias.product_id = $_unitCostAlias.product_id",
                    [ "unit_cost" => "ifnull(unit_price, 0)" ])
                ->where("$_orderItemAlias.product_type = 'simple'")
                ->where("$_orderAlias.status != 'canceled'")
                ->where("($_transactionAlias.created_at between $_fromSql and $_toSql or $_orderHistoryAlias.created_at between $_fromSql and $_toSql)");
        }
        if ($this->_pageSize) {
            $_select->limitPage($this->_curPage, $this->_pageSize);
        }

        $_helper->log("\nProduct Margins select:\n\n".$_select->__toString());
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