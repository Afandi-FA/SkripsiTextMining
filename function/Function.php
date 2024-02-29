<?php
require_once '../vendor/autoload.php';

use Sastrawi\StopWordRemover\StopWordRemoverFactory;
use Sastrawi\Stemmer\StemmerFactory;

// Koneksi Ke Database
function Connection()
{
  return mysqli_connect("localhost", "root", "", "cocok");
}

// Menampilkan Data
function showData($query)
{
  $con = Connection();
  $result = mysqli_query($con, $query);
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
  }
  return $rows;
}


// Menghitung Persentase
function persentase($number)
{
  return $number * 100;
}

//preprocessing text
//text normalizer
function normalizeText($text)
{
// Mengubah teks menjadi huruf kecil
$normalizedText = strtolower($text);

// Menghapus karakter-karakter tertentu
$normalizedText = preg_replace('/[^A-Za-z0-9 ]/', '', $normalizedText);

// Menghapus spasi berlebih
$normalizedText = trim(preg_replace('/\s+/', ' ', $normalizedText));

// Menghapus tanda baca
$normalizedText = preg_replace('/[^\w\s]/', '', $normalizedText);

// Menggantikan karakter tertentu dengan karakter pengganti
$normalizedText = str_replace(['*', '&', '^', '%', '$', '#', '@'], '', $normalizedText);

return $normalizedText;
}

//tonenizing
function tokenizeBySpace($text)
{
    $tokens = explode(' ', $text);
    return $tokens;
}

//stopword
function stopwordRemover($text)
{
    // Buat instance StopWordRemover
    $stopwordFactory = new StopWordRemoverFactory();
    $stopwordRemover = $stopwordFactory->createStopWordRemover();

    // Menghapus stopword dari token
    $filteredTokens = [];
    foreach ($text as $token) {
        $filteredToken = $stopwordRemover->remove($token);
        if (!empty($filteredToken)) {
            $filteredTokens[] = $filteredToken;
        }
    }
    return $filteredTokens;
}

//stemming
function stemming($text)
{
    $stemmerFactory = new StemmerFactory();
    $stemmer = $stemmerFactory->createStemmer();

    // Melakukan stemming pada setiap token yang telah difilter
    $stemmedTokens = array_map(function ($text) use ($stemmer) {
        return $stemmer->stem($text);
    }, $text);

    //menggabungkan teks
    $preprocessedText = implode(' ', $stemmedTokens);

    return $preprocessedText;
}

// Fungsi untuk mengambil data dari database
function getDataFromDatabase($connection)
{
  $data = [];

  // Query SQL untuk mengambil data dari tabel
  $sql = "SELECT judul, abstrak, judulAsli, abstrakAsli FROM data";

  $result = $connection->query($sql);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          // Menambahkan judul dan abstrak ke dalam array data
          $data[] = [
              'judul' => $row['judul'],
              'abstrak' => $row['abstrak'],
              'judulAsli' => $row['judulAsli'],
              'abstrakAsli' => $row['abstrakAsli']
          ];
      }
  }

  return $data;
}

// Fungsi untuk menghitung TF-IDF
function calculateTFIDF($corpus)
{
  // Menghitung Term Frequency (TF) untuk setiap term dalam setiap dokumen
  $tf = [];
  foreach ($corpus as $document) {
      $terms = array_count_values($document);
      $tf[] = $terms;
  }
  // Menghitung Inverse Document Frequency (IDF) untuk setiap term
  $idf = [];
  $totalDocuments = count($corpus);
  foreach ($corpus as $document) {
      foreach ($document as $term) {
          if (!isset($idf[$term])) {
              $documentFrequency = 0;
              foreach ($corpus as $d) {
                  if (in_array($term, $d)) {
                      $documentFrequency++;
                  }
              }
              $countDocumentFrequency[] = $documentFrequency;
              $idf[$term] = number_format(log10($totalDocuments / $documentFrequency), 4);
          }
      }
  }
  // Menghitung TF-IDF untuk setiap term dalam setiap dokumen
  $tfidf = [];
  foreach ($tf as $i => $document) {
      $tfidfDocument = [];
      foreach ($document as $term => $tfValue) {
          $tfidfValue = $tfValue * $idf[$term];
          $tfidfDocument[$term] = $tfidfValue;
      }
      $tfidf[] = $tfidfDocument;
      
  }

  return [
      'df' => $countDocumentFrequency,
      'tf' => $tf,
      'idf' => $idf,
      'tfidf' => $tfidf
  ];
}

