<?php
class Soularpanic_TRSReports_Model_Observers_Reports_Schedule {

    public function updateDailyMetrics() {
        $logger = Mage::helper('trsreports');
        $logger->log("I'm the update daily metrics method");
//        $collection = Mage::getModel('trsreports/daily_metric')->getCollection();
        $productCollection = Mage::getModel('catalog/product')->getCollection();

        foreach ($productCollection as $product) {
            $metric = Mage::getModel('trsreports/daily_metric')->load($product->getId(), 'product_id');
            $stock = Mage::getModel('cataloginventory/stock_item')->load($product->getId(), 'product_id');

            $currentStock = (int)$stock->getAvailableQty();
            $_sold = (int)$stock->getStockOrderedQty(); // sold today

//            $_yesterdayInv = (int)$metric->getYesterdayEndOfDayInventory();
            $_todayInv = (int)$metric->getTodayStartOfDayInventory();
            $_averageRate = (float)$metric->getAverageRate();
            $_averageRateWeight = (int)$metric->getAverageRateWeight();

            // average is units / week; weight is in days
            $_newAverage = ((((float)$_averageRateWeight * $_averageRate) + ($_sold / 7)) / ($_averageRateWeight + 1));

            $metric->setProductId($product->getId());
            $metric->setYesterdayEndOfDayInventory($_todayInv);
            $metric->setTodayStartOfDayInventory($currentStock);
            $metric->setAverageRate($_newAverage);
            $metric->setAverageRateWeight($_averageRateWeight + 1);

            $metric->save();
        }
    }

    public function emailDailyMetricsReport() {
        $logger = Mage::helper('trsreports');
        $logger->log("I am the schedule emailDailyMetrics method!");

        $this->_emailMonthlyReport(
            "Daily Metrics",
            Mage::helper('trsreports/report_automation')->getDailyMetricsReportRecipients(),
            "DailyMetric",
            "trsreports/adminhtml_report_DailyMetric_Grid"
        );
    }

    public function emailInternationalSalesOverviewReport() {
        $logger = Mage::helper('trsreports');
        $logger->log("I am the schedule emailInternationalSalesOverview method!");

        $this->_emailMonthlyReport(
            "International Sales Overview",
            Mage::helper('trsreports/report_automation')->getInternationalSalesReportRecipients(),
            "InternationalSalesOverview",
            "trsreports/adminhtml_report_InternationalSalesOverview_CsvGrid"
        );
    }

    public function emailSalesTaxReport() {
        $logger = Mage::helper('trsreports');
        $logger->log("I am the schedule emailSalesTaxReport method!");

        $this->_emailMonthlyReport(
            "Sales Tax",
            Mage::helper('trsreports/report_automation')->getSalesTaxReportRecipients(),
            "SalesTax",
            "trsreports/adminhtml_report_SalesTax_grid"
        );
    }

    public function emailDeliveryAndValueReport() {
        $logger = Mage::helper('trsreports');
        $logger->log("I am the schedule emailDeliveryAndValue method!");

        $this->_emailMonthlyReport(
            "Delivery and Value",
            Mage::helper('trsreports/report_automation')->getDeliveryAndValueReportRecipients(),
            "DeliveryAndValue",
            "trsreports/adminhtml_report_DeliveryAndValue_CsvGrid",
            [ 'show_zero_remaining_delivery' => '1' ]
        );
    }

    protected function _emailMonthlyReport($emailSubjectLead, $emailAddressesArray, $reportCode, $reportBlock, $customFilterData = []) {
        $logger = Mage::helper('trsreports');
        $logger->log("I am the schedule _emailMonthlyReport method!");


        $_reportBlock = Mage::getSingleton('core/layout')->createBlock($reportBlock);

        $to = new DateTime();
        $year = (int)$to->format('Y');
        $month = (int)$to->format('m');
        $logger->log("to year: $year; to month: $month");
        $fromMonth = $month === 1 ? 12 : $month - 1;
        $fromYear = $month === 1 ? $year - 1 : $year;
        $from = new DateTime("$fromYear-$fromMonth-01");
        $formattedFrom = $from->format('Y-m-d');
        $formattedTo = $to->format('Y-m-d');
        $logger->log("from date: $formattedFrom");

        $params = new Varien_Object(array_merge([
            'from' => $formattedFrom,
            'to' => $formattedTo,
            'report_code' => $reportCode
        ], $customFilterData));
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