  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$klausimo_raktas = "";
$klausimo_atsakymas = "";
$atsakymas = "";

$string2 = $_POST['tekstas'];
$string3 = $_POST['pavadinimas'];

$newfile = fopen($string3, "a");
 fwrite($newfile, $string2);
 fclose($newfile);
 echo "<center style='color:green;'>SOURCE kodas sėkmingai pridėtas prie pasirinktos paskaitos</center>";
}

?>
