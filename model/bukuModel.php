<?php

class ModelBuku {
    private $database;

    public function __construct($host = "127.0.0.1", $dbname = "drzperpus", $username = "root", $password = "") {
        try {
            $this->database = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Mendapatkan semua buku
    public function getAllBuku() {
        try {
            $stmt = $this->database->query("SELECT * FROM buku");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error fetching books: " . $e->getMessage();
        }
    }

    // Mendapatkan buku berdasarkan ID
    public function getBukuById($idBuku) {
        try {
            $stmt = $this->database->prepare("SELECT * FROM buku WHERE idBuku = :idBuku");
            $stmt->execute(['idBuku' => $idBuku]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error fetching book by ID: " . $e->getMessage();
        }
    }

    public function addBuku($idBuku, $kategori, $judul, $author, $penerbit, $tahunTerbit, $sinopsis, $jumlahCopy, $cover) {
        try {
            // Validasi file cover
            if (!empty($cover['name']) && !$this->validateCover($cover)) {
                return "Invalid cover file. Only JPG, JPEG, and PNG are allowed.";
            }
    
            // Masukkan data buku ke database
            $stmt = $this->database->prepare(
                "INSERT INTO buku (idBuku, kategori, judul, author, penerbit, tahunTerbit, sinopsis, jumlahCopy, cover) 
                 VALUES (:idBuku, :kategori, :judul, :author, :penerbit, :tahunTerbit, :sinopsis, :jumlahCopy, :cover)"
            );
    
            $stmt->execute([
                'idBuku' => $idBuku,
                'kategori' => $kategori,  // Simpan kategori sebagai string, bukan ID
                'judul' => $judul,
                'author' => $author,
                'penerbit' => $penerbit,
                'tahunTerbit' => $tahunTerbit,
                'sinopsis' => $sinopsis,
                'jumlahCopy' => $jumlahCopy,
                'cover' => !empty($cover['name']) ? $cover['name'] : null
            ]);
    
            return "Buku berhasil ditambahkan.";
        } catch (PDOException $e) {
            return "Error adding book: " . $e->getMessage();
        }
    }
    
    // Memperbarui buku
    public function updateBuku($idBuku, $kategori, $judul, $author, $penerbit, $tahunTerbit, $sinopsis, $jumlahCopy, $cover) {
        try {
            // Periksa cover baru
            $currentBuku = $this->getBukuById($idBuku);
            if (!$currentBuku) {
                return "Buku dengan ID $idBuku tidak ditemukan.";
            }

            $coverName = !empty($cover['name']) && $this->validateCover($cover) ? $cover['name'] : $currentBuku['cover'];

            // Update data buku
            $stmt = $this->database->prepare(
                "UPDATE buku 
                 SET kategori = :kategori, judul = :judul, author = :author, penerbit = :penerbit, tahunTerbit = :tahunTerbit, 
                     sinopsis = :sinopsis, jumlahCopy = :jumlahCopy, cover = :cover 
                 WHERE idBuku = :idBuku"
            );

            $stmt->execute([
                'idBuku' => $idBuku,
                'kategori' => $kategori,  // Simpan kategori sebagai string, bukan ID
                'judul' => $judul,
                'author' => $author,
                'penerbit' => $penerbit,
                'tahunTerbit' => $tahunTerbit,
                'sinopsis' => $sinopsis,
                'jumlahCopy' => $jumlahCopy,
                'cover' => $coverName
            ]);

            return "Buku berhasil diperbarui.";
        } catch (PDOException $e) {
            return "Error updating book: " . $e->getMessage();
        }
    }

    // Menghapus buku
    public function deleteBuku($idBuku) {
        try {
            $stmt = $this->database->prepare("DELETE FROM buku WHERE idBuku = :idBuku");
            $stmt->execute(['idBuku' => $idBuku]);
            return "Buku berhasil dihapus.";
        } catch (PDOException $e) {
            return "Error deleting book: " . $e->getMessage();
        }
    }

    // Validasi kategori (optional)
    private function kategoriExists($kategori) {
        $stmt = $this->database->prepare("SELECT COUNT(*) FROM buku WHERE kategori = :kategori");
        $stmt->execute(['kategori' => $kategori]);
        return $stmt->fetchColumn() > 0;
    }

    // Validasi file cover
    private function validateCover($cover) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = pathinfo($cover['name'], PATHINFO_EXTENSION);
        return in_array(strtolower($fileExtension), $allowedExtensions);
    }
}

// Contoh Pengujian
$modelBuku = new ModelBuku();

// Tambah Buku Baru
$coverFile1 = ['name' => 'example2.png'];

// Tambahkan buku pertama dengan kategori "Bisnis"
echo $modelBuku->addBuku(
    "bis003",
    "bisnis",  // Kategori bisa digunakan berulang kali
    "Buku Bisnis Pertama",
    "Author A",
    "Publisher A",
    "2023",
    "Sinopsis Buku Bisnis Pertama",
    5,
    $coverFile1
);
print_r($modelBuku->getAllBuku());
