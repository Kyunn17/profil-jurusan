<?php
include 'config/koneksi.php';

// CREATE
if (isset($_POST['simpan'])) {
    $judul   = $_POST['judul'];
    $isi     = $_POST['isi'];
    $tanggal = $_POST['tanggal_post'];

    $foto = null;
    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto = addslashes(file_get_contents($_FILES['foto']['tmp_name']));
    }

    mysqli_query($koneksi, "INSERT INTO berita (judul, isi, tanggal_post, foto) 
                            VALUES ('$judul','$isi','$tanggal','$foto')");
    header("Location: home.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id      = $_POST['id_berita'];
    $judul   = $_POST['judul'];
    $isi     = $_POST['isi'];
    $tanggal = $_POST['tanggal_post'];

    if (!empty($_FILES['foto']['tmp_name'])) {
        $foto = addslashes(file_get_contents($_FILES['foto']['tmp_name']));
        mysqli_query($koneksi, "UPDATE berita 
                                SET judul='$judul', isi='$isi', tanggal_post='$tanggal', foto='$foto' 
                                WHERE id_berita='$id'");
    } else {
        mysqli_query($koneksi, "UPDATE berita 
                                SET judul='$judul', isi='$isi', tanggal_post='$tanggal' 
                                WHERE id_berita='$id'");
    }

    header("Location: home.php");
    exit;
}

// DELETE
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM berita WHERE id_berita='$id'");
    header("Location: home.php");
    exit;
}

// AMBIL DATA
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($koneksi, "SELECT * FROM berita WHERE id_berita='$id'");
    $editData = mysqli_fetch_assoc($result);
}
$berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - PPLG FUTURISTIC</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .hero{
    
    background:
        linear-gradient(135deg, rgba(15,23,42,0.8), rgba(30,41,59,0.8)),
        url('../bg.jpg') center center / cover no-repeat;
    padding: 80px 20px;     
}

        .berita-container {width:90%; margin:30px auto;}
        .berita-form {margin-bottom:30px; border:1px solid #ddd; padding:15px; border-radius:8px;}
        .berita-form input, .berita-form textarea {width:100%; margin:5px 0; padding:8px;}
        .berita-card {display:flex; gap:15px; border:1px solid #ddd; border-radius:10px; margin-bottom:20px; padding:15px;}
        .berita-card img {width:200px; border-radius:10px; object-fit:cover;}
        .berita-content {flex:1;}
        .berita-actions a, .berita-actions button {margin-right:10px; text-decoration:none; padding:6px 10px; border-radius:5px;}
        .edit-btn {background:#3498db; color:#fff;}
        .delete-btn {background:#e74c3c; color:#fff;}
        .save-btn {background:#2ecc71; color:#fff; padding:8px 15px; border:none; border-radius:5px;}
    </style>
</head>
<body>
<header class="header">
    <div class="logo">🌐 PPLG FUTURISTIC</div>
    <div class="menu">
        <a href="home.php">Beranda</a>
        <a href="siswa.php">Siswa</a>
        <a href="guru.php">Guru</a>
        <a href="mapel.php">Mata Pelajaran</a>
        <a href="mapel_diampu.php">Mapel Diampu</a>
        <a href="nilai.php">Nilai</a>
        <a href="kelas.php">Kelas</a>
        <a href="absensi.php">Absensi</a>
        <a href="search.php">Pencarian</a>
        <a href="../index.php" class="btn btn-success">Log Out</a>
    </div>
</header>

<section class="hero">
    <h1>Selamat Datang ADMIN </h1>
    <p>Sistem Informasi Akademik SMK berbasis web yang modern 🚀</p>
</section>

<section class="cards">
    <div class="card">
        <h3>👨‍🎓 Siswa</h3>
        <p>Kelola data siswa</p>
        <a href="siswa.php">Masuk</a>
    </div>
    <div class="card">
        <h3>👨‍🏫 Guru</h3>
        <p>Kelola data guru</p>
        <a href="guru.php">Masuk</a>
    </div>
    <div class="card">
        <h3>📚 Mata Pelajaran</h3>
        <p>Daftar mata pelajaran</p>
        <a href="mapel.php">Masuk</a>
    </div>
    <div class="card">
        <h3>📊 Nilai</h3>
        <p>Rekap nilai siswa</p>
        <a href="nilai.php">Masuk</a>
    </div>
    <div class="card">
        <h3>🏫 Kelas</h3>
        <p>Manajemen kelas</p>
        <a href="kelas.php">Masuk</a>
    </div>
    <div class="card">
        <h3>📚 Mapel Diampu</h3>
        <p>mapel yang diampu guru</p>
        <a href="mapel_diampu.php">Log Out</a>
    </div>
    <div class="card">
        <h3>👨‍🎓 Absensi</h3>
        <p>Input Absensi</p>
        <a href="absensi.php">Cari</a>
    </div>
    <div class="card">
        <h3> Create By</h3>
        <p>XI PPLG B</p>
    </div>
</section>

<!-- SECTION BERITA -->
<section class="berita-container">
    <h2>📰 Berita Terbaru</h2>
    <a href="aksitambah/tambah_berita.php" class="btn btn-success" >+ Tambah Berita</a>
    <br><br>

     <!-- Card Berita -->
    <?php while($row = mysqli_fetch_assoc($berita)) { ?>
    <div class="berita-card">
        <?php if (!empty($row['foto'])) { ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($row['foto']); ?>" alt="Foto Berita">
        <?php } ?>
        <div class="berita-content">
            <h3><?= $row['judul']; ?></h3>
            <small><?= $row['tanggal_post']; ?></small>
            <p><?= nl2br($row['isi']); ?></p>
            <div class="berita-actions">
                <a href="aksitambah/tambah_berita.php?edit=<?= $row['id_berita']; ?>" class="edit-btn">Edit</a>
                <a href="home.php?hapus=<?= $row['id_berita']; ?>" class="delete-btn" onclick="return confirm('Hapus berita?')">Hapus</a>
            </div>
        </div>
    </div>
    <?php } ?>
</section>

<footer>
    <p>&copy; <?= date("Y"); ?> Khairul Faridz - SMKN 4 PADALARANG</p>
</footer>
</body>
</html>
