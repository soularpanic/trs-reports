<?php
class Soularpanic_TRSReports_Helper_Data
    extends Mage_Core_Helper_Abstract {

    const LOG_FILENAME = 'trs_reports.log';

    public function log($message) {
        if (Mage::getStoreConfig('trs_logging/logs/trs_reports_log')) {
            Mage::log($message, null, self::LOG_FILENAME);
        }
    }

}