// Fungsi untuk memproses analisis kemiripan skripsi
function calculateAllTerm($judul, $abstrak)
{
// Menggunakan contoh koneksi ke database yang sudah ada
$connection = Connection();
if ($connection->connect_error) {
    die("Koneksi ke database gagal: " . $connection->connect_error);
}

// Mengambil data dari database
$data = getDataFromDatabase($connection);

  // Lakukan pemrosesan analisis kemiripan skripsi di sini
  // Implementasikan langkah-langkah yang dijelaskan sebelumnya
  foreach ($data as $row) {
      $corpus_judul[] = explode(' ', $row['judul']);
      $corpus_abstrak[] = explode(' ', $row['abstrak']);
  }

  //data pembanding
  $judul = explode(' ', $judul);
  $abstrak = explode(' ', $abstrak);
  
  // memasukkan pembanding ke data set
  array_push($corpus_judul, $judul);
  array_push($corpus_abstrak, $abstrak);
  $connection->close();

  // Memasukkan data ke dalam fungsi TW
  $tfidf_judul = calculateTFIDF($corpus_judul);
  $tfidf_abstrak = calculateTFIDF($corpus_abstrak);


  $df = array($tfidf_judul['df'], $tfidf_abstrak['df']);
  $tf = array($tfidf_judul['tf'], $tfidf_abstrak['tf']);
  $idf = array($tfidf_judul['idf'], $tfidf_abstrak['idf']);
  $tfidf = array($tfidf_judul['tfidf'], $tfidf_abstrak['tfidf']);
  return [
      'df' => $df,
      'tf' => $tf,
      'idf' => $idf,
      'tfidf' => $tfidf
  ];
}

function calculateAllSimilarity($datasetJudul, $datasetAbstrak, $searchDataJudul, $searchDataAbstrak)
{
  // Array untuk menyimpan hasil perhitungan cosine similarity
  $resultsJudul = array();

  // Hitung cosine similarity untuk setiap dataset
  // perhitungan judul
  foreach ($datasetJudul as $dataset) {
      $datasetId = $dataset['id'];
      $datasetData = $dataset['data'];

      // Hitung cosine similarity dengan dataset saat ini
      $similarity = cosineSimilarity($searchDataJudul['data'], $datasetData);
      // Simpan hasil perhitungan cosine similarity
      $resultsJudul[$datasetId] = number_format($similarity, 3);
  }

  //hapus data datalatih
  array_pop($resultsJudul);

  // Urutkan hasil perhitungan cosine similarity dari yang tertinggi ke terendah
  arsort($resultsJudul);

  // Array untuk menyimpan hasil perhitungan cosine similarity
  $resultsAbstrak = array();

  // Hitung cosine similarity untuk setiap dataset
  // perhitungan abstrak
  foreach ($datasetAbstrak as $dataset) {
      $datasetId = $dataset['id'];
      $datasetData = $dataset['data'];

      // Hitung cosine similarity dengan dataset saat ini
      $similarity = cosineSimilarity($searchDataAbstrak['data'], $datasetData);

      // Simpan hasil perhitungan cosine similarity
      $resultsAbstrak[$datasetId] = number_format($similarity, 3);
  }
  //hapus data datalatih
  array_pop($resultsAbstrak);
  // Urutkan hasil perhitungan cosine similarity dari yang tertinggi ke terendah
  arsort($resultsAbstrak);

  return array($resultsJudul, $resultsAbstrak);
}

function cosineSimilarity($doc1, $doc2){
  $dotProduct = 0;
  $normA = 0;
  $normB = 0;

  foreach ($doc1 as $term => $weight) {
    if(array_key_exists($term, $doc2)){
      $dotProduct += $weight * $doc2[$term];
    }
    $normA += pow($weight, 2);
  }
  $normA = sqrt($normA);

  foreach ($doc2 as $term => $weight) {
    $normB += pow($weight, 2);
  }
  $normB = sqrt($normB);

  if($normA > 0 && $normB > 0){
    $cosineSimilatiry = $dotProduct / ($normA * $normB);
  }else {
    $cosineSimilatiry = 0;
  }
  return $cosineSimilatiry;
}
function generateColor($word) {
  $hash = md5($word);
  $color = substr($hash, 0, 6);
  return $color;
}

function highlightDynamicBackground($baseDocArray, $docToHighlight) {
  $baseWords = explode(" ", $baseDocArray);
  $wordsToHighlight = explode(" ", $docToHighlight);

  $highlightedWords = [];

  foreach ($wordsToHighlight as $word) {
      if (in_array($word, $baseWords)) {
          $color = generateColor($word);
          $highlightedWords[] = '<span style="background-color: #' . $color . ';">' . $word . '</span>';
      } else {
          $highlightedWords[] = $word;
      }
  }

  return implode(" ", $highlightedWords);
}