<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_ProductSales
extends Soularpanic_TRSReports_Block_Adminhtml_Report_JsonGrid_Container_Abstract {
    protected $_reportTag = "ProductSales";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Items Sold');
        parent::__construct();
    }
}