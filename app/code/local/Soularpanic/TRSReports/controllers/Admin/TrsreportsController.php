<?php
class Soularpanic_TRSReports_Admin_TrsreportsController
    extends Mage_Adminhtml_Controller_Report_Abstract {

    public function log($message) {
        Mage::helper('trsreports')->log($message);
    }

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function manageAction() {
        $this->loadLayout();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_manage_exclusions.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $today = date('m/d/Y');
        $fiveWeeksAgo = date('m/d/Y', time() - (5 * 7 * 24 * 60 * 60));
        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => $fiveWeeksAgo,
                'to' => $today));
        $this->renderLayout();
    }

    public function excludeAction() {
        $reportCode = $this->getRequest()->getParam('report_code');
        $skus = $this->getRequest()->getParam('sku');
        $product = Mage::getModel('catalog/product');
        foreach ($skus as $sku) {
            $id = $product->getIdBySku($sku);
            if (!$id) {
                $this->log("Could not resolve sku '{$sku}'!");
                Mage::getSingleton('adminhtml/session')->addError("Could not resolve sku '{$sku}'!");
                continue;
            }

            $exclusion = Mage::getModel('trsreports/excludedproduct');
            $exclusion->setProductId($id);
            $exclusion->setReportId($reportCode);
            $exclusion->save();
        }

        $this->_redirectReferer();
    }

    public function unexcludeAction() {
        $exclusionIds = $this->getRequest()->getParam('entity_id');
        foreach ($exclusionIds as $exclusionId) {
            $exclusion = Mage::getModel('trsreports/excludedproduct')->load($exclusionId);
            if (!$exclusion) {
                $this->log("Could not find an exclusion with ID of $exclusionId!");
                continue;
            }
            $exclusion->delete();
        }

        $this->_redirectReferer();
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
        //'simple');

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
        //'attrSet');

        $this->getResponse()->setHeader('Content-type', 'text/javascript');
        $this->getResponse()->setBody($salesData);
    }

    public function lowstockavailabilityAction() {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_LowStockAvailability.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $today = date('m/d/Y');
        $fiveWeeksAgo = date('m/d/Y', time() - (5 * 7 * 24 * 60 * 60));
        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => $fiveWeeksAgo,
                'to' => $today));

        $this->renderLayout();
    }

    public function lowstockavailabilityplustransitAction() {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_LowStockAvailabilityPlusTransit.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $today = date('m/d/Y');
        $fiveWeeksAgo = date('m/d/Y', time() - (5 * 7 * 24 * 60 * 60));
        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => $fiveWeeksAgo,
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

        $today = date('m/d/Y');
        $fifteenWeeksAgo = date('m/d/Y', time() - (15 * 7 * 24 * 60 * 60));

        $future = $this->_store('future', true);
        if (!$future) {
            $future = $today;
        }

        $growthPercent = $this->_store('growth_percent', true);
        if (!$growthPercent) {
            $growthPercent = "0";
        }

        $this->_initReportAction(array(
                $gridBlock,
                $filterFormBlock
            ),
            array('from' => $fifteenWeeksAgo,
                'to' => $today,
                'future' => $future,
                'growth_percent' => $growthPercent),
            array('future'));

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
            if (!empty($value)) {
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
