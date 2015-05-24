<?php
class Soularpanic_TRSReports_Helper_Report_Automation
    extends Mage_Core_Helper_Abstract {

    const SALES_TAX_RECIPIENTS_PATH = 'trs_automated_reports/sales_tax_report/recipients';
    const INTERNATIONAL_SALES_RECIPIENTS_PATH = 'trs_automated_reports/sales_tax_report/recipients';
    const DAILY_METRIC_RECIPIENTS_PATH = 'trs_automated_reports/daily_metrics_report/recipients';

    public function getSalesTaxReportRecipients() {
        return $this->_getReportRecipients(Mage::getStoreConfig(self::SALES_TAX_RECIPIENTS_PATH));
    }

    public function getInternationalSalesReportRecipients() {
        return $this->_getReportRecipients(Mage::getStoreConfig(self::INTERNATIONAL_SALES_RECIPIENTS_PATH));
    }

    public function getDailyMetricReportRecipients() {
        return $this->_getReportRecipients(Mage::getStoreConfig(self::DAILY_METRIC_RECIPIENTS_PATH));
    }

    private function _getReportRecipients($recipientsStr) {
        return array_map('trim', explode(',', $recipientsStr));
    }

}