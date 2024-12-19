<?php

class ModelAdmin {
    private $database; 

    public function __construct($host = "127.0.0.1", $dbname = "drzperpus", $username = "root", $password = "") {
        try {
            $this->database = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getAllAdmins() {
        $stmt = $this->database->query("SELECT * FROM admin");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminById($idAdmin) {
        $stmt = $this->database->prepare("SELECT * FROM admin WHERE idAdmin = :idAdmin");
        $stmt->execute(['idAdmin' => $idAdmin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addAdmin($idAdmin, $nama, $password) {
        $stmt = $this->database->prepare("INSERT INTO admin (idAdmin, nama, password) VALUES (:idAdmin, :nama, :password)");
        return $stmt->execute(['idAdmin' => $idAdmin, 'nama' => $nama, 'password' => $password]);
    }

    public function updateAdmin($idAdmin, $nama, $password) {
        $stmt = $this->database->prepare("UPDATE admin SET nama = :nama, password = :password WHERE idAdmin = :idAdmin");
        return $stmt->execute(['idAdmin' => $idAdmin, 'nama' => $nama, 'password' => $password]);
    }

    public function deleteAdmin($idAdmin) {
        $stmt = $this->database->prepare("DELETE FROM admin WHERE idAdmin = :idAdmin");
        return $stmt->execute(['idAdmin' => $idAdmin]);
    }
}

// Contoh Pengujian
$modelAdmin = new ModelAdmin();

// 1. Menambahkan Admin Baru
echo "\nMenambahkan Admin Baru:\n";
$modelAdmin->addAdmin(5, "Ade", "12345");
print_r($modelAdmin->getAllAdmins());

// 2. Mengambil Semua Admin
echo "\nMengambil Semua Admin:\n";
print_r($modelAdmin->getAllAdmins());

// 3. Mengambil Admin Berdasarkan ID
echo "\nMengambil Admin dengan ID 1:\n";
print_r($modelAdmin->getAdminById(1));

// 4. Memperbarui Admin
echo "\nMemperbarui Admin dengan ID 1:\n";
$modelAdmin->updateAdmin(1, "jeno", "12345");
print_r($modelAdmin->getAdminById(1));

// 5. Menghapus Admin
echo "\nMenghapus Admin dengan ID 2:\n";
$modelAdmin->deleteAdmin(2);
print_r($modelAdmin->getAllAdmins());
?>
