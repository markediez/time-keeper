<?php
/**
 * This php file CUD's table data
 * @param tableName - name of the table
 * @param action - update or insert
 * @param where - where clause
 * @param id - used for updating
 * @param values - an object of 'columnName' => 'columnValues'
 */
ob_start();
session_start();
include('../../server.php');
include('../development/database.php');
checkSession();
$db = new DBLite();

$tableName = $_POST['tableName'];
$action = $_POST['action'];
$id = $_POST['id'];
$timeNow = date("Y-m-d H:i:s");

$colNames = "";
$colValues = "";
$update = "";
$where = "";


foreach($_POST['values'] as $key => $value) {
  $colNames .= "$key, ";
  $colValues .= "\"$value\", ";
  $update .= $key . " = \"" . $value . "\",";
}

foreach($_POST['where'] as $key => $value) {
  $where .= $key . " = \"" . $value . "\"";
  $where .= " AND ";
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

$query = $db->escapeString($query);
$stmt = $db->prepare($query);
$result = $stmt->execute();
echo $db->lastInsertRowID();
?>
