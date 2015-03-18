<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_ProductLine_Membership_List
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $membersStr = $row->getData('product_line_members');
        $memberStrs = explode(',', $membersStr);

        $lis = "";
        foreach ($memberStrs as $memberStr) {
            list($id, $name) = explode('::', $memberStr);
            $url = Mage::helper('adminhtml')->getUrl('*/catalog_product/edit', ['id' => $id]);
            $lis.="<li><a href=\"{$url}\">$name</a></li>\n";
        }
        return "<ul>$lis</ul>";
    }

}