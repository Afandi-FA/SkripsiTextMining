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
  <?php
include '../function/Function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Cek apakah file CSV diunggah tanpa ada kesalahan
  if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['csv_file'];

    // Tentukan direktori tujuan untuk menyimpan file CSV yang diunggah
    $targetDir = '../dataset';

    // Pindahkan file CSV yang diunggah ke direktori tujuan
    $destination = $targetDir . $file['name'];
    if (move_uploaded_file($file['tmp_name'], $destination)) {
      // File berhasil diunggah, lanjutkan ekstraksi data
      // Buka file CSV
      $handle = fopen($destination, 'r');

      // Periksa apakah file berhasil dibuka
      if ($handle !== false) {
        // Array untuk menyimpan data dari file CSV
        $data = array();

        // Membaca baris-baris dari file CSV
        while (($row = fgetcsv($handle, 10000, ',', '~')) !== false) {
          $data[] = $row;
        }
        // Menutup file CSV
        fclose($handle);
        // var_dump(count($data));
        
      foreach ($data as $subArray) {
        $firstArray[] = $subArray[0];
        $secondArray[] = $subArray[1];
      }
      ?>

  <table class="table table-bordered">
    <tr>
      <th>No</th>
      <th>Text Asli</th>
      <th>Normalisasi</th>
      <th>Token</th>
      <th>Stopword</th>
      <th>Stemming</th>
    </tr>

    <h4 style='text-align: center;'>Data Judul yang disimpan</h5>
      <a href="../index.php" class="btn btn-danger" role="button">Kembali</a>
      <?php 

  $no = 1;
  for ($i = 0; $i < count($data); $i++) : 

   ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?php print_r($firstArray[$i]); ?></td>
        <td><?php print_r($a[$i] = normalizeText($firstArray[$i])); ?></td>
        <td><?php print_r($a[$i] = tokenizeBySpace($a[$i])); ?></td>
        <td><?php print_r($a[$i] = stopwordRemover($a[$i])); ?></td>
        <td><?php print_r($a[$i] = stemming($a[$i])); ?></td>
      </tr>
      <?php endfor; ?>
  </table>

  <table class="table table-bordered">
    <tr>
      <th>No</th>
      <th>Text Asli</th>
      <th>Normalisasi</th>
      <th>Token</th>
      <th>Stopword</th>
      <th>Stemming</th>
    </tr>

    <?php 
  $no = 1;
  echo "<h4 style='text-align: center;'>Data Abstrak yang disimpan</h5>";
  for ($i = 0; $i < count($data); $i++) : 

   ?>
    <tr>
      <td><?= $no++; ?></td>
      <td><?php print_r($secondArray[$i]); ?></td>
      <td><?php print_r($a[$i] = normalizeText($secondArray[$i])); ?></td>
      <td><?php print_r($a[$i] = tokenizeBySpace($a[$i])); ?></td>
      <td><?php print_r($a[$i] = stopwordRemover($a[$i])); ?></td>
      <td><?php print_r($a[$i] = stemming($a[$i])); ?></td>
    </tr>
    <?php endfor; ?>
  </table>
  <?php
      $log = array();
      for ($i=0; $i < count($data); $i++) { 
        for ($j=0; $j < 2; $j++) { 
          $log[$i][$j] = normalizeText($data[$i][$j]);
          $token[$i][$j] = tokenizeBySpace($log[$i][$j]);
          $stopword[$i][$j] = stopwordRemover($token[$i][$j]);
          $stemming[$i][$j] = stemming($stopword[$i][$j]);
        }
      }

      $conn = Connection();

      // Periksa koneksi
      if ($conn->connect_error) {
      die("Koneksi database gagal: " . $conn->connect_error);
      }
      // foreach ($data as $row) {
        for ($i=0; $i < count($data); $i++) { 
          $data1 = $data[$i];
          $data2 = $stemming[$i];
      // Lakukan operasi penyisipan data ke tabel yang sesuai
        $sql = "INSERT INTO data (data_id, judul, abstrak, judulAsli, abstrakAsli) VALUES ('', '$data2[0]', '$data2[1]', '$data1[0]', '$data1[1]')";
        if ($conn->query($sql) === true) {
        echo "";
        } else {
        echo "Terjadi kesalahan saat menyimpan data: " . $conn->error;
        die();
        }

      }
      // Tutup koneksi database
      $conn->close();
      } else {
        echo "Gagal membuka file CSV";
      }
    } else {
      echo 'Terjadi kesalahan saat mengunggah file.';
    }
  }
  if(!empty($_POST["judul"])&&!empty($_POST["abstrak"])){

    $judul = $_POST["judul"];
    $abstrak = $_POST["abstrak"];

    // var_dump($judul);
    // var_dump($abstrak);
    // die();
    ?>
  <table class="table table-bordered">
    <tr>
      <th>No</th>
      <th>Text Asli</th>
      <th>Normalisasi</th>
      <th>Token</th>
      <th>Stopword</th>
      <th>Stemming</th>
    </tr>
    <h4 style="text-align: center;">Judul yang disimpan</h5>
      <a href="../index.php" class="btn btn-danger" role="button">Kembali</a>

      <tr>
        <td>1</td>
        <td><?php print_r($judul); ?></td>
        <td><?php print_r($judulClean = normalizeText($judul)); ?></td>
        <td><?php print_r($judulClean = tokenizeBySpace($judulClean)); ?></td>
        <td><?php print_r($judulClean = stopwordRemover($judulClean)); ?></td>
        <td><?php print_r($judulClean = stemming($judulClean)); ?></td>
      </tr>
  </table>
  <table class="table table-bordered">
    <tr>
      <th>No</th>
      <th>Text Asli</th>
      <th>Normalisasi</th>
      <th>Token</th>
      <th>Stopword</th>
      <th>Stemming</th>
    </tr>
    <h4 style="text-align: center;">Abstrak yang disimpan</h5>

      <tr>
        <td>1</td>
        <td><?php print_r($abstrak); ?></td>
        <td><?php print_r($abstrakClean = normalizeText($abstrak)); ?></td>
        <td><?php print_r($abstrakClean = tokenizeBySpace($abstrakClean)); ?></td>
        <td><?php print_r($abstrakClean = stopwordRemover($abstrakClean)); ?></td>
        <td><?php print_r($abstrakClean = stemming($abstrakClean)); ?></td>
      </tr>
  </table>
  <?php
    $conn = Connection();

    // Periksa koneksi
    if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
    }
    // Lakukan operasi penyisipan data ke tabel yang sesuai
      $sql = "INSERT INTO data (data_id, judul, abstrak, judulAsli, abstrakAsli) VALUES ('', '$judulClean', '$abstrakClean', '$judul', '$abstrak')";
      if ($conn->query($sql) === true) {
      echo "";
      } else {
      echo "Terjadi kesalahan saat menyimpan data: " . $conn->error;
      die();
      }
    // Tutup koneksi database
    $conn->close();
  }
}
?>
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/jquery.sticky.js"></script>
  <script src="../js/custom.js"></script>
  </body>

</html>