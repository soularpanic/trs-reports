<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailabilityPlusTransit
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "LowStockAvailabilityPlusTransit";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Low Stock by Availability Plus In-Transit (10 Week Sales Average)');
        parent::__construct();
    }
}