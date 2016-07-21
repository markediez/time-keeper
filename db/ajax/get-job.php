 <?php
header('Content-Type: application/json');
ob_start();
session_start();
include('../../server.php');
include('../development/database.php');
checkSession();
$db = new DBLite();

// Redirect to time-progress.php if there is a job in progress
$query = "SELECT id FROM WorkLog WHERE user_id=:uid and end_time IS NULL OR end_time = ''";
$query = $db->escapeString($query);
$statement = $db->prepare($query);
$statement->bindValue(':uid', $_SESSION['user_id']);
$res = $statement->execute();
$row = $res->fetchArray(SQLITE3_ASSOC);
$json = array();

if($row) {
  $json['status'] = "false";
  $json['log_id'] = $row['id'];
} else {
  $json['status'] = "true";
}

// Otherwise, grab all jobs
$query = "SELECT id, title FROM Jobs WHERE user_id = :id";
$query = $db->escapeString($query);
$statement = $db->prepare($query);
$statement->bindValue(':id', $_SESSION['user_id']);
$res = $statement->execute();


while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
  array_push($json, $row);
}

echo json_encode($json);
?>
