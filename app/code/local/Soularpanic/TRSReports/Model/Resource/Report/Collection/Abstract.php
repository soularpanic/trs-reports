<?php
abstract class Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract
    extends Mage_Sales_Model_Resource_Report_Collection_Abstract {

    protected $_sort = null;
    protected $_sortDir = null;
    protected $_defaultSort = null;
    protected $_productTable = null;
    protected $_selects = null;
    protected $_customFilterData = null;

    public function __construct() {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());

        $this->setProductTable($this->getTable('catalog/product'));
    }

    public function setCustomFilterData($data) {
        $this->_customFilterData = $data;
    }

    public function getCustomFilterData() {
        if ($this->_customFilterData === null) {
            return new Varien_Object();
        }
        return $this->_customFilterData;
    }

    protected function _getMode($mode = null) {
        if (is_null($mode)) {
            $mode = 'default';
            if ($this->isTotals()) {
                $mode = 'total';
            }
            if ($this->isSubTotals()) {
                $mode = 'subtotal';
            }
        }
        return $mode;
    }

    protected function _getSelectCols(array $cols, $mode = null) {
        $mode = $this->_getMode($mode);

        $toReturn = array();
        foreach ($cols as $col) {
            if (array_key_exists($col, $this->_selects)) {
                $colSelects = $this->_selects[$col];
                if (!is_array($colSelects)) {
                    $toReturn[$col] = $colSelects;
                }
                elseif (array_key_exists($mode, $colSelects)) {
                    $toReturn[$col] = $colSelects[$mode];
                }
                elseif (array_key_exists('default', $colSelects)) {
                    $toReturn[$col] = $colSelects['default'];
                }
                else {
                    $toReturn[$col] = $col;
                }
            }
            else {
                $toReturn[$col] = $col;
            }
        }
        return $toReturn;
    }

    public function setSortKey($sortKey) {
        $this->_sort = $sortKey;
    }

    public function setSortDir($sortDir) {
        $this->_sortDir = $sortDir;
    }

    public function getProductTable() {
        return $this->_productTable;
    }

    public function setProductTable($productTable) {
        $this->_productTable = $productTable;
    }

    public function log($message) {
        Mage::helper('trsreports')->log($message);
    }

    protected function _applyCustomFilter() {
        $_field = $this->_sort === null ? $this->_defaultSort : $this->_sort;
        $_dir = $this->_sortDir ? $this->_sortDir : 'ASC';
        if ($_field !== null) {
            $this->getSelect()->order(array("{$_field} {$_dir}"));
        }

        $_customFilterData = $this->getCustomFilterData();
        if ($_customFilterData && $_customFilterData['report_code']) {
            $this->_applyTrsExclusions($_customFilterData['report_code'],
                $_customFilterData['product_table']);
        }

        return parent::_applyCustomFilter();
    }

    protected function _applyTrsExclusions($reportCode, $productTable = null) {
        $_productTable = $productTable ?: $this->getProductTable();
        $exclusionTable = $this->getTable('trsreports/excludedproduct');
        $this->getSelect()
            ->where("$_productTable.entity_id not in (
                SELECT product_id
                FROM $exclusionTable
                WHERE report_id = '$reportCode')");
    }
}