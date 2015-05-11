<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage_Form_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'trsreports';
        $this->_controller = "adminhtml_catalog_product_tree_manage_form";
        $this->_headerText = $this->__("Manage Product Tree");
    }

}