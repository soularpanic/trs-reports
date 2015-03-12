<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines
    extends Mage_Adminhtml_Block_Widget_Grid_Container {

    protected $_blockGroup = "trsreports";
    protected $_controller = "adminhtml_report_manage_lines";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Manage Product Lines');
        parent::__construct();

        $this->_addButton('add', array(
            'label'     => $this->getAddButtonLabel(),
            'class'     => 'add',
            'data-foo' => 'bar'
        ));
    }
}