<?php
class Soularpanic_TRSReports_Model_Resource_Report_DailyMetric_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    const TOP_LEVEL_TABLE_ALIAS = 'tableMetrics';
    const TIME_TRIP_1 = 3;
    const TIME_TRIP_2 = 6;
    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = [];
    protected $_defaultSort = 'entity_id';

    protected function _initSelect() {
        $_select = $this->getSelect();

        $_helper = Mage::helper('trsreports/collection');
        $_rawSelect = $_helper->_getNewSelect();
        $_dailyMetricTable = 'dailyMetricTable';
        $_lowStockLevelAlias = 'lowStockLevel';
        $_rawSelect
            ->from([ $_dailyMetricTable => $this->getTable('trsreports/daily_metric') ],
                [ 'entity_id' => 'product_id',
                    'average_rate',
                    'average_rate_weight',
                    'yesterday_end_of_day_inventory',
                    'today_start_of_day_inventory',
                    'yesterday_remaining_time' => "$_dailyMetricTable.yesterday_end_of_day_inventory / ($_dailyMetricTable.average_rate)",
                    'today_remaining_time' => "(($_dailyMetricTable.today_start_of_day_inventory - $_dailyMetricTable.average_rate) / ($_dailyMetricTable.average_rate))"
                ])
            ->joinLeft([ $_lowStockLevelAlias => 'erp_view_supplyneeds_base' ],
                "$_dailyMetricTable.product_id = $_lowStockLevelAlias.product_id",
                [ "warning_stock_level",
                    'stock_level_trip' => "if($_dailyMetricTable.yesterday_end_of_day_inventory > $_lowStockLevelAlias.warning_stock_level AND $_dailyMetricTable.today_start_of_day_inventory < $_lowStockLevelAlias.warning_stock_level, TRUE, FALSE)"]);

        $_timeTrip1 = self::TIME_TRIP_1;
        $_timeTrip2 = self::TIME_TRIP_2;
        $_dailyMetricsSelect = $_helper->_getNewSelect();
        $_rawData = 'rawData';
        $_dailyMetricsSelect->from([ $_rawData => $_rawSelect ],
            [ 'entity_id',
                'inventory_qty' => 'today_start_of_day_inventory',
                'yesterday_remaining_time',
                'today_remaining_time',
                'stock_level_trip',
                'estimated_time_trip' => "if(($_rawData.yesterday_remaining_time >= '$_timeTrip1' and $_rawData.today_remaining_time < '$_timeTrip1') OR ($_rawData.yesterday_remaining_time >= '$_timeTrip2' and $_rawData.today_remaining_time < '$_timeTrip2'), TRUE, FALSE)"
            ]);
        $_dailyMetricsAlias = self::TOP_LEVEL_TABLE_ALIAS;
        $_skuAlias = 'sku';
        $productNameAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
        $_productNameTable = $productNameAttr->getBackendTable();
        $_select->from([ $_dailyMetricsAlias => $_dailyMetricsSelect ],
            [ 'entity_id',
                'inventory_qty',
                'stock_level_trip',
                'today_remaining_time',
                'estimated_time_trip' ])
            ->joinLeft([ $_skuAlias => $this->getTable('catalog/product') ],
                "$_skuAlias.entity_id = $_dailyMetricsAlias.entity_id",
                [ 'sku' ])
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '{$productNameAttr->getId()}' and {$_productNameTable}.entity_id = {$_dailyMetricsAlias}.entity_id",
                [ 'name' => 'value' ])
            ->where("((stock_level_trip = TRUE) OR (estimated_time_trip = TRUE))");


        $this->log("Daily Metric SQL:\n".$this->getSelect()->__toString());
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