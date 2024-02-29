<?php 
include '../function/Function.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $judulSkripsi = $_POST["judul"];
  $abstrakSkripsi = $_POST["abstrak"];

  //preprocessing judul
  $logJudul = array();
  $logJudul = normalizeText($judulSkripsi);
  $logJudul = tokenizeBySpace($logJudul);
  $logJudul = stopwordRemover($logJudul);
  $logJudul = stemming($logJudul);

  //preprocessing abstrak
  $logAbstrak = array();
  $logAbstrak = normalizeText($abstrakSkripsi);
  $logAbstrak = tokenizeBySpace($logAbstrak);
  $logAbstrak = stopwordRemover($logAbstrak);
  $logAbstrak = stemming($logAbstrak);

  // Memanggil fungsi analisis kemiripan
  $hasilAnalisis = calculateAllTerm($logJudul, $logAbstrak);
  for ($i=0; $i < count($hasilAnalisis['tfidf'][0]); $i++) { 
    $datasetJudul[] = array(
        'id' => $i,
        'data' => $hasilAnalisis['tfidf'][0][$i]
    );
    $datasetAbstrak[] = array(
        'id' => $i,
        'data' => $hasilAnalisis['tfidf'][1][$i]
    );
  }
}



$result =  calculateAllSimilarity($datasetJudul, $datasetAbstrak, end($datasetJudul), end($datasetAbstrak));
// Menggunakan contoh koneksi ke database yang sudah ada
$connection = Connection();
if ($connection->connect_error) {
    die("Koneksi ke database gagal: " . $connection->connect_error);
}

// Mengambil data dari database
$data = getDataFromDatabase($connection);
foreach ($data as $row) {
  $corpus_judul[] = $row['judulAsli'];
  $corpus_abstrak[] = $row['abstrakAsli'];
}


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="description" content="">
  <meta name="author" content="">

  <title>Analisis Kemiripan Skripsi</title>

  <!-- CSS FILES -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap"
    rel="stylesheet">
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/bootstrap-icons.css" rel="stylesheet">
  <link href="../css/templatemo-topic-listing.css" rel="stylesheet">
  <style>
  #myDIV,
  #tfJudul,
  #idfJudul,
  #tfidfJudul,
  #tfAbstrak,
  #idfAbstrak,
  #tfidfAbstrak {
    display: none;
  }


  @media print {
    .hero-section {
        background-image: none;
    }
    /* Properti CSS tambahan yang ingin Anda ubah saat mencetak */
    .text-white, 
    .text-center {
                color: #000 !important; /* Ganti warna teks putih menjadi hitam */
            }

    .no-print {
        display: none; /* Misalnya, elemen dengan kelas 'no-print' akan disembunyikan saat mencetak */
    }
  }

  </style>
</head>

