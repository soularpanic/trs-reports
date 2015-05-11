<?php
class Soularpanic_TRSReports_Helper_Report_Graph_Data
    extends Mage_Core_Helper_Abstract {

    const ALL_DATA_TYPE = 'all';
    const SIMPLE_PRODUCT_DATA_TYPE = 'simple';
    const ATTR_SET_DATA_TYPE = 'attrSet';

    public function getItemsSoldData($from, $to, $granularity) {
        $itemCollectionByTime = $this->_getOrderItemCollectionByTime($from, $to, $granularity, null, self::ALL_DATA_TYPE);
        $orderCollection = $this->_getOrderCollection($from, $to);

        $itemCollectionByItem = $this->_getOrderItemCollectionByItem($from, $to)->load();

        $salesData = $this->_assembleItemsSoldData($itemCollectionByTime, $itemCollectionByItem, $orderCollection);
        return $salesData;
    }

    public function getSalesReportData($productIds = null, $attrSetIds = null, $storeIds = null, $from, $to, $granularity /*, $dataType */ ) {
        $idsArr = [];
        $salesData = [
            'meta' => ['ids' => []],
//            'actual_sold' => array(),
//            'avg_sold' => array()
        ];
        if ($productIds) {
            foreach(explode(',', $productIds) as $productId) {
                $idsArr[] = [
                    'dbId' => $productId,
                    'type' => self::SIMPLE_PRODUCT_DATA_TYPE,
                    'jsonId' => self::SIMPLE_PRODUCT_DATA_TYPE . '_' . $productId
                ];
            }
        }
        if ($attrSetIds) {
            foreach(explode(',', $attrSetIds) as $attrSetId) {
                $idsArr[] = array(
                    'dbId' => $attrSetId,
                    'type' => self::ATTR_SET_DATA_TYPE,
                    'jsonId' => self::ATTR_SET_DATA_TYPE . '_' . $attrSetId
                );
            }
        }
        if ($storeIds) {
            foreach(explode(',', $storeIds) as $storeId) {
                $idsArr[] = array(
                    'dbId' => $storeId,
                    'type' => self::ALL_DATA_TYPE,
                    'jsonId' => self::ALL_DATA_TYPE . '_' . $storeId
                );
            }
        }

        foreach ($idsArr as $idData) {
            $id = $idData['jsonId'];
            $dataType = $idData['type'];
            $dbId = $idData['dbId'];
            $_orderItems = $this->_getOrderItemCollectionByTime($from, $to, $granularity, $dbId, $dataType);

            $_soldCount = 0;

            foreach ($_orderItems as $_orderItem) {
                $date = $_orderItem['date'];
                $sold = $_orderItem['total_qty_ordered'];
                $_soldCount += $sold;
                $salesData['meta']['domain_min'] = $salesData['meta']['domain_min'] ? min($date, $salesData['meta']['domain_min']) : $date;
                $salesData['meta']['domain_max'] = $salesData['meta']['domain_max'] ? max($date, $salesData['meta']['domain_max']) : $date;
                $salesData['meta']['range_min'] = $salesData['meta']['range_min'] ? min($sold, $salesData['meta']['range_min']) : $sold;
                $salesData['meta']['range_max'] = $salesData['meta']['range_max'] ? max($sold, $salesData['meta']['range_max']) : $sold;
                $salesData[$id]['actual_sold'][] = array(
                    'date' => $date,
                    'sold' => $sold,
                    'revenue' => $_orderItem['total_index_price'],
                    'cost' => $_orderItem['total_cost'],
                    'profit' => $_orderItem['gross_index_profit']
                );
                $salesData[$id]['avg_sold'][] = array(
                    'date' => $date,
                    'sold' => $_soldCount / ($_orderItem->getElapsed() + 1)
                );
            }

            $salesData['meta']['ids'][] = $id;
            $salesData['meta'][$id]['total_sold'] = $_soldCount;
            $salesData['meta'][$id]['total_time'] = $_orderItems->getFirstItem()->getTotalTime();
            if ($dataType === self::SIMPLE_PRODUCT_DATA_TYPE) {
                $salesData['meta'][$id]['label'] = $_orderItems->getFirstItem()->getName();
            }
            if ($dataType === self::ATTR_SET_DATA_TYPE) {
                $salesData['meta'][$id]['label'] = Mage::getModel('eav/entity_attribute_set')->load($dbId)->getAttributeSetName();
            }
            if ($dataType === self::ALL_DATA_TYPE) {
                $salesData['meta'][$id]['label'] = Mage::getSingleton('adminhtml/system_store')->getStoreName($dbId);
            }
        }
        Mage::log("returning following data: ".print_r($salesData, true), null, 'trs_reports.log');

        return Mage::helper('core')->jsonEncode($salesData);

    }

    protected function _assembleItemsSoldData($itemsCollectionOverTime, $itemsCollectionOverItem, $ordersCollection) {

        $salesData = array(
            'meta' => array(),
            'data' => array()
        );

        $totalOrders = 0;
        $totalItems = 0;
        $totalValue = 0;

        $storeData = array();

        foreach ($ordersCollection as $_storeOrders) {
            $orders = $_storeOrders->getTotalOrders();
            $items = $_storeOrders->getTotalItems();
            $value = $_storeOrders->getTotalValue();

            $totalOrders += $orders;
            $totalItems += $items;
            $totalValue += $value;

            $storeData[] = array(
                'store_id' => $_storeOrders->getStoreId(),
                'store_name' => $_storeOrders->getStore()->getName(),
                'orders' => $orders,
                'items' => $items,
                'value' => $value
            );
        }

        if ($storeData) {
            $salesData['meta']['stores'] = $storeData;
            $salesData['meta']['totals'] = array(
                'orders' => $totalOrders,
                'items' => $totalItems,
                'value' => $totalValue
            );
        }

        $itemsByTime = $itemsCollectionOverTime->load()->toArray();

        foreach($itemsByTime['items'] as $item) {
            $date = $item['date'];
            $sold = $item['total_qty_ordered'];
            $salesData['meta']['domain_min'] = $salesData['meta']['domain_min'] ? min($date, $salesData['meta']['domain_min']) : $date;
            $salesData['meta']['domain_max'] = $salesData['meta']['domain_max'] ? max($date, $salesData['meta']['domain_max']) : $date;
            $salesData['meta']['range_min'] = $salesData['meta']['range_min'] ? min($sold, $salesData['meta']['range_min']) : $sold;
            $salesData['meta']['range_max'] = $salesData['meta']['range_max'] ? max($sold, $salesData['meta']['range_max']) : $sold;
            $salesData['data'][] = array(
                'date' => $date,
                'sold' => $sold
            );
        }

        $topSellers = array();
        foreach ($itemsCollectionOverItem as $item) {
            $topSellers[] = array(
                'name' => $item->getName(),
                'id' => $item->getProductId(),
                'qty' => $item->getTotalQtyOrdered()
            );
        }
        if ($topSellers) {
            $salesData['meta']['top_sellers'] = $topSellers;
        }

        return Mage::helper('core')->jsonEncode($salesData);
    }

    protected function _getOrderCollection($from, $to) {
        $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToFilter('created_at',
                array('from' => $from,
                    'to' => $to))
            ->addAttributeToFilter('protect_code', array('notnull' => true));

        $collection->getSelect()
            ->group("store_id")
            ->columns(array('total_orders' => 'count(*)',
                'total_items' => 'sum(total_qty_ordered)',
                'total_value' => 'sum(base_grand_total)'));
        return $collection;
    }

    protected function _getOrderItemCollectionByTime($from, $to, $granularity, $id = null, $dataType = self::ALL_DATA_TYPE) {
        $granularityDateForm = $this->_parseGranularity($granularity);
        $idFilter = $id === null ? array('notnull' => true) : array('eq' => $id);

        $addlCols = array('total_qty_ordered' => "sum(qty_ordered)",
            'date' => "DATE_FORMAT(main_table.created_at, '$granularityDateForm')");
        if ($id !== null) {
            $addlCols = array_merge($addlCols, array(
                'total_time' => "TIMESTAMPDIFF($granularity, '$from', '$to')",
                'elapsed' => "TIMESTAMPDIFF($granularity, '$from', DATE_FORMAT(main_table.created_at, '%Y-%m-%d'))"
            ));
        }

        $collection = Mage::getModel('sales/order_item')
            ->getCollection()
            ->addAttributeToFilter('main_table.created_at',
                array('from' => $from,
                    'to' => $to));

        if ($dataType === self::SIMPLE_PRODUCT_DATA_TYPE) {
            $collection->addAttributeToFilter('product_id', $idFilter)
                ->addFilter('product_type', 'simple');
            $collection->getSelect()
                ->joinLeft(array('pps' => 'purchase_product_supplier'),
                    'main_table.product_id = pps.pps_product_id',
                    array('cost' => 'pps_last_price',
                        'total_cost' => '(pps_last_price * sum(qty_ordered))'))
                ->joinLeft(array('cpip' => 'catalog_product_index_price'),
                    'main_table.product_id = cpip.entity_id and cpip.customer_group_id = 0 and website_id = 1',
                    array('index_price' => 'price',
                        'total_index_price' => '(cpip.price * sum(qty_ordered))',
                        'gross_index_profit' => '((cpip.price - pps_last_price) * sum(qty_ordered))'));
        }
        if ($dataType === self::ATTR_SET_DATA_TYPE) {
            $collection->getSelect()
                ->joinLeft(array('cpe' => 'catalog_product_entity'),
                    'main_table.product_id = cpe.entity_id', // or main_table.parent_item_id = cpe.entity_id',
                    array())
                ->where("cpe.attribute_set_id = $id")
                ->where("product_type = 'simple'");
        }
        if ($dataType === self::ALL_DATA_TYPE && $id !== null) {
            $collection->getSelect()
                ->where("store_id = {$id}")
                ->where("product_type = 'simple'");
        }

        $collection->getSelect()
            ->group("DATE_FORMAT(main_table.created_at, '$granularityDateForm')")
            ->columns($addlCols, "main_table");

        Mage::log("_getOrderItemCollectionByTime:\n\tparams:\n\t\tfrom: $from\n\t\tto: $to\n\t\tgranularity: $granularity\n\t\tproductId: $id\n\t\tonlySimple: $dataType\n\tsql:\n\t".$collection->getSelect()->__toString(),
            null, 'trs_reports.log');

        return $collection;
    }

    protected function _getOrderItemCollectionByItem($from, $to) {
        $collection = Mage::getModel('sales/order_item')
            ->getCollection()
            ->addAttributeToFilter('created_at',
                array('from' => $from,
                    'to' => $to))
            ->addAttributeToFilter('product_id', array('notnull' => true))
            ->addFilter('product_type', 'simple');
        $collection->getSelect()
            ->group("product_id")
            ->columns(array('total_qty_ordered' => "sum(qty_ordered)"),
                "main_table")
            ->limit(10)
            ->order('total_qty_ordered DESC');
        return $collection;
    }



    protected function _parseGranularity($granularity) {
        switch($granularity) {
            case 'month':
                return '%Y-%m';
            case 'week':
                return '%Y-%u';
            case 'day':
                return '%Y-%m-%d';
            case 'hour':
                return '%Y-%m-%d %H';
            default:
                Mage::throwException("Could not parse granularity value of \"$granularity\"");
        }
    }
}
