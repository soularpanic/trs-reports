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

//        $filterData = $this->getCustomFilterData();

//        $growth = $filterData->getGrowthPercent();
//        $futureRate = $growth ? 1.0 + (floatval($growth) / 100.0) : 1.0;
//        $futureDate = $filterData->getFuture();
//        $futureDate = $futureDate ? "'$futureDate'" : "NOW()";

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


//        $_helper = Mage::helper('trsreports/collection');

//        $_customerOrders = $_helper->getProductOrders($this->_from, $this->_to);
//        $_select
//            ->from([ 'foo' => $_customerOrders ],
//                '*');


//        $_productLinesSelect = $_helper->getProductLinesSelect();
//        $_customerOrders = $_helper->getProductOrders($this->_from, $this->_to, $_productLinesSelect);
//        $_inventory = $_helper->getProductInventory($this->_from, $this->_to);
//
//
//
//        $_grpByProductSelect = $_helper->_getNewSelect();
//        $_productLinesAlias = "productLines";
//        $_customerOrdersAlias = "customerOrders";
//        $_inventoryAlias = "inventory";
//        $_grpByProductSelect->from([ $_productLinesAlias => $_productLinesSelect ],
//            [ 'product_id' => 'product_id',
//                'derived_name' => "line_name",
//                'derived_sku' => 'line_sku',
//                'derived_id' => "(if($_productLinesAlias.tree_name is not null, concat('T-', $_productLinesAlias.tree_id), if($_productLinesAlias.piece_name is not null, concat('L-', $_productLinesAlias.piece_id), concat('P-', $_productLinesAlias.product_id))))",
//            ])
//            ->joinLeft([ $_customerOrdersAlias => $_customerOrders ],
//                "$_customerOrdersAlias.product_id = $_productLinesAlias.product_id",
//                [ "total_qty_ordered",
//                    "time_in_days" ])
//            ->joinLeft([ $_inventoryAlias => $_inventory ],
//                "$_inventoryAlias.product_id = $_productLinesAlias.product_id",
//                [ 'qty',
//                    'suppliers',
//                    'purchase_orders',
//                    'received_qty' => "ifnull(received_qty, 0)",
//                    'incoming_qty' => "ifnull(incoming_qty, 0)",
//                    'total_qty' => "ifnull(total_qty, 0)"]);
//        $this->log("\n\n2:\n".$_grpByProductSelect->__toString());
//
//        $_futureForecastRawSelect = $_helper->_getNewSelect();
//        $_futureForecastRaw = "futureForecastRaw";
//        $_futureForecastRawSelect
//            ->from([ $_futureForecastRaw => $_grpByProductSelect ],
//                [ 'derived_id',
//                    'entity_id' => 'product_id',
//                    'derived_name',
//                    'derived_sku',
//                    'total_qty_stock' => 'sum(ifnull(qty, 0))',
//                    'total_qty_stock_and_transit' => 'sum(ifnull(qty, 0)) + sum(incoming_qty)',
//                    'total_qty_sold' => "sum(ifnull(total_qty_ordered, 0))",
//                    'time_in_days' => 'max(time_in_days)',
//                    'suppliers',
//                    'purchase_orders' => 'group_concat(purchase_orders)'
//                ])
//            ->group('derived_id');
//        $this->log("\n\n3:\n" . $_futureForecastRawSelect->__toString());
//
//        $_futureForecastAlias = self::TOP_LEVEL_TABLE_ALIAS;
//        $_qtyToOrderFormula = "(($futureRate * DATEDIFF($futureDate, NOW()) * total_qty_sold / time_in_days) - total_qty_stock_and_transit)";
//        $_select
//            ->from([ $_futureForecastAlias => $_futureForecastRawSelect ],
//                [ 'derived_id',
//                    'entity_id',
//                    'derived_name',
//                    'derived_sku',
//                    'total_qty_stock',
//                    'total_qty_stock_and_transit',
//                    'total_qty_sold',
//                    'time_in_days',
//                    'purchase_orders',
//                    'suppliers',
//                    'estimated_remaining_weeks' => "(total_qty_stock / (7 * total_qty_sold / time_in_days))",
//                    'future_qty' => "($futureRate * DATEDIFF($futureDate, NOW()) * total_qty_sold / time_in_days)",
//                    "qty_to_order" => "if($_qtyToOrderFormula > 0, $_qtyToOrderFormula, 0)"
//                ])
//            ->joinLeft([ 'catalog' => $this->getTable('catalog/product') ],
//                "catalog.entity_id = $_futureForecastAlias.entity_id",
//                [])
//            ->joinLeft([ 'attrset' => $this->getTable("eav/attribute_set") ],
//                "catalog.attribute_set_id = attrset.attribute_set_id",
//                "attribute_set_name")
//            ->where("time_in_days is not null")
//            ->where("catalog.type_id = 'simple'")
//            ->where('attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');

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