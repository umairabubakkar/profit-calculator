<?php
$servername = "139.59.64.115";
$username = "gsmp_dhru";
$password = "fd1c0fb00fe8b937ba476cf41c12123@1122";
$dbname = "gsmp_dhru";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
