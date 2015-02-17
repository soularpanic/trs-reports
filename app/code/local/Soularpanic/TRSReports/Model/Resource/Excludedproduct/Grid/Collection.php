<?php
class Soularpanic_TRSReports_Model_Resource_Excludedproduct_Grid_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('trsreports/excludedproduct');
    }

    protected function _initSelect() {
        parent::_initSelect();
        $_select = $this->getSelect();
        $productName = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
        $productSku = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'sku');
        $mainTable = 'main_table';
        $_select
            ->join(['product_sku' => $productSku->getBackendTable()],
                "product_sku.entity_id = $mainTable.product_id",
                ['sku' ])
            ->join(['product_name' => $productName->getBackendTable()],
                "`product_name`.entity_id = `$mainTable`.product_id and `product_name`.attribute_id = '{$productName->getId()}'",
                [ 'product_name' => 'value' ]);

        Mage::log("manage exclusion sql:\n".$_select->__toString(), null, 'trs_reports.log');
    }

}