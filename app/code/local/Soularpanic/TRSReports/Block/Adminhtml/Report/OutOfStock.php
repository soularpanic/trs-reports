<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_OutOfStock
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "OutOfStock";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Out of Stock Products');
        parent::__construct();
        //$this->_requiresDateRange = false;
    }
}