<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage
    extends Mage_Adminhtml_Block_Widget_Grid_Container {

    protected $_blockGroup = "trsreports";
    protected $_controller = "adminhtml_catalog_product_tree_manage";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Manage Product Trees');

        parent::__construct();

        $this->_addButton('add', array(
            'label'     => $this->getAddButtonLabel(),
            'value'   => $this->getCreateUrl(),
            'class'     => 'add',
        ));
    }
}