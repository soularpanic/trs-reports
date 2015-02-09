<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Graph
    extends Mage_Adminhtml_Block_Widget {

    public function getGraphId() {
        $id = $this->_getData("graph_id");
        if (!$id) {
            $id = "graph";
        }
        return $id;
    }

    public function getGraphWidth() {
        $width = $this->_getData("graph_width");
        if (!$width) {
            $width = 300;
        }
        return $width;
    }

    public function getGraphHeight() {
        $height = $this->_getData('graph_height');
        if (!$height) {
            $height = 225;
        }
        return $height;
    }

    public function getGraphClass() {
        $class = $this->_getData('graph_class');
        if (!$class) {
            $class = 'trsGraph';
        }
        return $class;
    }
}