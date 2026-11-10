<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "tugas";

$connect = mysqli_connect($host, $username, $password, $dbname);

if(!$connect){
	echo "GAGAL KONEKSI KE DATABASE";
}
?>