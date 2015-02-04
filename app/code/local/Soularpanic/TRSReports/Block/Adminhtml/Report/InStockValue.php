<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InStockValue
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "InStockValue";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('In Stock Value');
        parent::__construct();
    }
}