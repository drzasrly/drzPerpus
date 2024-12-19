<!-- <?php

function koneksi() {
    $host = 'localhost'; // Ganti dengan host database Anda
    $user = 'root';      // Ganti dengan username database Anda
    $password = '';      // Ganti dengan password database Anda
    $database = 'drzperpus'; // Ganti dengan nama database Anda

    $connection = new mysqli($host, $user, $password, $database);

    // Periksa apakah koneksi berhasil
    if ($connection->connect_error) {
        die("Koneksi database gagal: " . $connection->connect_error);
    }

    return $connection;
}


//require_once '/../drzperpus/connect.php';
require_once 'bukuModel.php';
class BukuModelTest {
    private $connection;
    private $bukuModel;

    public function runTests() {
        $this->setUp();
        $this->testTambahBukuBerhasil();
        $this->testTambahBukuGagalUpload();
        $this->tearDown();
    }

    protected function setUp(): void {
        // Koneksi database
        $this->connection = koneksi();
        if (!$this->connection) {
            echo "Koneksi database gagal.\n";
            exit;
        }

        // Membuat mock sederhana untuk upload
        $this->bukuModel = new BukuModel($this->connection);
    }

    public function testTambahBukuBerhasil() {
        $dataBuku = [
            "cover" => "hidhhsinhfciu",
            "idBuku" => "B001",
            "kategori" => "Fiksi",
            "judul" => "Buku Tes",
            "author" => "Pengarang Tes",
            "penerbit" => "Penerbit Tes",
            "tahunTerbit" => "2023",
            "sinopsis" => "Ini adalah sinopsis tes.",
            "jumlahCopy" => 5
        ];

        $result = $this->bukuModel->tambahBuku($dataBuku);
        echo $result > 0 ? "Test Tambah Buku Berhasil: OK\n" : "Test Tambah Buku Berhasil: FAILED\n";

        $query = "SELECT * FROM buku WHERE idBuku = 'B001'";
        $result = $this->connection->query($query);
        echo $result->num_rows > 0 ? "Verifikasi Data: OK\n" : "Verifikasi Data: FAILED\n";
    }

    public function testTambahBukuGagalUpload() {
        // Simulasi upload gagal
        $_FILES['cover'] = [
            'name' => '',
            'error' => 4 // Tidak ada file diunggah
        ];

        $dataBuku = [
            "idBuku" => "B002",
            "kategori" => "Non-Fiksi",
            "judul" => "Buku Tes Gagal",
            "author" => "Pengarang Tes",
            "penerbit" => "Penerbit Tes",
            "tahunTerbit" => "2023",
            "sinopsis" => "Ini adalah sinopsis tes.",
            "jumlahCopy" => 3
        ];

        $result = $this->bukuModel->tambahBuku($dataBuku);
        echo $result === 0 ? "Test Tambah Buku Gagal Upload: OK\n" : "Test Tambah Buku Gagal Upload: FAILED\n";
    }

    protected function tearDown(): void {
        $this->connection->query("DELETE FROM buku WHERE idBuku = 'B001'");
        $this->connection->query("DELETE FROM buku WHERE idBuku = 'B002'");
        $this->connection->close();
    }
}

$test = new BukuModelTest();
$test->runTests();

?> -->