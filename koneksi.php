<?php
$host = "localhost";
<<<<<<< HEAD
$username = "root";
$password = "";
$dbname = "forum_db";

$connect = mysqli_connect($host, $username, $password, $dbname);

if(!$connect){
	echo "GAGAL KONEKSI KE DATABASE";
}
?>

=======
$user = "root";
$password = "";
$database = "forum_db";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
>>>>>>> 2f8439f44c9ac5621d54ebe4f05318bb34c2fbd9
