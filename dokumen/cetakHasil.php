<?php 
include '../function/Function.php';


$baseDoc =  $_POST['baseDocJudul'];
$docs = $_POST['docsJudul'];
$hasil = $_POST['similarityJudul'];
// $hasil = explode(',', $hasil);
$docs = explode(',', $docs);
var_dump($hasil);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Perbandingan Dokumen</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/bootstrap-icons.css" rel="stylesheet">

</head>

<body onload="window.print();">
<?php 
$dokumen = "";
  echo "<table class='table table-bordered'>";
  echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";
  echo "<p>Hasil Kemiripan</p>";
  echo "<tr>
    <td>Data latih</td>
    <td>". $baseDoc ."</td>
  </tr>";
  foreach ($docs as $index => $doc) {
  echo "<tr>";
  echo "<td>Dokumen " . $index + 1  ."</td>";
  echo "<td>". highlightDynamicBackground($baseDoc, $doc) ."</td><td>". $hasil ."</td></tr>";
  } ?>

<script src="../js/bootstrap.bundle.min.js"></script>

</body>

</html>
