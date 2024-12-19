<?php
session_start();

// Cek login pengguna
if (!isset($_SESSION["signIn"])) {
    header("Location: ../../sign/member/sign_in.php");
    exit;
}

require "../config/config.php";

// Fungsi untuk menjalankan query database
function queryReadData($query) {
    $pdo = new PDO("mysql:host=localhost;dbname=drzperpus", "root", "");
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Query untuk menampilkan semua buku
$buku = queryReadData("SELECT * FROM buku");

// Filter kategori buku
if (isset($_POST["informatika"])) {
    $buku = queryReadData("SELECT * FROM buku WHERE kategori = 'informatika'");
} elseif (isset($_POST["bisnis"])) {
    $buku = queryReadData("SELECT * FROM buku WHERE kategori = 'bisnis'");
} elseif (isset($_POST["filsafat"])) {
    $buku = queryReadData("SELECT * FROM buku WHERE kategori = 'filsafat'");
} elseif (isset($_POST["novel"])) {
    $buku = queryReadData("SELECT * FROM buku WHERE kategori = 'novel'");
} elseif (isset($_POST["sains"])) {
    $buku = queryReadData("SELECT * FROM buku WHERE kategori = 'sains'");
}

// Pencarian buku berdasarkan judul atau kategori
if (isset($_POST["search"])) {
    $keyword = $_POST["keyword"];
    $buku = queryReadData("SELECT * FROM buku WHERE judul LIKE '%$keyword%' OR kategori LIKE '%$keyword%'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card img {
            height: 250px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Perpustakaan</a>
            <form action="" method="post" class="d-flex">
                <input type="text" name="keyword" class="form-control me-2" placeholder="Cari buku...">
                <button class="btn btn-outline-light" type="submit" name="search">Cari</button>
            </form>
        </div>
    </nav>

    <!-- Filter Kategori -->
    <div class="container mt-4">
        <form action="" method="post" class="d-flex justify-content-center gap-3">
            <button class="btn btn-primary" type="submit" name="informatika">Informatika</button>
            <button class="btn btn-primary" type="submit" name="bisnis">Bisnis</button>
            <button class="btn btn-primary" type="submit" name="filsafat">Filsafat</button>
            <button class="btn btn-primary" type="submit" name="novel">Novel</button>
            <button class="btn btn-primary" type="submit" name="sains">Sains</button>
        </form>
    </div>

    <!-- Daftar Buku -->
    <div class="container mt-5">
        <div class="row">
            <?php foreach ($buku as $item): ?>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <img src="../imgDB/<?= $item["cover"]; ?>" class="card-img-top" alt="<?= $item["judul"]; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $item["judul"]; ?></h5>
                            <p class="card-text">Kategori: <?= $item["kategori"]; ?></p>
                            <p class="card-text">Id Buku: <?= $item["idBuku"]; ?></p>
                        </div>
                        <div class="card-body d-flex justify-content-between">
                            <a href="editBuku.php?id=<?= $item["idBuku"]; ?>" class="btn btn-success">Edit</a>
                            <a href="deleteBuku.php?id=<?= $item["idBuku"]; ?>" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
