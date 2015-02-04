<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailability
extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "LowStockAvailability";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Low Stock by Availability (15 Week Sales Average)');
        parent::__construct();
    }
}