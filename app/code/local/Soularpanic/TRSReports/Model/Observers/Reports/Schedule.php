<?php
class Soularpanic_TRSReports_Model_Observers_Reports_Schedule {

    public function emailInternationalSalesOverviewReport() {
        Mage::log("I am the schedule emailInternationalSalesOverview method!", null, 'trs_reports.log');

        $this->_emailMonthlyReport(
            "International Sales Overview",
            Mage::helper('trsreports/report_automation')->getInternationalSalesReportRecipients(),
            "InternationalSalesOverview",
            "trsreports/adminhtml_report_InternationalSalesOverview_CsvGrid"
        );
    }

    public function emailSalesTaxReport() {
        Mage::log("I am the schedule emailSalesTaxReport method!", null, 'trs_reports.log');

        $this->_emailMonthlyReport(
            "Sales Tax",
            Mage::helper('trsreports/report_automation')->getSalesTaxReportRecipients(),
            "SalesTax",
            "trsreports/adminhtml_report_SalesTax_grid"
        );
//        $reportBlock = Mage::getSingleton('core/layout')->createBlock('trsreports/adminhtml_report_SalesTax_grid');
//
//        $to = new DateTime();
//        $year = (int)$to->format('Y');
//        $month = (int)$to->format('m');
//        Mage::log("to year: $year; to month: $month", null, 'trs_reports.log');
//        $fromMonth = $month === 1 ? 12 : $month - 1;
//        $fromYear = $month === 1 ? $year - 1 : $year;
//        $from = new DateTime("$fromYear-$fromMonth-01");
//        $formattedFrom = $from->format('Y-m-d');
//        $formattedTo = $to->format('Y-m-d');
//        Mage::log("from date: $formattedFrom", null, 'trs_reports.log');
//
//        $params = new Varien_Object([
//            'from' => $formattedFrom,
//            'to' => $formattedTo,
//            'report_code' => 'SalesTax'
//        ]);
//        $reportBlock->setFilterData($params);
//        $fileContents = $reportBlock->getCsv();
//
//        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('test_email_template');
//        $emailVars = [ 'from' => $formattedFrom,
//            'to' => $formattedTo ];
//
//        $emailTemplate->getMail()->createAttachment(
//            $fileContents,
//            Zend_Mime::TYPE_OCTETSTREAM,
//            Zend_Mime::DISPOSITION_ATTACHMENT,
//            Zend_Mime::ENCODING_QUOTEDPRINTABLE,
//            'SalesTax.csv');
//
//        $emailTemplate->setSenderName("TRS Automated Reports");
//        $emailTemplate->setSenderEmail('josh@theretrofitsource.com');
//        $emailTemplate->setTemplateSubject("Sales Tax: $formattedFrom - $formattedTo");
//
//        $recipients = Mage::helper('trsreports/report_automation')->getSalesTaxReportRecipients();
//        Mage::log("Recipients: -".print_r($recipients, true)."-", null, 'trs_reports.log');
//
//        foreach ($recipients as $recipient) {
//            $emailTemplate->send(
//                $recipient,
//                $recipient,
//                $emailVars
//            );
//        }
    }

    protected function _emailMonthlyReport($emailSubjectLead, $emailAddressesArray, $reportCode, $reportBlock) {
        Mage::log("I am the schedule _emailMonthlyReport method!", null, 'trs_reports.log');


        $_reportBlock = Mage::getSingleton('core/layout')->createBlock($reportBlock);

        $to = new DateTime();
        $year = (int)$to->format('Y');
        $month = (int)$to->format('m');
        Mage::log("to year: $year; to month: $month", null, 'trs_reports.log');
        $fromMonth = $month === 1 ? 12 : $month - 1;
        $fromYear = $month === 1 ? $year - 1 : $year;
        $from = new DateTime("$fromYear-$fromMonth-01");
        $formattedFrom = $from->format('Y-m-d');
        $formattedTo = $to->format('Y-m-d');
        Mage::log("from date: $formattedFrom", null, 'trs_reports.log');

        $params = new Varien_Object([
            'from' => $formattedFrom,
            'to' => $formattedTo,
            'report_code' => $reportCode
        ]);
        $_reportBlock->setFilterData($params);
        $fileContents = $_reportBlock->getCsv();

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('test_email_template');
        $emailVars = [ 'from' => $formattedFrom,
            'to' => $formattedTo ];

        $emailTemplate->setSenderName("TRS Automated Reports");
        $emailTemplate->setSenderEmail('josh@theretrofitsource.com');
        $emailTemplate->setTemplateSubject("$emailSubjectLead: $formattedFrom - $formattedTo");

        foreach ($emailAddressesArray as $recipient) {

            $emailTemplate->getMail()->createAttachment(
                $fileContents,
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_QUOTEDPRINTABLE,
                "$reportCode.csv");

            $emailTemplate->send(
                $recipient,
                $recipient,
                $emailVars
            );
        }
    }
}