<body id="top" onload="window.print();">
  <main>
    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
      <div class="container">
        <div class="row">
          <div class="col-lg-10 col-12 mx-auto">
            <h1 class="text-white text-center">Hasil Perhitungan</h1>
            <button type="button" class="btn btn-success no-print" onclick="hideOrShowElement('myDIV')">Lihat
              Perhitungan</button>

            <div id="myDIV">
              <?php 
                if(!empty($_POST["judul"])){
                   ?>
              <button type="button" onclick="toggleVisibility('tfJudul')">TF Judul</button>
              <div id="tfJudul">
                <?php
                  //tf untuk judul
                  //mengambil data akhir (data latih)
                  $latihJudul = end($hasilAnalisis['tf'][0]);
                  // menghapus data latih
                  array_pop($hasilAnalisis['tf'][0]);
              
                  $dokumen = "";
                  echo "<h3 class='text-center'>TF judul</h3>";
                  echo "<table class='table table-bordered'>";
                  echo "<tr><th>Dokumen</th><th>Term</th><th>TF Value</th></tr>";
                  $dokumen .= "<td  rowspan=" . count($latihJudul) .  " >Data latih</td>";
                  foreach ($latihJudul as $i => $document) {
                    echo "<tr >";
                    echo $dokumen;
                    echo "<td >". $i ."</td><td>". $document ."</td></tr>";
                    $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
              
                  }
              
                  $dokumen = "";
                  foreach ($hasilAnalisis['tf'][0] as $i => $document) {
                    $dokumen .= "<td rowspan=" . count($document) .  " >Dokumen " . ($i + 1) . "</td>";
                    foreach ($document as $term => $tfidfValue) {
                      echo "<tr>";
                      echo $dokumen;
                      echo "<td >" . $term . "</td>";
                      echo "<td >" . $tfidfValue . "</td>";
                      echo "</tr>";
                      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
                    }
                  }
                  echo "</table>";
                  echo "<br/>";
                  ?>
              </div>
              <button type="button" onclick="toggleVisibility('idfJudul')">IDF Judul</button>
              <div id="idfJudul">
                <?php 
                  // ---------------------------------------------------------------------------------------------------------------------
                  //idf untuk judul
                  // Menampilkan  idf judul
                  echo "<h3 class='text-center'>IDF judul</h3>";
                  echo "<table class='table table-bordered'>";
                  echo "<tr ><th>Term</th><th>IDF Value</th><th>DF Value</th></tr>";
              
                  //urutkan hasil bersararkan key abjad
                  $urutDf[] = $hasilAnalisis['df'][0];
                  $urutIdf[] = $hasilAnalisis['idf'][0];
              
                  $dfIdf = array_merge_recursive($urutDf, $urutIdf);
                  //hitung baris maksimal
                  $maxCount = max(count($dfIdf[0]), count($dfIdf[1]));
                  for ($i = 0; $i < $maxCount; $i++) {
                    echo "<tr >";
                    echo "<td >";
                        echo key($dfIdf[1]);
                    echo "</td>";
                    echo "<td >";
                        echo current($dfIdf[1]);
                        next($dfIdf[1]);
                    echo "</td>";
                    echo "<td >";
                    if (array_key_exists($i, $dfIdf[0])) {
                        echo $dfIdf[0][$i];
                    }
                    echo "</td>";
                    echo "</tr>";
                  }
              
                  echo "</table>";
                  echo "<br/>";
               ?>
              </div>
              <button type="button" onclick="toggleVisibility('tfidfJudul')">TF-IDF Judul</button>
              <div id="tfidfJudul">
                <?php 
                  // ----------------------------------------------------------------------------------------------------------------------------------
                  //tf idf untuk judul
                  //mengambil data akhir (data latih)
                  $latihJudul = end($hasilAnalisis['tfidf'][0]);
                  // menghapus data latih
                  array_pop($hasilAnalisis['tfidf'][0]);
              
                  $dokumen = "";
                  echo "<h3 class='text-center'>TF-IDF Judul</h3>";
                  echo "<table class='table table-bordered'>";
                  echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";
                  $dokumen .= "<td  rowspan=" . count($latihJudul) .  ">Data latih</td>";
                  foreach ($latihJudul as $i => $document) {
                    echo "<tr>";
                    echo $dokumen;
                    echo "<td>". $i ."</td><td>". $document ."</td></tr>";
                    $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
              
                  }
              
                  $dokumen = "";
                  foreach ($hasilAnalisis['tfidf'][0] as $i => $document) {
                    $dokumen .= "<td rowspan=" . count($document) .  ">Dokumen " . ($i + 1) . "</td>";
                    foreach ($document as $term => $tfidfValue) {
                      echo "<tr>";
                      echo $dokumen;
                      echo "<td>" . $term . "</td>";
                      echo "<td>" . $tfidfValue . "</td>";
                      echo "</tr>";
                      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
                    }
                  }
                  echo "</table>";
                  ?>
              </div>
              <?php 
                }
              ?>
              <?php 
                if(!empty($_POST["abstrak"])){
                ?>
              <button type="button" onclick="toggleVisibility('tfAbstrak')">TF Abstrak</button>
              <div id="tfAbstrak">
                <?php 
                  //tf untuk abstrak
                  //mengambil data akhir (data latih)
                  $latihJudul = end($hasilAnalisis['tf'][1]);
                  // menghapus data latih
                  array_pop($hasilAnalisis['tf'][1]);
              
                  $dokumen = "";
                  echo "<h3 class='text-center'>TF Abstrak</h3>";
                  echo "<table class='table table-bordered'>";
                  echo "<tr><th>Dokumen</th><th>Term</th><th>TF Value</th></tr>";
                  $dokumen .= "<td  rowspan=" . count($latihJudul) .  " >Data latih</td>";
                  foreach ($latihJudul as $i => $document) {
                    echo "<tr >";
                    echo $dokumen;
                    echo "<td >". $i ."</td><td>". $document ."</td></tr>";
                    $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
              
                  }
              
                  $dokumen = "";
                  foreach ($hasilAnalisis['tf'][1] as $i => $document) {
                    $dokumen .= "<td rowspan=" . count($document) .  " >Dokumen " . ($i + 1) . "</td>";
                    foreach ($document as $term => $tfidfValue) {
                      echo "<tr>";
                      echo $dokumen;
                      echo "<td >" . $term . "</td>";
                      echo "<td >" . $tfidfValue . "</td>";
                      echo "</tr>";
                      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
                    }
                  }
                  echo "</table>";
                  echo "<br/>";
              ?>
              </div>
              <button type="button" onclick="toggleVisibility('idfAbstrak')">IDF Abstrak</button>
              <div id="idfAbstrak">
                <?php 
              
                  // ---------------------------------------------------------------------------------------------------------------------
                  //idf untuk Abstrak
                  // Menampilkan  idf Abstrak
                  echo "<h3 class='text-center'>IDF Abstrak</h3>";
                  echo "<table class='table table-bordered'>";
                  echo "<tr ><th>Term</th><th>IDF Value</th><th>DF Value</th></tr>";
              
                  //urutkan hasil bersararkan key abjad
                  $urutDfAbstrak[] = $hasilAnalisis['df'][1];
                  $urutIdfAbstrak[] = $hasilAnalisis['idf'][1];
                  $dfIdfAbstrak = array_merge_recursive($urutDfAbstrak, $urutIdfAbstrak);
              
                  //hitung baris maksimal
                  $maxCount = max(count($dfIdfAbstrak[0]), count($dfIdfAbstrak[1]));
                  for ($i = 0; $i < $maxCount; $i++) {
                    echo "<tr>";
                    echo "<td>";
                        echo key($dfIdfAbstrak[1]);
                    echo "</td>";
                    echo "<td>";
                        echo current($dfIdfAbstrak[1]);
                        next($dfIdfAbstrak[1]);
                    echo "</td>";
                    echo "<td>";
                    if (array_key_exists($i, $dfIdfAbstrak[0])) {
                        echo $dfIdfAbstrak[0][$i];
                    }
                    echo "</td>";
                    echo "</tr>";
                  }
              
                  echo "</table>";
                  echo "<br/>";
              ?>
              </div>
              <button type="button" onclick="toggleVisibility('tfidfAbstrak')">TF-IDF Abstrak</button>
              <div id="tfidfAbstrak">
                <?php 
              
                  // ------------------------------------------
                  //tf idf untuk Abstrak
                  //mengambil data akhir (data latih)
                  $latihAbstrak = end($hasilAnalisis['tfidf'][1]);
                  // menghapus data latih
                  array_pop($hasilAnalisis['tfidf'][1]);
              
                  // Menampilkan hasil TF-IDF Abstrak
                  $dokumen = "";
                  echo "<h3 class='text-center'>TF-IDF Abstrak</h3>";
                  echo "<table class='table table-bordered'>";
                  echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";
                  $dokumen .= "<td  rowspan=" . count($latihAbstrak) .  ">Data latih</td>";
                  foreach ($latihAbstrak as $i => $document) {
                    echo "<tr>";
                    echo $dokumen;
                    echo "<td>". $i ."</td><td>". $document ."</td></tr>";
                    $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
              
                  }
              
                  $dokumen = "";
                  foreach ($hasilAnalisis['tfidf'][1] as $i => $document) {
                    $dokumen .= "<td rowspan=" . count($document) .  ">Dokumen " . ($i + 1) . "</td>";
                    foreach ($document as $term => $tfidfValue) {
                      echo "<tr>";
                      echo $dokumen;
                      echo "<td>" . $term . "</td>";
                      echo "<td>" . $tfidfValue . "</td>";
                      echo "</tr>";
                      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
                    }
                  }
                  echo "</table>";
                  ?>
              </div>
              <?php 
                }
              ?>
            </div>
            <?php 
              if(!empty($_POST["judul"])){
              echo "<h3 class='text-center'>Cosine Similarity Judul</h3>";
              echo "<table class='table table-bordered'>";
              echo "<tr><th>Dataset</th><th>Persen</th><th>Similarity</th><th>Isi</th></tr>";
              echo "<tr><td colspan=3 style='text-align:center'>Data latih</td><td>". $judulSkripsi ."</td></tr>";

              $counter = 0;
              $hasSimilarity = false; // Flag untuk menandakan apakah ada hasil kemiripan atau tidak
              foreach ($result[0] as $datasetId => $similarity) {
                if ($similarity > 0) {
                  $hasSimilarity = true; // Set flag menjadi true jika ada hasil kemiripan
                  echo "<tr>";
                  echo "<td>Dataset " . ($datasetId + 1) . "</td>";
                  echo "<td>" . persentase($similarity, 3) . "% </td>";
                  echo "<td>" . $similarity . "</td>";
                  echo "<td>" . highlightDynamicBackground( $judulSkripsi, $corpus_judul[$datasetId]) . "</td>";
                  echo "</tr>";

                  $counter++;
                  if ($counter == 3) {
                    break;
                  }
                }
              }

              if (!$hasSimilarity) {
                  echo "<tr><td colspan='4'>Tidak ada data yang mirip</td></tr>";
              }

              echo "</table>";

                }
                
            if(!empty($_POST["abstrak"])){
            echo "<h3 class='text-center'>Cosine Similarity Abstrak</h3>";
            echo "<table class='table table-bordered'>";
              echo "<tr>
                <th>Dataset</th>
                <th>Persen</th>
                <th>Similarity</th>
                <th>Isi</th>
              </tr>";
              echo "<tr>
                <td colspan=3 style='text-align:center'>Data latih</td>
                <td>". $abstrakSkripsi ."</td>
              </tr>";

              $counter = 0;
              $hasSimilarity = false; // Flag untuk menandakan apakah ada hasil kemiripan atau tidak

              foreach ($result[1] as $datasetId => $similarity) {
              if ($similarity > 0) {
              $hasSimilarity = true; // Set flag menjadi true jika ada hasil kemiripan
              echo "<tr>";
                echo "<td>Dataset " . ($datasetId + 1) . "</td>";
                echo "<td>" . persentase($similarity, 3) . "% </td>";
                echo "<td>" . $similarity . "</td>";
                echo "<td>" .  highlightDynamicBackground( $abstrakSkripsi, $corpus_abstrak[$datasetId]) . "</td>";
                echo "</tr>";

              $counter++;
              if ($counter == 3) {
              break;}
              }
              }
              if (!$hasSimilarity) {
              echo "<tr>
                <td colspan='4'>Tidak ada data yang mirip</td>
              </tr>";
              }
              echo "</table>";
              } ?>
            <div>
              <a href="../index.php" class="no-print btn btn-danger" role="button">Kembali</a>
            </div>
          </div>
        </div>
      </div>

    </section>

  </main>

  <!-- JAVASCRIPT FILES -->
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/jquery.sticky.js"></script>
  <script src="../js/custom.js"></script>
  <script>
  function myFunction() {
    var x = document.getElementById("myDIV");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

  function toggleVisibility(elementId) {
    var x = document.getElementById(elementId);
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

  function hideOrShowElement(elementType) {
    if (elementType === 'tfjudul') {
      toggleVisibility('tfJudul');
    } else if (elementType === 'idfJudul') {
      toggleVisibility('idfJudul');
    } else if (elementType === 'tfidfJudul') {
      toggleVisibility('tfidfJudul');
    } else if (elementType === 'tfabstrak') {
      toggleVisibility('tfAbstrak');
    } else if (elementType === 'idfabstrak') {
      toggleVisibility('idfAbstrak');
    } else if (elementType === 'tfidfabstrak') {
      toggleVisibility('tfidfAbstrak');
    } else if (elementType === 'myDIV') {
      toggleVisibility('myDIV');
    }
  }
  </script>

</body>

</html>