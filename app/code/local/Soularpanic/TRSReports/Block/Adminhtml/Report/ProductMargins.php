<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_ProductMargins
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "ProductMargins";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Product Margins');
        parent::__construct();
    }
}