<?php
/*
Copyright (c) 2016 Mark Diez

This file is part of Time Keeper

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
ob_start();
session_start();
header('Content-Type: application/json');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/php/server.php');
checkSession();
$db = new DBSql();

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
          WHERE Jobs.id = $jid AND WorkLog.start_time BETWEEN :start AND :end";

$stmt = $db->prepare($query);
$stmt->bindValue(':start', $start);
$stmt->bindValue(':end', $end);
$stmt->execute();
$json = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  array_push($json, $row);
}

echo json_encode($json);
?>
