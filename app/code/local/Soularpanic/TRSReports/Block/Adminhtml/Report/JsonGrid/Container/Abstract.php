<?php
abstract class Soularpanic_TRSReports_Block_Adminhtml_Report_JsonGrid_Container_Abstract
    extends Mage_Adminhtml_Block_Widget_Container
{

    protected $_addButtonLabel;
    protected $_backButtonLabel;
    protected $_blockGroup = 'trs';

    public function __construct()
    {

        $this->_controller = "adminhtml_report_{$this->_reportTag}";
        $this->setTemplate('report/grid/container.phtml');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

//    public function addColumn($columnId, $column)
//    {
//       $column['renderer'] =
//
//        if (is_array($column)) {
//            $this->_columns[$columnId] = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
//                ->setData($column)
//                ->setGrid($this);
//        }
//        /*elseif ($column instanceof Varien_Object) {
//            $this->_columns[$columnId] = $column;
//        }*/
//        else {
//            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
//        }
//
//        $this->_columns[$columnId]->setId($columnId);
//        $this->_lastColumnId = $columnId;
//        return $this;
//    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_JsonGrid',
                $this->_controller . '.JsonGrid')->setSaveParametersInSession(true) );
        return parent::_prepareLayout();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    protected function getAddButtonLabel()
    {
        return $this->_addButtonLabel;
    }

    protected function getBackButtonLabel()
    {
        return $this->_backButtonLabel;
    }

    protected function _addBackButton()
    {
        $this->_addButton('back', array(
            'label'     => $this->getBackButtonLabel(),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'     => 'back',
        ));
    }

    public function getHeaderCssClass()
    {
        return 'icon-head ' . parent::getHeaderCssClass();
    }

    public function getHeaderWidth()
    {
        return 'width:50%;';
    }
}
