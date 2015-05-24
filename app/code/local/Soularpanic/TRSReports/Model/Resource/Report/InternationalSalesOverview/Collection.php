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
//        $_ordersSelect = $_helper->getProductOrders($this->_from, $this->_to);

        $_internationalAlias = self::TOP_LEVEL_TABLE_ALIAS;
        $_orderAlias = 'orders';
        $_addressAlias = 'addresses';
        $_countryAlias = 'countries';
        $_internationalSelect
            ->from([ $_orderAlias => $this->getTable('sales/order') ],
                [ 'entity_id',
                    'order_count' => "count(distinct $_orderAlias.entity_id)",
                    'sold_value' => "sum(grand_total)",
                    'credit_value' => "sum(ifnull(customer_credit_amount, 0))",
                    'total_value' => "sum(grand_total) + sum(ifnull(customer_credit_amount, 0))",
                    'order_numbers' => "group_concat(distinct concat($_orderAlias.entity_id, ':', $_orderAlias.increment_id))" ])
            ->joinLeft([ $_addressAlias => $this->getTable('sales/order_address') ],
                "$_orderAlias.entity_id = $_addressAlias.parent_id",
                [ "country_id" ])
            ->where("$_orderAlias.created_at between '{$this->_from}' and '{$this->_to}'")
            ->where("$_addressAlias.country_id != 'US'")
            ->where("$_addressAlias.address_type = 'shipping'")
            ->group("$_addressAlias.country_id");

        $_select->from([ $_internationalAlias => $_internationalSelect ],
            [ //"region" => "country_id",
                "order_count",
                "sold_value",
                "credit_value",
                "total_value",
                "order_numbers" ])
            ->joinLeft([ $_countryAlias => $this->getTable('directory/country') ],
                "$_countryAlias.country_id = $_internationalAlias.country_id",
                [ 'region' => 'name' ]);

//        $_select->from([ $_xAlias => $_ordersSelect ],
//            '*');

//        $_taxRate = self::LOCAL_TAX_RATE;
//        $_region = self::REGION;
//        $_statuses = "'".implode('\', \'', $this->_statusWhitelist)."'";
//
//        $_ordersAlias = self::TOP_LEVEL_TABLE_ALIAS;
//        $_addressAlias = 'addresses';
//
//        $_select->from([ $_ordersAlias => $this->getTable('sales/order') ],
//            [ 'subtotal' => 'sum(base_subtotal)',
//                'tax_total' => 'sum(base_tax_amount)',
//                'calculated_taxed_sales' => "sum(base_tax_amount) / {$_taxRate}",
//                'calculated_tax_exempt_sales' => "sum(base_subtotal) - (sum(base_tax_amount) / {$_taxRate})" ])
//            ->joinLeft([ $_addressAlias => $this->getTable('sales/order_address') ],
//                "$_addressAlias.parent_id = $_ordersAlias.entity_id",
//                [ 'region' ])
//            ->where("$_addressAlias.region = '$_region'")
//            ->where("$_addressAlias.address_type = 'shipping'")
//            ->where("$_ordersAlias.created_at between '$this->_from' and '$this->_to'")
//            ->where("status in($_statuses)");

        $this->log("International Sales SQL:\n".$this->getSelect()->__toString());
    }

    protected function _applyCustomFilter() {
        $_field = $this->_sort === null ? $this->_defaultSort : $this->_sort;
        $_dir = $this->_sortDir ? $this->_sortDir : 'ASC';
        if ($_field !== null) {
            $this->getSelect()->order(array("{$_field} {$_dir}"));
        }
//        $customFilterData = $this->getCustomFilterData();
//        $customFilterData->setProductTable(self::TOP_LEVEL_TABLE_ALIAS);
//
//        return parent::_applyCustomFilter();
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter() {
        return $this;
    }


}