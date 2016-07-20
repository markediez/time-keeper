<?php
/**
 * This php file CUD's table data
 * @param tableName - name of the table
 * @param action - update or insert
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


foreach($_POST['values'] as $key => $value) {
  $colNames .= "$key, ";
  if ($value > 0) {
    $colValues .= "\"$value\", ";
    $update .= $key . " = " . $value . ",";
  } else {
    $colValues .= "\"$value\", ";
    $update .= $key . " = \"" . $value . "\",";
  }
}

switch($_POST['action']) {
  case 'insert':
    $query = "INSERT INTO $tableName (" . $colNames . " created_at, updated_at) VALUES (" . $colValues . " \"$timeNow\", \"$timeNow\")";
    break;
  case 'update':
    $query = "UPDATE $tableName SET $update updated_at = \"$timeNow\" WHERE id = " . $id;
    break;
  case 'delete':
    $query = "DELETE FROM $tableName WHERE id = " . $id;
    break;
}

$query = $db->escapeString($query);
$stmt = $db->prepare($query);
$result = $stmt->execute();

echo $db->lastInsertRowID();
?>
