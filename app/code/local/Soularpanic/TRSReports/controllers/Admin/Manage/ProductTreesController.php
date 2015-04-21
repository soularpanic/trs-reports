<?php
class Soularpanic_TRSReports_Admin_Manage_ProductTreesController
    extends Soularpanic_TRSReports_Controller_Abstract {

    const PRODUCT           = 'product';
    const PRODUCT_TREE      = 'tree';
    const PRODUCT_TREE_NODE = 'node';

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newSubmitAction() {
        $desiredName = $this->getRequest()->getParam('name');
        $existingTrees = Mage::getModel('trsreports/product_tree')->getCollection()
            ->addFieldToFilter('name', $desiredName)
            ->load();
        if ($existingTrees->count()) {
            $this->getResponse()
                ->setHttpResponseCode(409)
                ->setBody(json_encode(['message' => "There is already a Product Tree named '$desiredName.'"]));
            return;
        }
        else {
            $tree = Mage::getModel('trsreports/product_tree')
                ->setName($desiredName)
                ->save();

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setBody(json_encode([
                    'message' => "'$desiredName' was created successfully!",
                    'id' => $tree->getId()
                ]));
        }
    }

    public function editAction() {
        $this->loadLayout();

        $treeId = $this->getRequest()->getParam('id');
        $tree = Mage::getModel('trsreports/product_tree')->load($treeId);
        Mage::register('soularpanic_adminform_manage_product_trees', $tree);

        $this->renderLayout();
    }

    public function saveAction() {
        $req = $this->getRequest();
        $id = $req->getParam('entity_id');

        $tree = Mage::getModel('trsreports/product_tree')->load($id);
        $tree->setData($req->getParams());
        $tree->save();
        $this->_redirectReferer();
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $tree = Mage::getModel('trsreports/product_tree')->load($id);
        $tree->delete();

        $this->_redirect('*/*/index');
    }

    public function fetchProductTreesAction() {
        $nodeReq = $this->getRequest()->getParams('node');
        $nodeId = $nodeReq['node'];
        $data = [];
        if ($nodeId === 'source' || !$nodeId) {
            $trees = Mage::getModel('trsreports/product_tree')->getCollection();

            foreach ($trees as $tree) {
                $data[] = [
                    'text'  => $tree->getName(),
                    'id'    => self::PRODUCT_TREE.'-'.$tree->getId(),
                    'cls'   => 'folder'
                ];
            }
        }
        else {
            list($type, $id) = explode('-', $nodeId);

            if ($type === self::PRODUCT_TREE) { // get root nodes for this tree
                $roots = Mage::getModel('trsreports/product_tree')->load($id)->getRootNodes();
                foreach ($roots as $root) {
                    $data[] = [
                        'text' => $root->getProductName(),
                        'id' => self::PRODUCT_TREE_NODE.'-'.$root->getId(),
                        'cls' => 'folder'
                    ];
                }
            }
            if ($type === self::PRODUCT_TREE_NODE) {
                $node = Mage::getModel('trsreports/product_tree_node')->load($id);
                foreach ($node->getChildren() as $child) {
                    $data[] = $this->_assembleNodeDescendants($child);
                }
            }
        }
        $this->getResponse()->setBody(json_encode($data));
    }

    public function addToProductTreeAction() {
        $req = $this->getRequest();
        $sourceId = $req->getParam('sourceId');
        $sourceType = $req->getParam('sourceType');

        $targetId = $req->getParam('targetId');
        $targetType = $req->getParam('targetType');

        list($sourceType, $sourceId) = $sourceType ? [$sourceType, $sourceId] : explode('-', $sourceId);
        list($targetType, $targetId) = $targetType ? [$targetType, $targetId] : explode('-', $targetId);

        // set product as new root of tree
        if ($sourceType == self::PRODUCT && $targetType == self::PRODUCT_TREE) {
            $roots = Mage::getModel('trsreports/product_tree')->load($targetId)->getRootNodes();
            $supplanter = Mage::getModel('trsreports/product_tree_node');
            $supplanter->setData([
                'tree_id' => $targetId,
                'product_id' => $sourceId
            ]);
            $supplanter = $supplanter->save();

            foreach ($roots as $root) {
                if ($root->getId() === $supplanter->getId()) {
                    continue;
                }
                $root->setParentNodeId($supplanter->getId());
                $root->save();
            }
        }

        if ($sourceType == self::PRODUCT && $targetType == self::PRODUCT_TREE_NODE) {
            $node = Mage::getModel('trsreports/product_tree_node')->load($targetId);
            $child = Mage::getModel('trsreports/product_tree_node');
            $child->setData([
                'product_id' => $sourceId,
                'parent_node_id' => $node->getId(),
                'tree_id' => $node->getTreeId()
            ]);
            $child->save();
        }

        if ($sourceType == self::PRODUCT_TREE_NODE && $targetType == self::PRODUCT_TREE_NODE) {
            $sourceNode = Mage::getModel('trsreports/product_tree_node')->load($sourceId);
            $targetNode = Mage::getModel('trsreports/product_tree_node')->load($targetId);

            $sourceNode->setParentNodeId($targetNode->getId());

            // if we're moving to a new tree, we need to update all child tree ids
            if ($sourceNode->getTreeId() !== $targetNode->getTreeId()) {
                $this->_updateAllDescendants($sourceNode, ['tree_id' => $targetNode->getTreeId()]);
            }
        }
    }

    protected function _updateAllDescendants($node, $newDataArr) {
        foreach ($newDataArr as $key => $val) {
            $node->setData($key, $val);
            $node->save();
        }
        foreach ($node->getChildren() as $child) {
            $this->_updateAllDescendants($child, $newDataArr);
        }
    }

    protected function _assembleNodeDescendants($parentNode) {
        $data = [
            'text' => $parentNode->getProductName(),
            'id' => self::PRODUCT_TREE_NODE.'-'.$parentNode->getId(),
            'cls' => 'folder'
        ];
        $children = $parentNode->getChildren();
        if ($children->count()) {
            $data['children'] = [];
            foreach ($children as $child) {
                $data['children'][] = $this->_assembleNodeDescendants($child);
            }
        }

        return $data;
    }
}