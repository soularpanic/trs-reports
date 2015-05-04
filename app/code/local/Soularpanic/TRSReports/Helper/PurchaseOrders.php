<?php
class Soularpanic_TRSReports_Helper_PurchaseOrders
    extends Soularpanic_TRSReports_Helper_Data {

    public function decodePurchaseOrderData($purchaseOrdersString) {
        $purchaseOrders = new Varien_Data_Collection();

        if (!$purchaseOrdersString) {
            return $purchaseOrders;
        }

        $poStrArr = explode(',', $purchaseOrdersString);
        foreach ($poStrArr as $idx => $poStr) {
            $poArr = [];
            list($poArr['po_id'],
                $poArr['supplier_name'],
                $poArr['po_code'],
                $poArr['po_count_arrived'],
                $poArr['po_count_total'],
                $poArr['po_expected_arrival']) = explode('::', $poStr);
            $purchaseOrders->addItem(new Varien_Object($poArr));
        }
        return $purchaseOrders;
    }


    public function getPurchaseOrdersByProductSql() {
        return "(select
                    po_data.pps_product_id as product_id
                    , sum(po_data.pop_qty) - sum(po_data.pop_supplied_qty) as incoming_qty
                    , concat_ws(',', po_data.po_string) as encoded_pos
                    , concat_ws(', ', po_data.sup_name) as suppliers
                from(select
                    pps.pps_product_id
                    , pop.pop_supplied_qty
                    , pop.pop_qty
                    , ps.sup_name
                    , concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date) as po_string
                    from purchase_product_supplier as pps
                        left join purchase_supplier as ps
                            on pps.pps_supplier_num = ps.sup_id
                        left join purchase_order_product as pop
                            on pop.pop_product_id = pps.pps_product_id
                                and pop.pop_supplied_qty < pop.pop_qty
                        left join purchase_order as po
                            on po.po_num = pop.pop_order_num
                                and po.po_status in('new','waiting_for_delivery')
                    where po.po_num is not null) as po_data
                group by po_data.pps_product_id)";
    }
}