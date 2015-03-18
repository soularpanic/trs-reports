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

        $lineId = $this->getRequest()->getParam('id');
        $line = Mage::getModel('trsreports/product_line')->load($lineId);
        Mage::register('soularpanic_adminform_manage_lines', $line);

        $this->renderLayout();
    }

    public function addLineProductsAction() {
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

    public function removeLineProductsAction() {
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


    public function saveAction() {
        $req = $this->getRequest();

        $lineId = $req->getParam('entity_id');

        $newName = $req->getParam('name');
        $newSku = $req->getParam('line_sku');

        $currentLine = Mage::getModel('trsreports/product_line')->load($lineId);

        // if we didn't actually change anything, we're done
        if ($currentLine->getName() === $newName
            && $currentLine->getLineSku() === $newSku) {
            $this->_redirectReferer();
            return $this;
        }

        $conflicts = Mage::getModel('trsreports/product_line')->getCollection();
        $conflicts->addFilter('name', $newName, 'or')
            ->addFilter('line_sku', $newSku, 'or');


        foreach ($conflicts as $conflict) {
            if ($conflict->getId() !== $lineId) {
                Mage::getSingleton('adminhtml/session')->addError("{$currentLine->getName()}/{$currentLine->getLineSku()} update to {$newName}/{$newSku} failed, because it conflicts with existing line {$conflict->getName()}/{$conflict->getLineSku()}");
                $this->_redirectReferer();
                return $this;
            }
        }

        $currentLine->setName($newName);
        $currentLine->setLineSku($newSku);
        $currentLine->save();

        $this->_redirect('*/*/index');
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $line = Mage::getModel('trsreports/product_line')->load($id);
        $line->delete();
        $this->_redirect('*/*/index');
        return $this;
    }
}