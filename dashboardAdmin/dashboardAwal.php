<?php
session_start();

// // Cek login pengguna
// if (!isset($_SESSION["signIn"])) {
//     header("Location: ../../sign/member/sign_in.php");
//     exit;
// }

require "../config/config.php";

// Fungsi untuk membuat koneksi database
function createPDOConnection() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=drzperpus", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// Fungsi untuk menjalankan query database
function queryReadData($query, $params = []) {
    $pdo = createPDOConnection();
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Query gagal: " . $e->getMessage());
    }
}

// Query rekomendasi buku berdasarkan jumlah copy terbanyak
$rekomendasiBuku = queryReadData("SELECT * FROM buku ORDER BY jumlahCopy DESC LIMIT 3");

// Query default untuk daftar semua buku
$bukuQuery = "SELECT * FROM buku";
$bukuParams = [];

// Filter kategori buku jika ada input dari user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["search"])) {
        $keyword = filter_input(INPUT_POST, 'keyword', FILTER_SANITIZE_STRING);
        $bukuQuery .= " WHERE judul LIKE :keyword OR kategori LIKE :keyword";
        $bukuParams[':keyword'] = '%' . $keyword . '%';
    } else {
        $kategori = filter_input(INPUT_POST, 'kategori', FILTER_SANITIZE_STRING);
        if (in_array($kategori, ['informatika', 'bisnis', 'filsafat', 'novel', 'sains'], true)) {
            $bukuQuery .= " WHERE kategori = :kategori";
            $bukuParams[':kategori'] = $kategori;
        }
    }
}

// Ambil data buku berdasarkan query
$buku = queryReadData($bukuQuery, $bukuParams);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://source.unsplash.com/1600x600/?library');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-section h1 { font-size: 3rem; }
        .card img { height: 250px; object-fit: cover; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Perpustakaan Digital</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#kategori">Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rekomendasi">Rekomendasi</a></li>
                </ul>
                <form action="" method="post" class="d-flex">
                    <input type="text" name="keyword" class="form-control me-2" placeholder="Cari buku...">
                    <button class="btn btn-outline-light" type="submit" name="search">Cari</button>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">        
                    <li class="nav-item"><a class="nav-link" href="singIn.php">LOGIN</a></li>
                    </ul>
                </form>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Selamat Datang di Perpustakaan Digital</h1>
            <p>Jelajahi ribuan buku, video, dan audio di ujung jari Anda.</p>
        </div>
    </section>

    <!-- Kategori Section -->
    <section id="kategori" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Kategori Buku</h2>
            <form action="" method="post" class="d-flex justify-content-center gap-3 mb-4">
                <button class="btn btn-primary" type="submit" name="kategori" value="informatika">Informatika</button>
                <button class="btn btn-primary" type="submit" name="kategori" value="bisnis">Bisnis</button>
                <button class="btn btn-primary" type="submit" name="kategori" value="filsafat">Filsafat</button>
                <button class="btn btn-primary" type="submit" name="kategori" value="novel">Novel</button>
                <button class="btn btn-primary" type="submit" name="kategori" value="sains">Sains</button>
            </form>
            <div class="row">
                <?php foreach ($buku as $book): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="../imgDB/<?= $book["cover"]; ?>" class="card-img-top" alt="<?= htmlspecialchars($book['judul']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($book['judul']); ?></h5>
                                <p class="card-text">Kategori: <?= htmlspecialchars($book['kategori']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Rekomendasi Buku Section -->
    <section id="rekomendasi" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Rekomendasi Buku</h2>
            <div class="row">
                <?php foreach ($rekomendasiBuku as $book): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="../imgDB/<?= $book["cover"]; ?>" class="card-img-top" alt="<?= htmlspecialchars($book['judul']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($book['judul']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($book['sinopsis']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
