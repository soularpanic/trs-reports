<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_CashSales
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "CashSales";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Cash Sales Report');
        parent::__construct();
    }
}