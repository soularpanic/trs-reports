<?php
class Soularpanic_TRSReports_Admin_Manage_ProductLinesController
    extends Soularpanic_TRSReports_Controller_Abstract {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addLinePromptAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addLineSubmitAction() {
        $req = $this->getRequest();
        $resp = $this->getResponse()->setHeader('Content-type', 'application/json');

        $sku = $req->getParam('sku');
        $name = $req->getParam('name');

        $conflicts = Mage::getModel('trsreports/product_line')->getCollection();
        $conflicts->addFilter('name', $name, 'or')
            ->addFilter('line_sku', $sku, 'or');

        if ($conflicts->count() > 0) {
            $conflict = $conflicts->getFirstItem();
            $msg = "Error! {$name}/{$sku} conflicts with existing line {$conflict->getName()}/{$conflict->getLineSku()}";
            $this->log("Error!  There's already a line for {$name}/{$sku}");

            $resp->setHttpResponseCode(409)
                ->setBody($msg);
            return $this;

        }


        $newLine = Mage::getModel('trsreports/product_line');
        $newLine->setData([
            'line_sku' => $sku,
            'name' => $name
        ]);
        $newLine->save();
        $newId = $newLine->getId();

        if (!$newId) {
            $resp->setHttpResponseCode(500)
                ->setBody("Failed to save the new line to the database.  Call Josh!");
            return $this;
        }

        $resp->setHttpResponseCode(200)
            ->setBody($newId);
    }

    public function editAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function addLineProductsActions() {
        $req = $this->getRequest();
        $lineId = $req->getParam('line_id');
        $productIds = $req->getParam('product_id');

        $failures = [];

        foreach ($productIds as $productId) {
            $existingLinks = Mage::getModel('trsreports/product_line_link')->getCollection();
            $existingLinks->addFilter('line_id', $lineId, 'and')
                ->addFilter('product_id', $productId, 'and');

            if ($existingLinks->count() > 0) {
                $failures[] = $productId;
            }
            else {
                $link = Mage::getModel('trsreports/product_line_link');
                $link->setData(['line_id' => $lineId, 'product_id' => $productId]);
                $link->save();
            }

        }

        foreach ($failures as $failure) {
            $product = Mage::getModel('catalog/product')->load($failure);
            Mage::getSingleton('adminhtml/session')->addWarning("{$product->getName()} was not added as it's already a member");
        }

        $this->_redirectReferer();
    }

    protected function removeLineProductsAction() {
        $req = $this->getRequest();
        $lineId = $req->getParam('line_id');
        $productIds = $req->getParam('product_id');

        $failures = [];

        foreach ($productIds as $productId) {
            $existingLinks = Mage::getModel('trsreports/product_line_link')->getCollection();
            $existingLinks->addFilter('line_id', $lineId, 'and')
                ->addFilter('product_id', $productId, 'and');

            if ($existingLinks->count() < 1) {
                $failures[] = $productId;
            }
            else {
                $link = $existingLinks->getFirstItem();
                $link->delete();
            }
        }

        foreach ($failures as $failure) {
            $product = Mage::getModel('catalog/product')->load($failure);
            Mage::getSingleton('adminhtml/session')->addWarning("{$product->getName()} was not removed as it's not a member");
        }

        $this->_redirectReferer();
    }
}