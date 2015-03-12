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
}