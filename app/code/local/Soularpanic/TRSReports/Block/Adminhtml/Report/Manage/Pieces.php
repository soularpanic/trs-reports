<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Pieces
    extends Mage_Adminhtml_Block_Widget_Grid_Container {

    protected $_blockGroup = "trsreports";
    protected $_controller = "adminhtml_report_manage_pieces";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Manage Product Pieces');
        parent::__construct();

        $this->_addButton('add', array(
            'label'     => $this->getAddButtonLabel(),
            'class'     => 'add'
        ));
    }
}