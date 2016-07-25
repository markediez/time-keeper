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
include('../../server.php');
checkSession();
$db = new DBSql();

// Redirect to time-progress.php if there is a job in progress
$query = "SELECT id FROM WorkLog WHERE user_id=:uid and end_time IS NULL OR end_time = ''";
$statement = $db->prepare($query);
$statement->bindValue(':uid', $_SESSION['user_id']);
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);
$json = array();

if($row) {
  $json['status'] = "false";
  $json['log_id'] = $row['id'];
} else {
  $json['status'] = "true";
}

// Otherwise, grab all jobs
$query = "SELECT id, title FROM Jobs WHERE user_id = :id";
$statement = $db->prepare($query);
$statement->bindValue(':id', $_SESSION['user_id']);
$statement->execute();


while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
  array_push($json, $row);
}

echo json_encode($json);
?>
