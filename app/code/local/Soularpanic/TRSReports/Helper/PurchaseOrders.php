<?php
class Soularpanic_TRSReports_Helper_PurchaseOrders
    extends Soularpanic_TRSReports_Helper_Data {

    public function decodePurchaseOrderData($purchaseOrdersString) {
        $purchaseOrders = new Varien_Data_Collection();

        if (!$purchaseOrdersString) {
            return $purchaseOrders;
        }

        $poStrArr = explode(',', $purchaseOrdersString);
        foreach ($poStrArr as $idx => $poStr) {
            $poArr = [];
            list($poArr['po_id'], $poArr['supplier_name'], $poArr['po_code'], $poArr['po_expected_arrival'])
                = explode('::', $poStr);
            $purchaseOrders->addItem(new Varien_Object($poArr));
        }
        return $purchaseOrders;
    }

}