<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "forum_db";

$connect = mysqli_connect($host, $username, $password, $dbname);

if(!$connect){
	echo "GAGAL KONEKSI KE DATABASE";
}
?>
