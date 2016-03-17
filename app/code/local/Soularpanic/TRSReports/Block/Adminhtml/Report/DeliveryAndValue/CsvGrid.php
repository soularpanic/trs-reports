<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValue_CsvGrid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValue_Grid {

    protected function _prepareColumns() {
        parent::_prepareColumns();

        $this->removeColumn('delivery_details');
        $this->removeColumn('payment_details');

        return $this;
    }

}