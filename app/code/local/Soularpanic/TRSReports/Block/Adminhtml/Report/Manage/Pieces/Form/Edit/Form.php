<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Pieces_Form_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', [
            'legend' => 'Multi-Piece Product Details',
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('entity_id', 'hidden', [
            'name' => 'entity_id'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => 'Name'
        ]);

        $fieldset->addField('pieced_product_sku', 'text', [
            'name' => 'pieced_product_sku',
            'label' => 'SKU'
        ]);

        if (Mage::registry('soularpanic_adminform_manage_product_pieces')) {
            $form->setValues(Mage::registry('soularpanic_adminform_manage_product_pieces')->getData());
        }

        return parent::_prepareForm();
    }

}