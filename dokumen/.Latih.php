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
  // var_dump($hasilAnalisis['idf']);
  // die();
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

  if(!empty($_POST["judul"])){
    //tf untuk judul
    //mengambil data akhir (data latih)
    $latihJudul = end($hasilAnalisis['tf'][0]);
    // menghapus data latih
    array_pop($hasilAnalisis['tf'][0]);
    // var_dump($hasilAnalisis);

    // Menampilkan  TF judul
    $dokumen = "";
    echo "TF judul:<br>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr><th>Dokumen</th><th>Term</th><th>TF Value<
    /th></tr>";
    $dokumen .= "<td  rowspan=" . count($latihJudul) .  " style='border: 1px solid black;'>Data latih</td>";
    foreach ($latihJudul as $i => $document) {
      echo "<tr style='border: 1px solid black;'>";
      echo $dokumen;
      echo "<td style='border: 1px solid black;'>". $i ."</td><td>". $document ."</td></tr>";
      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama

    }

    $dokumen = "";
    foreach ($hasilAnalisis['tf'][0] as $i => $document) {
      $dokumen .= "<td rowspan=" . count($document) .  " style='border: 1px solid black;'>Dokumen " . ($i + 1) . "</td>";
      foreach ($document as $term => $tfidfValue) {
        echo "<tr>";
        echo $dokumen;
        echo "<td style='border: 1px solid black;'>" . $term . "</td>";
        echo "<td style='border: 1px solid black;'>" . $tfidfValue . "</td>";
        echo "</tr>";
        $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
      }
    }
    echo "</table>";
    echo "<br/>";

    // ---------------------------------------------------------------------------------------------------------------------
    //idf untuk judul
    // Menampilkan  idf judul
    echo "IDF judul:<br>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr style='border: 1px solid black;'><th>Term</th><th>IDF Value</th><th>DF Value</th></tr>";

    //urutkan hasil bersararkan key abjad
    $urutDf[] = $hasilAnalisis['df'][0];
    $urutIdf[] = $hasilAnalisis['idf'][0];

    $dfIdf = array_merge_recursive($urutDf, $urutIdf);
    //hitung baris maksimal
    $maxCount = max(count($dfIdf[0]), count($dfIdf[1]));
    for ($i = 0; $i < $maxCount; $i++) {
      echo "<tr style='border: 1px solid black;'>";
      echo "<td style='border: 1px solid black;'>";
          echo key($dfIdf[1]);
      echo "</td>";
      echo "<td style='border: 1px solid black;'>";
          echo current($dfIdf[1]);
          next($dfIdf[1]);
      echo "</td>";
      echo "<td style='border: 1px solid black;'>";
      if (array_key_exists($i, $dfIdf[0])) {
          echo $dfIdf[0][$i];
      }
      echo "</td>";
      echo "</tr>";
    }

    echo "</table>";
    echo "<br/>";


    // ----------------------------------------------------------------------------------------------------------------------------------
    //tf idf untuk judul
    //mengambil data akhir (data latih)
    $latihJudul = end($hasilAnalisis['tfidf'][0]);
    // menghapus data latih
    array_pop($hasilAnalisis['tfidf'][0]);

    $dokumen = "";
    echo "Hasil perhitungan tf-idf judul:<br>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";
    $dokumen .= "<td  rowspan=" . count($latihJudul) .  " style='border: 1px solid black;'>Data latih</td>";
    foreach ($latihJudul as $i => $document) {
      echo "<tr style='border: 1px solid black;'>";
      echo $dokumen;
      echo "<td style='border: 1px solid black;'>". $i ."</td><td>". $document ."</td></tr>";
      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama

    }

    $dokumen = "";
    foreach ($hasilAnalisis['tfidf'][0] as $i => $document) {
      $dokumen .= "<td rowspan=" . count($document) .  " style='border: 1px solid black;'>Dokumen " . ($i + 1) . "</td>";
      foreach ($document as $term => $tfidfValue) {
        echo "<tr>";
        echo $dokumen;
        echo "<td style='border: 1px solid black;'>" . $term . "</td>";
        echo "<td style='border: 1px solid black;'>" . $tfidfValue . "</td>";
        echo "</tr>";
        $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
      }
    }
    echo "</table>";
  }

  if(!empty($_POST["abstrak"])){
    //tf idf untuk abstrak
    //mengambil data akhir (data latih)
    $latihAbstrak = end($hasilAnalisis['tfidf'][1]);
    // menghapus data latih
    array_pop($hasilAnalisis['tfidf'][1]);

    // Menampilkan hasil TF-IDF abstrak
    $dokumen = "";
    echo "Hasil perhitungan tf-idf abstrak:<br>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";
      $dokumen .= "<td  rowspan=" . count($latihAbstrak) .  " style='border: 1px solid black;'>Abstrak Data latih</td>";
      foreach ($latihAbstrak as $i => $document) {
        echo "<tr style='border: 1px solid black;'>";
        echo $dokumen;
        echo "<td style='border: 1px solid black;'>". $i ."</td><td>". $document ."</td></tr>";
        $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
    }

    $dokumen = "";
    foreach ($hasilAnalisis['tfidf'][1] as $i => $document) {
      $dokumen .= "<td rowspan=" . count($document) .  " style='border: 1px solid black;'>Abstrak Dokumen " . ($i + 1) . "</td>";
      foreach ($document as $term => $tfidfValue) {
        echo "<tr>";
        echo $dokumen;
        echo "<td style='border: 1px solid black;'>" . $term . "</td>";
        echo "<td style='border: 1px solid black;'>" . $tfidfValue . "</td>";
        echo "</tr>";
        $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
      }
    }
    echo "</table>";
    echo "<br/>";

    // ---------------------------------------------------------------------------------------------------------------------
    //idf untuk Abstrak
    // Menampilkan  idf Abstrak
    echo "IDF Abstrak:<br>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr style='border: 1px solid black;'><th>Term</th><th>IDF Value</th><th>DF Value</th></tr>";

    //urutkan hasil bersararkan key abjad
    $urutDfAbstrak[] = $hasilAnalisis['df'][1];
    $urutIdfAbstrak[] = $hasilAnalisis['idf'][1];
    $dfIdfAbstrak = array_merge_recursive($urutDfAbstrak, $urutIdfAbstrak);

    //hitung baris maksimal
    $maxCount = max(count($dfIdfAbstrak[0]), count($dfIdfAbstrak[1]));
    for ($i = 0; $i < $maxCount; $i++) {
      echo "<tr style='border: 1px solid black;'>";
      echo "<td style='border: 1px solid black;'>";
          echo key($dfIdfAbstrak[1]);
      echo "</td>";
      echo "<td style='border: 1px solid black;'>";
          echo current($dfIdfAbstrak[1]);
          next($dfIdfAbstrak[1]);
      echo "</td>";
      echo "<td style='border: 1px solid black;'>";
      if (array_key_exists($i, $dfIdfAbstrak[0])) {
          echo $dfIdfAbstrak[0][$i];
      }
      echo "</td>";
      echo "</tr>";
    }

    echo "</table>";
    echo "<br/>";


    // ------------------------------------------
    //tf idf untuk Abstrak
    //mengambil data akhir (data latih)
    $latihAbstrak = end($hasilAnalisis['tfidf'][1]);
    // menghapus data latih
    array_pop($hasilAnalisis['tfidf'][1]);

    // Menampilkan hasil TF-IDF Abstrak
    $dokumen = "";
    echo "Hasil perhitungan tf-idf Abstrak:<br>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";
    $dokumen .= "<td  rowspan=" . count($latihAbstrak) .  " style='border: 1px solid black;'>Abstrak Data latih</td>";
    foreach ($latihAbstrak as $i => $document) {
      echo "<tr style='border: 1px solid black;'>";
      echo $dokumen;
      echo "<td style='border: 1px solid black;'>". $i ."</td><td>". $document ."</td></tr>";
      $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama

    }

    $dokumen = "";
    foreach ($hasilAnalisis['tfidf'][1] as $i => $document) {
      $dokumen .= "<td rowspan=" . count($document) .  " style='border: 1px solid black;'>Abstrak Dokumen " . ($i + 1) . "</td>";
      foreach ($document as $term => $tfidfValue) {
        echo "<tr>";
        echo $dokumen;
        echo "<td style='border: 1px solid black;'>" . $term . "</td>";
        echo "<td style='border: 1px solid black;'>" . $tfidfValue . "</td>";
        echo "</tr>";
        $dokumen = ""; // Kosongkan nilai dokumen setelah baris pertama
      }
    }
    echo "</table>";
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
  $corpus_judul[] = $row['judul'];
  $corpus_abstrak[] = $row['abstrak'];
}

if(!empty($_POST["judul"])){

  // Tampilkan hasil
  echo "Hasil perhitungan cosine similarity judul:<br>";
  echo "<table border='1'>";
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
      echo "<td>" . $corpus_judul[$datasetId] . "</td>";
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

  // Tampilkan hasil
  echo "Hasil perhitungan cosine similarity abstrak:<br>";
  echo "<table border='1'>";
  echo "<tr><th>Dataset</th><th>Persen</th><th>Similarity</th><th>Isi</th></tr>";
  echo "<tr><td colspan=3 style='text-align:center'>Data latih</td><td>". $abstrakSkripsi ."</td></tr>";

  $counter = 0;
  $hasSimilarity = false; // Flag untuk menandakan apakah ada hasil kemiripan atau tidak

  foreach ($result[1] as $datasetId => $similarity) {
    if ($similarity > 0) {
      $hasSimilarity = true; // Set flag menjadi true jika ada hasil kemiripan
      echo "<tr>";
      echo "<td>Dataset " . ($datasetId + 1) . "</td>";
      echo "<td>" . persentase($similarity, 3) . "% </td>";
      echo "<td>" . $similarity . "</td>";
      echo "<td>" . $corpus_abstrak[$datasetId] . "</td>";
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
?>