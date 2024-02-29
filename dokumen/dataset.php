<?php 
include '../function/Function.php';
$connection = Connection();
if ($connection->connect_error) {
    die("Koneksi ke database gagal: " . $connection->connect_error);
}
$data = getDataFromDatabase($connection);
foreach ($data as $row) {
  $corpus_judul[] = $row['judul'];
  $corpus_abstrak[] = $row['abstrak'];
  $corpus_judulAsli[] = $row['judulAsli'];
  $corpus_abstrakAsli[] = $row['abstrakAsli'];
}
?>
<style>
table {
  border-collapse: collapse;
  width: 100%;
}

th,
td {
  border: 1px solid black;
  padding: 8px;
  text-align: left;
}
</style>



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
  <script>
  function konfirmasiPengosongan() {
    return confirm("Apakah Anda yakin ingin mengosongkan tabel? Tindakan ini tidak dapat dibatalkan.");
  }
  </script>
</head>

<body id="top">

  <main>
    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
      <div class="container">
        <div class="row">
          <div class="col-lg-10 col-12 mb-4 mx-auto">
            <h1 class="text-white text-center">Dataset</h1>
            <h3 class="text-center">Analisis kemiripan skripsi</h3>
            <form method="post" action="">
              <input type="hidden" name="konfirmasi" value="ya">
              <a href="../index.php" class="btn btn-danger" role="button">Kembali</a>

              <input type="submit" class="btn btn-danger" name="empty_table" value="Kosongkan Tabel"
                onclick="return konfirmasiPengosongan();">
            </form>

            <?php if(!empty($pesan)) { echo "<p>$pesan</p>"; } 
            $koneksi = Connection();
            $pesan = ""; 

            if(isset($_POST['empty_table'])) {
              $konfirmasi = $_POST['konfirmasi'];

                if($konfirmasi === "ya") {
                    $query = "TRUNCATE TABLE data"; 
                    if(mysqli_query($koneksi, $query)) {
                        $pesan = "Tabel berhasil dikosongkan.";
                    } else {
                        $pesan = "Gagal mengosongkan tabel: " . mysqli_error($koneksi);
                    }
                } else {
                    $pesan = "Pengosongan tabel dibatalkan.";
                }
            }
            mysqli_close($koneksi);
            ?>


            <table class='table table-bordered'>
              <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Abstrak</th>
                <th>Judul Asli</th>
                <th>Abstrak Asli</th>
              </tr>

              <?php 

  $no = 1;
  for ($i = 0; $i < count($data); $i++) : 

   ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?php print_r($corpus_judul[$i]); ?></td>
                <td><?php print_r($corpus_abstrak[$i]); ?></td>
                <td><?php print_r($corpus_judulAsli[$i]); ?></td>
                <td><?php print_r($corpus_abstrakAsli[$i]); ?></td>
              </tr>
              <?php endfor; ?>
            </table>
          </div>

        </div>
      </div>
    </section>

    <section class="featured-section">
      <div class="container">
        <div class="row justify-content-center">


        </div>
      </div>
    </section>



  </main>

  <!-- JAVASCRIPT FILES -->
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/jquery.sticky.js"></script>
  <script src="../js/custom.js"></script>

</body>

</html>