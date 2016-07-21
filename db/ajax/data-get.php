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
header('Content-Type: application/json');
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

$json = array();
while ($row = $result->fetchArray()) {
  array_push($json, $row);
}

echo json_encode($json);
?>
