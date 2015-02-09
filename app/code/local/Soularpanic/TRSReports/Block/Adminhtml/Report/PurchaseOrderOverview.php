<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_PurchaseOrderOverview
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "PurchaseOrderOverview";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Purchase Order Overview');
        parent::__construct();
    }
}