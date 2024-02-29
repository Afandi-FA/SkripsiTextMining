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
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-icons.css" rel="stylesheet">
  <link href="css/templatemo-topic-listing.css" rel="stylesheet">
</head>

<body>

  <main>
    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
      <div class="container">
        <div class="row">

          <h1 class="text-white text-center">Analisis Kemiripan Proposal Skripsi</h1>

          <div class=" col-sm col-lg-7 col-12 mb-4 mx-auto">
            <h3 class="text-center">masukkan dokumen</h3>
            <form action="dokumen/latihbaru.php" method="post" role=" search">
              <div class="form-group">
                <label for="judul" class="mb-2 h5">Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul" required>
              </div>
              <div class="form-group">
                <label for="abstrak" class="mb-2 mt-2 h5">Ringkasan Proposal</label>
                <textarea class="form-control" id="abstrak" name="abstrak" rows="8"
                  placeholder="Masukkan ringkasan proposal"></textarea>
              </div>
              <button type="submit" class="btn custom-btn mt-2 mb-5 mt-lg-3" value="Proses">Hitung</button>
            </form>
          </div>

          <div class=" col-sm col-lg-5 col-12 mb-4 mx-auto">
            <h3 class="text-center">upload dataset</h3>
            <form action="dokumen/view.php" method="post" role=" search">
              <div class="form-group">
                <label for="judul" class="mb-2 h5">Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul" required>
              </div>
              <div class="form-group">
                <label for="abstrak" class="mb-2 mt-2 h5">Ringkasan Proposal</label>
                <textarea class="form-control" id="abstrak" name="abstrak" rows="8"
                  placeholder="Masukkan ringkasan proposal" required></textarea>
              </div>
              <button type="submit" class="btn custom-btn mt-2 mb-5 mt-lg-3" value="Proses">Masukkan</button>
            </form>
          </div>

        </div>
      </div>
    </section>

    <section class="featured-section">
      <div class="container">
        <div class="row justify-content-center">

          <!-- <div class="col-lg-3 col-12 ">
            <div class="custom-block shadow-lg">

              <div class="d-flex">
                <div>
                  <h5 class="text-white text-center mb-2">Download Contoh Dataset</h5>
                </div>
              </div>
              <?php 
              echo '<p><a href="download.php?file=' . urlencode('dataset.csv') . '">Download</a></p>';
              ?>
              </form>
            </div>
          </div> -->

          <div class="col-lg-3 col-12 ">
            <div class="custom-block shadow-lg">
              <a href="dokumen/dataset.php">
                <div class="d-flex">
                  <div>
                    <h5 class="text-white text-center mb-2">Lihat Dataset</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>

          <div class="col-lg-6 col-12 mb-lg-0">
            <div class="custom-block shadow-lg">
              <div class="d-flex flex-column">
                <div>
                  <h5 class="text-white mb-2">Upload Proposal</h5>
                  <form method="POST" action="dokumen/view.php" enctype="multipart/form-data">
                    <input type="file" name="csv_file" accept=".csv">


                    <input type="submit" value="Upload">
                  </form>
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>
    </section>





  </main>

  <!-- JAVASCRIPT FILES -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/jquery.sticky.js"></script>
  <script src="js/custom.js"></script>

</body>

</html>