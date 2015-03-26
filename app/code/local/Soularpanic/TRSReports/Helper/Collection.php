<?php
class Soularpanic_TRSReports_Helper_Collection
    extends Soularpanic_TRSReports_Helper_Data {

    public function getTableAlias($select, $tableName) {
        $tables = $select->getPart('from');
        $map = function($arr) { return $arr['tableName']; };
        $_tables = array_map($map, $tables);
        $alias = array_search($tableName, $_tables);

        return $alias;
    }

}