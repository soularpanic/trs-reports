<?php
class Soularpanic_TRSReports_Admin_Manage_ProductExclusionsController
    extends Soularpanic_TRSReports_Controller_Abstract {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function excludeAction() {
        $reportCode = $this->getRequest()->getParam('report_code');
        $_ids = $this->getRequest()->getParam('product_id');
        $_skus = $this->getRequest()->getParam('sku');
        $product = Mage::getModel('catalog/product');

        foreach ($_skus as $sku) {
            $_resolvedId = $product->getIdBySku($sku);
            if (!$_resolvedId) {
                $this->log("Could not resolve sku '{$sku}'!");
                Mage::getSingleton('adminhtml/session')->addError("Could not resolve sku '{$sku}'!");
                continue;
            }
            $_ids[] = $_resolvedId;
        }

        foreach ($_ids as $_id) {
            $exclusion = Mage::getModel('trsreports/excludedproduct');
            $exclusion->setProductId($_id);
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

}