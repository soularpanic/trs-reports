<?php
class Soularpanic_TRSReports_Model_Resource_Report_DeliveryAndValuePaymentDetail_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'MDNPurchsae/order_payment';

    public function __construct() {
        parent::__construct();
    }

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');
        $_helper->log("Delivery and Value Payment Detail resource starting");
        $_toSql = "DATE_ADD('{$this->_to}', INTERVAL '23:59:59' HOUR_SECOND)";

        $customFilterData = $this->getCustomFilterData();
        $poId = $customFilterData->getPurchaseOrderNumber();

        $_select = $this->getSelect();
        $_select
            ->from([ $this->getTable('MDNPurchase/order_payment') ],
                [ "entity_id",
                    "payment",
                    "created_at",
                    "details" ])
            ->where("purchase_order_id = '$poId'")
            ->where("created_at < $_toSql");

        $_helper->log("\nDelivery and Value Payment Detail select:\n\n{$_select->__toString()}");
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