<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_SalesTax
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "SalesTax";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Sales Tax Report');
        parent::__construct();
    }
}