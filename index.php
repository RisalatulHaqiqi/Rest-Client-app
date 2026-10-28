<?php
//load config.php
include("config/config.php");

//untuk api_key newsapi.org
$api_key = "xxx";

//url api untuk ambil berita headline kategori teknologi
$url = "https://newsapi.org/v2/top-headlines?country=us&category=technology&apiKey=" . $api_key;

//menyimpan hasil dalam variabel
$data = http_request_get($url);

//konversi data json ke array
$hasil = json_decode((string)$data, true);

//cek apakah data berita valid sebelum ditampilkan
$berita_ok = is_array($hasil)
    && isset($hasil['articles'])
    && is_array($hasil['articles'])
    && count($hasil['articles']) > 0;

//pesan error ramah pengguna
$pesan_error = "";
if (!$berita_ok) {
    if (is_array($hasil) && isset($hasil['status']) && $hasil['status'] === 'error') {
        $pesan_error = "Gagal mengambil berita. Periksa kembali API key Anda.";
    } elseif (is_array($hasil) && isset($hasil['articles'])) {
        $pesan_error = "Berita tidak tersedia saat ini.";
    } else {
        $pesan_error = "Gagal mengambil berita. Periksa koneksi internet Anda lalu coba lagi.";
    }
}

//fungsi bantu untuk escape output
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Teknologi - Rest Client</title>
    <!-- CSS Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- navbar -->
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-info">
  <a class="navbar-brand" href="#">RestClient</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">News</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">About</a>
      </li>
    </ul>
  </div>
</nav>
<!-- navbar -->

<div class="container" style="margin-top: 90px; margin-bottom: 30px;">

    <!-- judul halaman -->
    <div class="mb-4">
        <h2 class="mb-1">Berita Teknologi</h2>
        <p class="text-muted mb-0">Headline teknologi terbaru dari NewsAPI.org</p>
    </div>

<?php if (!$berita_ok) { ?>

    <!-- pesan jika berita gagal diambil -->
    <div class="alert alert-danger" role="alert">
        <?php echo e($pesan_error); ?>
    </div>

<?php } else { ?>

    <div class="row">

    <!-- looping hasil data di array article -->
    <?php foreach ($hasil['articles'] as $row) {
        $author = !empty($row['author']) ? $row['author'] : "Anonim";
        $title  = !empty($row['title']) ? $row['title'] : "Tanpa judul";
        $image  = !empty($row['urlToImage']) ? $row['urlToImage'] : "";
        $link   = !empty($row['url']) ? $row['url'] : "#";
    ?>

        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
              <?php if ($image !== "") { ?>
              <img src="<?php echo e($image); ?>" class="card-img-top" alt="" style="height: 180px; object-fit: cover;">
              <?php } else { ?>
              <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 180px;">
                  Tidak ada gambar
              </div>
              <?php } ?>
              <div class="card-body d-flex flex-column">
                <h6 class="card-title font-weight-bold"><?php echo e($title); ?></h6>
                <p class="text-muted small mb-3"><i>Oleh <?php echo e($author); ?></i></p>
                <a href="<?php echo e($link); ?>" target="_blank" rel="noopener" class="btn btn-info btn-sm btn-block mt-auto">Selengkapnya &raquo;</a>
              </div>
            </div>
        </div>

    <?php } ?>

    </div>

<?php } ?>

</div>

<!-- JS Bootstrap -->
<script src="js/jquery-3.4.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>