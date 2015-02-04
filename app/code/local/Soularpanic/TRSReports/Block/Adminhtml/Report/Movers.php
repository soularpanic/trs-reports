<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Movers
    extends Mage_Adminhtml_Block_Widget {

    const DATE_FORMAT = "Y-m-d";
    const WEEK_LABEL = "Week to Date";
    const MONTH_LABEL = "Month to Date";
    const YEAR_LABEL = "Year to Date";

    public function getMovers() {
        return array(
            $this->_getWeekMovers(),
            $this->_getMonthMovers(),
            $this->_getYearMovers()
        );
    }

    function _getYearMovers() {
        return $this->_getBiggestMovers($this->_getDate("first day of January"), self::YEAR_LABEL);
    }

    function _getMonthMovers() {
        return $this->_getBiggestMovers($this->_getDate("first day of this month"), self::MONTH_LABEL);
    }

    function _getWeekMovers() {
        return $this->_getBiggestMovers($this->_getDate("Saturday last week"), self::WEEK_LABEL);
    }

    function _getDate($intervalStr) {
        return date(self::DATE_FORMAT, strtotime($intervalStr));
    }

    function _getBiggestMovers($from, $label) {
        $obj = new Varien_Object();
        $obj->setLabel($label);
        $obj->setStartDate($from);
        $obj->setItems($this->_getBiggestMoversData($from));
        return $obj;
    }

    function _getBiggestMoversData($from) {
        $collection = Mage::getModel('sales/order_item')
            ->getCollection()
            ->addAttributeToFilter('product_id', array('notnull' => true))
            ->addAttributeToFilter("created_at", array('from' => $from))
            ->addFilter('product_type', 'simple');
        $collection->getSelect()
            ->group('product_id')
            ->columns(array('total_qty_ordered' => "sum(qty_ordered)"), "main_table")
            ->order("total_qty_ordered DESC")
            ->limit(5);

        return $collection;
    }
}