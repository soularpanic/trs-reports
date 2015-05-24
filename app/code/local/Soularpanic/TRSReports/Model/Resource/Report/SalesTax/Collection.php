<?php
class Soularpanic_TRSReports_Model_Resource_Report_SalesTax_Collection
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
        $_select = $this->getSelect();

        $_taxRate = self::LOCAL_TAX_RATE;
        $_region = self::REGION;
        $_statuses = "'".implode('\', \'', $this->_statusWhitelist)."'";

        $_ordersAlias = self::TOP_LEVEL_TABLE_ALIAS;
        $_addressAlias = 'addresses';

        $_select->from([ $_ordersAlias => $this->getTable('sales/order') ],
            [ 'subtotal' => 'sum(base_subtotal)',
                'tax_total' => 'sum(base_tax_amount)',
                'calculated_taxed_sales' => "sum(base_tax_amount) / {$_taxRate}",
                'calculated_tax_exempt_sales' => "sum(base_subtotal) - (sum(base_tax_amount) / {$_taxRate})" ])
            ->joinLeft([ $_addressAlias => $this->getTable('sales/order_address') ],
                "$_addressAlias.parent_id = $_ordersAlias.entity_id",
                [ 'region' ])
            ->where("$_addressAlias.region = '$_region'")
            ->where("$_addressAlias.address_type = 'shipping'")
            ->where("$_ordersAlias.created_at between '$this->_from' and '$this->_to'")
            ->where("status in($_statuses)");

        $this->log("Sales Tax SQL:\n".$this->getSelect()->__toString());
    }

    protected function _applyCustomFilter() {
        $customFilterData = $this->getCustomFilterData();
        $customFilterData->setProductTable(self::TOP_LEVEL_TABLE_ALIAS);

        return parent::_applyCustomFilter();
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter() {
        return $this;
    }


}