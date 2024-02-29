<?php 
include("function/Function.php");

// Menggunakan contoh koneksi ke database yang sudah ada
$connection = Connection();
if ($connection->connect_error) {
    die("Koneksi ke database gagal: " . $connection->connect_error);
}

// Mengambil data dari database
$data = getDataFromDatabase($connection);

// Menutup koneksi ke database
$connection->close();

// Memasukkan data ke dalam fungsi TW
$corpus = [];
foreach ($data as $row) {
    $corpus_judul [] = explode(' ', $row['judul']);
    $corpus_abstrak[] = explode(' ', $row['abstrak']);
}

// print_r($data);
// print_r($corpus);

$tfidf_judul = calculateTFIDF($corpus_judul);
$tfidf_abstrak = calculateTFIDF($corpus_abstrak);

// Menampilkan hasil TF-IDF
echo "<table style='border-collapse: collapse;'>";
echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";

foreach ($tfidf_judul as $i => $document) {
    foreach ($document as $term => $tfidfValue) {
        echo "<tr>";
        echo "<td style='border: 1px solid black;'>Dokumen " . ($i + 1) . "</td>";
        echo "<td style='border: 1px solid black;'>" . $term . "</td>";
        echo "<td style='border: 1px solid black;'>" . $tfidfValue . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<table style='border-collapse: collapse;'>";
echo "<tr><th>Dokumen</th><th>Term</th><th>TF-IDF Value</th></tr>";

foreach ($tfidf_abstrak as $i => $document) {
    foreach ($document as $term => $tfidfValue) {
        echo "<tr>";
        echo "<td style='border: 1px solid black;'>Dokumen " . ($i + 1) . "</td>";
        echo "<td style='border: 1px solid black;'>" . $term . "</td>";
        echo "<td style='border: 1px solid black;'>" . $tfidfValue . "</td>";
        echo "</tr>";
    }
}

echo "</table>";



?>