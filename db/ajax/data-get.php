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
ob_start();
session_start();
header('Content-Type: application/json');
include('../../server.php');
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
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
  array_push($json, $row);
}

echo json_encode($json);
?>
