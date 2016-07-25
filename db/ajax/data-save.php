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
 * @param action - update or insert or delete
 * @param where - where clause
 * @param values - an object of 'columnName' => 'columnValues'
 */
ob_start();
session_start();
include('../../server.php');
checkSession();
$db = new DBSql();

$tableName = $_POST['tableName'];
$action = $_POST['action'];
$timeNow = date("Y-m-d H:i:s");

$colNames = "";
$colValues = "";
$update = "";
$where = "";

$values = array();
$whereKey = array();

foreach($_POST['values'] as $key => $value) {
  $colNames .= "$key, ";
  $colValues .= ":$key, ";
  $update .= $key . " = :" . $key . ",";
  $values[":$key"] = $value;
}

if (isset($_POST['where']) && is_array($_POST['where'])) {
  foreach($_POST['where'] as $key => $value) {
    $where .= $key . " = :" . $key . "";
    $where .= " AND ";
    $whereKey[":$key"] = $value;
  }
}

$where = substr($where, 0, strlen($where) - 4); // Removes last "AND "

switch($_POST['action']) {
  case 'insert':
    $query = "INSERT INTO $tableName (" . $colNames . " created_at, updated_at) VALUES (" . $colValues . " \"$timeNow\", \"$timeNow\")";
    break;
  case 'update':
    $query = "UPDATE $tableName SET $update updated_at = \"$timeNow\" WHERE $where";
    break;
  case 'delete':
    $query = "DELETE FROM $tableName WHERE $where";
    break;
}

$stmt = $db->prepare($query);
foreach($values as $key => $value) {
  $stmt->bindValue($key, $value);
}

foreach($whereKey as $key => $value) {
  $stmt->bindValue($key, $value);
}

$stmt->execute();
echo $db->lastInsertId();
?>
