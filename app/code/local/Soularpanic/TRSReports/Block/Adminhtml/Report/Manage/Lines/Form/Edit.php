<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines_Form_Edit
extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'trsreports';
        $this->_controller = "adminhtml_report_manage_lines_form";
        $this->_headerText = $this->__("Manage Product Line");
    }

}