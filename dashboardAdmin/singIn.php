<?php
require_once __DIR__ . '/../config/config.php';

// Tangkap data dari form login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama']; // Nama pengguna
    $password = $_POST['password']; // Password

    // Query untuk mencari pengguna di semua tabel
    $sql = "
        SELECT 'Admin' AS role, a.namaAdmin AS nama, a.passwordAdmin AS password
        FROM admin a
        JOIN role r ON r.idAdmin = a.idAdmin
        WHERE a.namaAdmin = ? 
        UNION
        SELECT 'Petugas' AS role, p.namaPetugas AS nama, p.passwordPetugas AS password
        FROM petugas p
        JOIN role r ON r.idPetugas = p.idPetugas
        WHERE p.namaPetugas = ?
        UNION
        SELECT 'Member' AS role, m.namaMember AS nama, m.passwordMember AS password
        FROM member m
        JOIN role r ON r.idMember = m.idMember
        WHERE m.namaMember = ?";

    // Persiapkan query dan eksekusi
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nama, $nama, $nama);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            echo "Login berhasil sebagai " . $row['role'] . "!";
            // Tambahkan pengalihan atau aksi lain di sini
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Nama pengguna tidak ditemukan!";
    }

    $stmt->close();
}

//$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/de8de52639.js" crossorigin="anonymous"></script>
    <title>Sign In || Admin</title>
</head>
<body>
<div class="container">
    <div class="card p-2 mt-5">
        <div class="position-absolute top-0 start-50 translate-middle">
            <img src="../../assets/adminLogo.png" class="" alt="adminLogo" width="85px">
        </div>
        <h1 class="pt-5 text-center fw-bold">Sign In</h1>
        <hr>
        <form action="" method="post" class="row g-3 p-4 needs-validation" novalidate>
            <label for="validationCustom01" class="form-label">Nama Lengkap</label>
            <div class="input-group mt-0">
                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-user"></i></span>
                <input type="text" class="form-control" name="nama" id="validationCustom01" required>
                <div class="invalid-feedback">
                    Masukkan Nama anda!
                </div>
            </div>
            <label for="validationCustom02" class="form-label">Password</label>
            <div class="input-group mt-0">
                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="validationCustom02" name="password" required>
                <div class="invalid-feedback">
                    Masukkan Password anda!
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit" name="signIn">Sign In</button>
                <a class="btn btn-success" href="../link_login.html">Batal</a>
            </div>
        </form>

        <!-- Display error if any -->
        <?php if(isset($error)) : ?>
            <div class="alert alert-danger mt-2" role="alert"><?= $error; ?></div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
