<?php
session_start();
include "../datacon.php";

$dep_id = $_SESSION['dep_id'];

$res = $conn->query("SELECT id, first_name, last_name FROM supervisors WHERE dep_id='$dep_id'");

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

echo json_encode($rows);
?>
