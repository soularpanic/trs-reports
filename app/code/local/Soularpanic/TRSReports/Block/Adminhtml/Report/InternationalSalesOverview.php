<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InternationalSalesOverview
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "InternationalSalesOverview";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('International Sales Overview Report');
        parent::__construct();
    }
}