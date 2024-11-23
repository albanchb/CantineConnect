<?php
$servername = "votrehost";
$username = "votreusername";
$password = "votrepassword";
$dbname = "votredbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
