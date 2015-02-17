<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Exclusions
    extends Mage_Adminhtml_Block_Widget_Grid_Container {

    protected $_blockGroup = "trsreports";
    protected $_controller = "adminhtml_report_manage_exclusions";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Manage Exclusions');
        parent::__construct();
    }
}