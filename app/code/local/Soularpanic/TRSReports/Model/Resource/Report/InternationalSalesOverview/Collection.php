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

        $_internationalSelect = $_helper->_getNewSelect();

        $_internationalAlias = self::TOP_LEVEL_TABLE_ALIAS;
        $_orderAlias = 'orders';
        $_addressAlias = 'addresses';
        $_countryAlias = 'countries';
        $_internationalSelect
            ->from([ $_orderAlias => $this->getTable('sales/order') ],
                [ 'entity_id',
                    'order_count' => "count(distinct $_orderAlias.entity_id)",
                    'sold_value' => "sum(grand_total) - sum(ifnull(total_refunded, 0))",
                    'credit_value' => "sum(ifnull(customer_credit_amount, 0))",
                    'refund_value' => "sum(ifnull(total_refunded, 0))",
                    'total_value' => "sum(grand_total) + sum(ifnull(customer_credit_amount, 0))",
                    'order_numbers' => "group_concat(distinct concat($_orderAlias.entity_id, ':', $_orderAlias.increment_id))" ])
            ->joinLeft([ $_addressAlias => $this->getTable('sales/order_address') ],
                "$_orderAlias.entity_id = $_addressAlias.parent_id",
                [ "country_id" ])
            // Use CST since that is what Braintree uses for its reports
            ->where("$_orderAlias.created_at between CONVERT_TZ('{$this->_from}', '-06:00', '+00:00') and CONVERT_TZ(DATE_ADD('{$this->_to}', INTERVAL '23:59:59' HOUR_SECOND), '-06:00', '+00:00')")
            ->where("$_orderAlias.status not in ('fraud', 'canceled', 'canceled_request')")
            ->where("$_addressAlias.address_type = 'shipping'")
            ->group("$_addressAlias.country_id");

        $_select->from([ $_internationalAlias => $_internationalSelect ],
            [ "order_count",
                "sold_value",
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