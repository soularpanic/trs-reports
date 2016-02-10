<?php
class Soularpanic_TRSReports_Helper_Report_Config
    extends Mage_Core_Helper_Abstract {

    const LOW_STOCK_LOW_MEDIUM_THRESHOLD_PATH = 'trs_reports/low_stock_by_availability_report/low_medium_threshold';
    const LOW_STOCK_MEDIUM_HIGH_THRESHOLD_PATH = 'trs_reports/low_stock_by_availability_report/medium_high_threshold';

    public function getLowStockLowMediumThreshold() {
        return Mage::getStoreConfig(self::LOW_STOCK_LOW_MEDIUM_THRESHOLD_PATH);
    }

    public function getLowStockMediumHighThreshold() {
        return Mage::getStoreConfig(self::LOW_STOCK_MEDIUM_HIGH_THRESHOLD_PATH);
    }

}