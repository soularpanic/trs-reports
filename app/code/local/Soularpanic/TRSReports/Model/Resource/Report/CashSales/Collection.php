<?php
class Soularpanic_TRSReports_Model_Resource_Report_CashSales_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'AdvancedStock/StockMovement';

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');
        $_helper->log("Cash Sales Resource starting");

        $orderAlias = "orders";
        $paymentsAlias = "payments";

        $_select = $this->getSelect();
        $_select
            ->from([ $orderAlias => $this->getTable('sales/order') ],
                [ "order_number" => "increment_id",
                    "customer_name" => "concat_ws(' ', customer_firstname, customer_lastname)",
                    "created_at" => "created_at",
                    "subtotal" => "subtotal",
                    "discount_amount" => "discount_amount",
                    "customer_credit_amount" => "customer_credit_amount",
                    "tax_amount" => "tax_amount",
                    "grand_total" => "grand_total" ])
            ->joinLeft([ $paymentsAlias => $this->getTable('sales/order_payment') ],
                "$orderAlias.entity_id = $paymentsAlias.parent_id",
                [  ])
            ->where("method = 'Money'")
            ->where("status != 'canceled'")
            ->where("created_at between '{$this->_from}' and '{$this->_to}'");

        $_helper->log("\n\nCash Sales SQL:\n\n{$_select->__toString()}");
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