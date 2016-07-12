<?php
/**
 * This php file CUD's table data
 * @param tableName - name of the table
 * @param columns - columns to retrieve -- "*" if empty
 * @param where - key => value where pair(s)
 * @param orderCol -- "" if empty
 * @param orderBy -- "" if empty
 */
ob_start();
session_start();
include('../../server.php');
include('../development/database.php');
checkSession();
$db = new DBLite();

$query = "";
$columns = "*";
$tableName = $_POST['tableName'];
$where = "";

foreach($_POST['where'] as $key => $value) {
  $where .= " AND $key = \"$value\"";
}

if (isset($_POST['orderCol']) && isset($_POST['orderBy'])) {
  $order = "ORDER BY $_POST[orderCol] $_POST[orderBy]";
} else {
  $order = "";
}

$query = "SELECT $columns FROM $tableName WHERE 1=1 $where $order";

$query = $db->escapeString($query);
$stmt = $db->prepare($query);
$result = $stmt->execute();

$json = "{";
$jsonIndex = 0;
while ($row = $result->fetchArray()) {
  $json .= $jsonIndex . ": {";
  foreach($row as $key => $value) {
    $json .= "'$key': '$value',"
  }
  $json .= "},";
  $jsonIndex++;
}
$json .= "}";

echo $json;
?>
