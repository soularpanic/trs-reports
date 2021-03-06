<?php
class Soularpanic_TRSReports_Admin_TrsreportsController
    extends Soularpanic_TRSReports_Controller_Abstract {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function fetchItemsSoldAction() {
        $salesData = Mage::helper('trsreports/report_graph_data')->getItemsSoldData($this->_getFrom(), $this->_getTo(), $this->_getGranularity());

        $this->getResponse()->setHeader('Content-type', 'text/javascript');
        $this->getResponse()->setBody($salesData);
    }

    public function productsalesAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsgridwithmassactionAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function attributesetsalesAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function profitandsalesAction() {
        $this->loadLayout();
        $this->renderLayout();
    }



    public function fetchSalesReportDataAction() {
        $salesData = Mage::helper('trsreports/report_graph_data')
            ->getSalesReportData(
                $this->_getProductId(),
                null,
                $this->_getSelectedStoreIds(),
                $this->_getFrom(),
                $this->_getTo(),
                $this->_getGranularity());

        $this->getResponse()->setHeader('Content-type', 'text/javascript');
        $this->getResponse()->setBody($salesData);
    }

    public function fetchAttributeSetSalesReportDataAction() {
        $salesData = Mage::helper('trsreports/report_graph_data')
            ->getSalesReportData(
                null,
                $this->_getAttrSetId(),
                null,
                $this->_getFrom(),
                $this->_getTo(),
                $this->_getGranularity());

        $this->getResponse()->setHeader('Content-type', 'text/javascript');
        $this->getResponse()->setBody($salesData);
    }

    public function lowstockavailabilityAction() {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_LowStockAvailability.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $today = date('m/d/Y');
        $tenWeeksAgo = date('m/d/Y', time() - (10 * 7 * 24 * 60 * 60));
        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => $tenWeeksAgo,
                'to' => $today));

        $this->renderLayout();
    }

    public function lowstockavailabilityplustransitAction() {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_LowStockAvailabilityPlusTransit.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $today = date('m/d/Y');
        $tenWeeksAgo = date('m/d/Y', time() - (10 * 7 * 24 * 60 * 60));
        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => $tenWeeksAgo,
                'to' => $today));

        $this->renderLayout();
    }

    public function outofstockAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_OutOfStock.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function futureforecastAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_FutureForecast.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $dateFormatString = 'm/d/Y';

        $today = date($dateFormatString);
        $helper = Mage::helper('trsreports/report_config');
        $averagePeriod = $helper->getFutureForecastAveragePeriod();
        $weeksAgo = date($dateFormatString, time() - ($averagePeriod * 7 * 24 * 60 * 60));
        $helper->log("calculating future forecast over $averagePeriod, i.e. since $weeksAgo");

        $growthPercent = $this->_store('growth_percent', true);
        if (!$growthPercent) {
            $growthPercent = "0";
        }

        $futureStart = $this->_store('future_start', true) ?: $today;
        $futureEnd = $this->_store('future_end', true) ?: $today;

        $oneYear = new DateInterval('P1Y');
        $futureStartDate = DateTime::createFromFormat($dateFormatString, $futureStart);
        $pastStartDate = $futureStartDate->sub($oneYear);
        if ($pastStartDate > new DateTime("now")) {
            Mage::getSingleton('adminhtml/session')->addError("Future date should be less than a year from now to generate meaningful results");
        }


        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            [ 'from' => $weeksAgo,
                'to' => $today,
                'future_start' => $futureStart,
                'future_end' => $futureEnd,
                'growth_percent' => $growthPercent ],
            [ 'future_start',
                'future_end' ]);

        $this->renderLayout();
    }

    public function instockvalueAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_InStockValue.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function intransitvalueAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_InTransitValue.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function purchaseorderoverviewAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_PurchaseOrderOverview.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function salestaxAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_SalesTax.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function exportSalesTaxCsvAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()
            ->createBlock('trsreports/adminhtml_report_SalesTax_grid');

        $this->_initReportAction([ $gridBlock ],
            [ 'from' => date('m/d/Y'),
                'to' => date('m/d/Y'),
                'report_code' => 'SalesTax' ]);

        $content = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse('SalesTax.csv', $content);
    }

    public function cashsalesAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_CashSales.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function exportCashSalesCsvAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()
            ->createBlock('trsreports/adminhtml_report_CashSales_CsvGrid');

        $this->_initReportAction([ $gridBlock ],
            [ 'from' => date('m/d/Y'),
                'to' => date('m/d/Y'),
                'report_code' => 'CashSales' ]);

        $content = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse('CashSales.csv', $content);
    }

    public function internationalsalesoverviewAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_InternationalSalesOverview.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function exportInternationalSalesOverviewCsvAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()
            ->createBlock('trsreports/adminhtml_report_InternationalSalesOverview_CsvGrid');

        $this->_initReportAction([ $gridBlock ],
            [ 'from' => date('m/d/Y'),
                'to' => date('m/d/Y'),
                'report_code' => 'InternationalSalesOverview' ]);

        $content = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse('InternationalSalesOverview.csv', $content);
    }

    public function dailymetricAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_DailyMetric.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => date('m/d/Y'),
                'to' => date('m/d/Y')));

        $this->renderLayout();
    }

    public function exportDailyMetricCsvAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()
            ->createBlock('trsreports/adminhtml_report_DailyMetric_Grid');

        $this->_initReportAction([ $gridBlock ],
            [ 'from' => date('m/d/Y'),
                'to' => date('m/d/Y'),
                'report_code' => 'DailyMetric' ]);

        $content = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse('DailyMetrics.csv', $content);
    }

    public function deliveryandvalueAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_DeliveryAndValue.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
                $gridBlock,
                $filterFormBlock
            ],
            [
                'from' => date('m/d/Y'),
                'to' => date('m/d/Y')
            ]);
        $this->renderLayout();
    }

    public function deliveryandvaluedeliverydetailajaxAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_DeliveryAndValueDeliveryDetail.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
                $gridBlock,
                $filterFormBlock
            ],
            [
                'from' => date('m/d/Y'),
                'to' => date('m/d/Y')
            ]);
        $this->renderLayout();
    }

    public function deliveryandvaluepaymentdetailajaxAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_DeliveryAndValuePaymentDetail.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
                $gridBlock,
                $filterFormBlock
            ],
            [
                'from' => date('m/d/Y'),
                'to' => date('m/d/Y')
            ]);
        $this->renderLayout();
    }

    public function exportDeliveryAndValueCsvAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()
            ->createBlock('trsreports/adminhtml_report_DeliveryAndValue_CsvGrid');

        $this->_initReportAction([ $gridBlock ],
            [ 'from' => date('m/d/Y'),
                'to' => date('m/d/Y'),
                'report_code' => 'DeliveryAndValue',
                'show_zero_remaining_delivery' => '1' ]);

        $content = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse('DeliveryAndValue.csv', $content);
    }

    public function productmarginsAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_ProductMargins.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([
                $gridBlock,
                $filterFormBlock
            ],
            [
                'from' => date('m/d/Y'),
                'to' => date('m/d/Y')
            ]);
        $this->renderLayout();
    }

    public function exportProductMarginsCsvAction() {
        $this->_initAction();
        $gridBlock = $this->getLayout()
            ->createBlock('trsreports/adminhtml_report_ProductMargins_CsvGrid');

        $this->_initReportAction([ $gridBlock ],
            [ 'from' => date('m/d/Y'),
                'to' => date('m/d/Y'),
                'report_code' => 'ProductMargins' ]);

        $content = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse('ProductMargins.csv', $content);
    }

    public function testDailyMetricAction() {
        Mage::log("testDailyMetricAction controller method - start", null, 'trs_reports.log');
        $model = Mage::getModel('trsreports/observers_reports_schedule');
        $model->updateDailyMetrics();
    }

    public function testDeliveryAndValueAction() {
        Mage::log("testDeliveryAndValue controller method - start", null, 'trs_reports.log');
        $model = Mage::getModel('trsreports/observers_reports_schedule');
        $model->emailDeliveryAndValueReport();
    }

    public function _initReportAction($blocks, $defaults = null, $additionalFilterDates = null)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $req = $this->getRequest();
        $requestData = Mage::helper('adminhtml')->prepareFilterString($req->getParam('filter'));
        $sortKey = $req->getParam('sort');
        if ($sortKey && !array_key_exists('sort', $requestData)) {
            $requestData['sort'] = $sortKey;
        }
        $sortDir = $req->getParam('dir');
        if ($sortDir && !array_key_exists('dir', $requestData)) {
            $requestData['dir'] = $sortDir;
        }

        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $requestData)) {
                $requestData[$key] = $value;
            }
        }

        $dateFilterKeys = array('from' ,'to');
        if ($additionalFilterDates) {
            $dateFilterKeys = array_merge($dateFilterKeys, $additionalFilterDates);
        }
        $requestData = $this->_filterDates($requestData, $dateFilterKeys);
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (isset($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    protected function _store($varKey, $checkFilter = false) {
        $req = Mage::app()->getRequest();
        $var = $req->getParam($varKey);
        if (!$var && $checkFilter) {
            $filterData = Mage::helper('adminhtml')->prepareFilterString($req->getParam('filter'));
            if (array_key_exists($varKey, $filterData)) {
                $var = $filterData[$varKey];
            }
        }
        if ($this->_shouldCheckSession()) {
            $session = Mage::getSingleton('core/session');
            if ($var) {
                $session->setData($varKey, $var);
            }
            else {
                $var = $session->getData($varKey);
            }
        }
        return $var;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('report/trsreports');
    }

    protected function _shouldCheckSession() {
        return (Mage::app()->getRequest()->getParam('ignoreSession') ? false : true);
    }

    protected function _getFrom() {
        return $this->_store('from');
    }

    protected function _getTo() {
        return $this->_store('to');
    }

    protected function _getGranularity() {
        return $this->_store('granularity');
    }

    protected function _getProductId() {
        return $this->_store('productId');
    }

    protected function _getAttrSetId() {
        return $this->_store('attributeSetId');
    }

    protected function _getSelectedStoreIds() {
        return $this->_store('storeId');
    }

}
