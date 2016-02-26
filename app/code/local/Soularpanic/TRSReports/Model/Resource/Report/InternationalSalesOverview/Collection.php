<?php
class Soularpanic_TRSReports_Model_Resource_Report_InternationalSalesOverview_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {
    const TOP_LEVEL_TABLE_ALIAS = 'orders';
    const LOCAL_TAX_RATE = 0.08;
    const REGION = "Georgia";

    protected $_statusWhitelist = [
        'pending',
        'pending_paypal',
        'pending_wu_confirm',
        'pending_tt_confirm',
        'processing',
        'processing_invoiced',
        'processing_shipped',
        'processing_pay_confirmed',
        'processing_pp_review',
        'processing_stock_pack',
        'complete',
        'complete_post',
        'holded',
        'back_order',
        'fraud',
        'payment_review'
    ];

    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = [];
    protected $_defaultSort = 'region';

    protected function _initSelect() {

        $_helper = Mage::helper('trsreports/collection');
        $_select = $this->getSelect();

        $_startDateSql = "CONVERT_TZ('{$this->_from}', '-06:00', '+00:00')";
        $_endDateSql = "CONVERT_TZ(DATE_ADD('{$this->_to}', INTERVAL '23:59:59' HOUR_SECOND), '-06:00', '+00:00')";


        $_internationalSalesSelect = $_helper->_getNewSelect();
        $_internationalRefundsSelect = $_helper->_getNewSelect();

        $_internationalAlias = self::TOP_LEVEL_TABLE_ALIAS;
        $_orderAlias = 'orders';
        $_addressAlias = 'addresses';
        $_creditAlias = 'refunds';
        $_paymentAlias = 'payments';
        $_countryAlias = 'countries';

        $_internationalRefundsSelect
            ->from($this->getTable('sales/creditmemo'),
                [ 'order_id' => 'order_id',
                    'refund_value' => 'sum(grand_total)',
                    'created_at' => 'created_at' ])
            ->where("created_at between $_startDateSql and $_endDateSql")
            ->group("order_id");
        $this->log("Intl Refunds Select:\n".$_internationalRefundsSelect->__toString());

        $_internationalSalesSelect
            ->from([ $_orderAlias => $this->getTable('sales/order') ],
                [ "entity_id" => "$_orderAlias.entity_id",
                    'order_count' => "count(distinct $_orderAlias.entity_id)",
                    'sold_value' => "sum(if($_orderAlias.created_at between $_startDateSql and $_endDateSql, grand_total, 0))",
                    'credit_value' => "sum(if($_orderAlias.created_at between $_startDateSql and $_endDateSql, ifnull(customer_credit_amount, 0), 0))",
                    'total_value' => "sum(grand_total) + sum(ifnull(customer_credit_amount, 0))",
                    'order_numbers' => "group_concat(distinct concat($_orderAlias.entity_id, ':', $_orderAlias.increment_id))" ])
            ->join([ $_paymentAlias => $this->getTable('sales/order_payment') ],
                "$_orderAlias.entity_id = $_paymentAlias.parent_id ",
                [ 'cash_value' => "sum(if($_paymentAlias.method = 'Money', $_orderAlias.grand_total, 0))" ])
            ->joinLeft([ $_creditAlias => $_internationalRefundsSelect ],
                "$_orderAlias.entity_id = $_creditAlias.order_id",
                [ "refund_value" => "sum(ifnull(refund_value, 0))" ])
            ->joinLeft([ $_addressAlias => $this->getTable('sales/order_address') ],
                "$_orderAlias.entity_id = $_addressAlias.parent_id",
                [ "country_id" ])
            // Use CST since that is what Braintree uses for its reports
            ->where("$_orderAlias.created_at between $_startDateSql and $_endDateSql or $_creditAlias.created_at between $_startDateSql and $_endDateSql")
            ->where("(($_orderAlias.state is null or $_orderAlias.state in ('complete', 'closed')) and ($_orderAlias.status is null or $_orderAlias.status in ('complete', 'closed')) or $_orderAlias.status = 'processing_invoiced')")
            ->where("$_addressAlias.address_type = 'shipping'")
            ->group("$_addressAlias.country_id");

        $_select->from([ $_internationalAlias => $_internationalSalesSelect ],
            [ "order_count",
                "sold_value",
                "cash_value",
                "credit_value",
                "refund_value",
                "total_value",
                "order_numbers" ])
            ->joinLeft([ $_countryAlias => $this->getTable('directory/country') ],
                "$_countryAlias.country_id = $_internationalAlias.country_id",
                [ 'region' => 'name' ]);


        $this->log("International Sales SQL:\n".$this->getSelect()->__toString());
    }

    protected function _applyCustomFilter() {
        $_field = $this->_sort === null ? $this->_defaultSort : $this->_sort;
        $_dir = $this->_sortDir ? $this->_sortDir : 'ASC';
        if ($_field !== null) {
            $this->getSelect()->order(array("{$_field} {$_dir}"));
        }
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter() {
        return $this;
    }


}