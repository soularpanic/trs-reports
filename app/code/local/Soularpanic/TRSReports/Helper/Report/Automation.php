<?php
class Soularpanic_TRSReports_Helper_Report_Automation
    extends Mage_Core_Helper_Abstract {

    const SALES_TAX_RECIPIENTS_PATH = 'trs_automated_reports/sales_tax_report/recipients';
    const INTERNATIONAL_SALES_RECIPIENTS_PATH = 'trs_automated_reports/sales_tax_report/recipients';

    public function getSalesTaxReportRecipients() {
        $raw = Mage::getStoreConfig(self::SALES_TAX_RECIPIENTS_PATH);
        return array_map('trim', explode(',', $raw));
    }

    public function getInternationalSalesReportRecipients() {
        $raw = Mage::getStoreConfig(self::INTERNATIONAL_SALES_RECIPIENTS_PATH);
        return array_map('trim', explode(',', $raw));
    }

}