<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValue_Form
    extends Mage_Adminhtml_Block_Report_Filter_Form {

    protected function _prepareForm() {
        parent::_prepareForm();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField('show_zero_remaining_delivery', 'select', array(
            'name'      => 'show_zero_remaining_delivery',
            'options'   => array(
                '1' => Mage::helper('reports')->__('Yes'),
                '0' => Mage::helper('reports')->__('No')
            ),
            'label'     => Mage::helper('reports')->__('Show Zero Remaining Deliveries'),
            'title'     => Mage::helper('reports')->__('Show Zero Remaining Deliveries')
        ));
    }
}