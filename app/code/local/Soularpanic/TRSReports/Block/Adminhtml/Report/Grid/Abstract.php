<?php
abstract class Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract
    extends Mage_Adminhtml_Block_Report_Grid_Abstract {

    public function getCollection()
    {
        if (is_null($this->_collection)) {
            $this->setCollection(Mage::getModel('trsreports/reports_grouped_collection'));
        }
        return $this->_collection;
    }

    public function getMultipleRows($item)
    {
        return null;
    }

    protected function _addCustomFilter($collection, $filterData)
    {
        $_filterData = $filterData;
        if (!$_filterData['report_code']) {
            $_filterData['report_code'] = $this->_getReportCode();
        }
        $collection->setSortKey($_filterData['sort']);
        $collection->setSortDir($_filterData['dir']);
        $collection->setCustomFilterData($_filterData);
        return $this;
    }

    protected function _prepareMassAction() {
        parent::_prepareMassaction();
        $this->setMassactionIdField('derived_sku');
        $this->getMassActionBlock()->setFormFieldName('sku');
        $reportCode = $this->_getReportCode();
        $this->getMassactionBlock()->addItem(
            'exclude',
            [ 'label' => $this->__('Exclude From Report'),
                'url' => $this->getUrl('*/admin_manage_ProductExclusions/exclude', [ 'report_code' => $reportCode ])]
        );
    }

    protected function _getReportCode() {
        return $this->getParentBlock()->getReportTag();
    }
}