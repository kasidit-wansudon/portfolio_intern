<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "file_management";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

define('HOST','http://192.168.1.106/GitHub/portfolio_intern/');

?>
