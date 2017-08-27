<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
/**
 * This php file CUD's table data
 * @param tableName - name of the table
 * @param columns - columns to retrieve -- "*" if empty
 * @param where - key => value where pair(s)
 * @param orderCol -- "" if empty
 * @param orderBy -- "" if empty
 */
include(realpath(dirname(__FILE__)) . '/../../assets/php/server.php');
checkSession();
$db = new DBSql();

$query = "";
$columns = "*";
$tableName = $_POST['tableName'];
$where = "";
$whereKey = array();

if (isset($_POST['where']) && is_array($_POST['where'])) {
  foreach($_POST['where'] as $key => $value) {
    $where .= $key . " = :" . $key . "";
    $where .= " AND ";
    $whereKey[":$key"] = $value;
  }

  $where = substr($where, 0, strlen($where) - 4); // Removes last "AND "
}

if (isset($_POST['orderCol']) && isset($_POST['orderBy'])) {
  $order = "ORDER BY :orderCol :orderBy";
  // $order = "ORDER BY $_POST[orderCol] $_POST[orderBy]";
} else {
  $order = "";
}

$query = "SELECT $columns FROM $tableName WHERE $where $order";
$stmt = $db->prepare($query);
foreach($whereKey as $key => $value) {
  $stmt->bindValue($key, $value);
}

if ($order != "") {
  $stmt->bindValue(':orderCol', $_POST['orderCol']);
  $stmt->bindValue(':orderBy', $_POST['orderBy']);
}

$stmt->execute();

$json = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  array_push($json, $row);
}

echo json_encode($json);
?>
