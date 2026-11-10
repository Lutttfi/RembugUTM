<<<<<<< HEAD
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
=======

<?php
$host = "localhost";
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
>>>>>>> f4901358c7d1aa90da203b34ec3f90591e3347bb
