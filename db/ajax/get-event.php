<?php
ob_start();
session_start();
header('Content-Type: application/json');
include('../../server.php');
include('../development/database.php');
checkSession();
$db = new DBLite();

$jid = $_REQUEST["jid"];
$date = $_REQUEST["date"];
$start = new DateTime($date);
$end = new DateTime($date);
$start->setTime(0,0,0);
$end->setTime(23,59,59);

$start = $start->format("Y-m-d H:i:s");
$end = $end->format("Y-m-d H:i:s");

$query = "SELECT Jobs.title as job_title, WorkLog.title as work_title, WorkLog.start_time as work_start, WorkLog.end_time as work_end, Entries.entry FROM Jobs
          LEFT JOIN WorkLog ON Jobs.id = WorkLog.job_id
          LEFT JOIN Entries ON WorkLog.id = Entries.log_id
          WHERE Jobs.id = $jid AND WorkLog.start_time BETWEEN \"$start\" AND \"$end\"";

$query = $db->escapeString($query);
$stmt = $db->prepare($query);
$res = $stmt->execute();
$json = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
  array_push($json, $row);
}

echo json_encode($json);
?>
