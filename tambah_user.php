<?php
include 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    if ($nama_lengkap === '' || $username === '' || $password === '') {
        $error = 'Semua field harus diisi.';
    } else {
        $insert = mysqli_query(
            $conn,
            "INSERT INTO user (nama_lengkap, username, password) VALUES ('$nama_lengkap', '$username', '$password')"
        );

        if ($insert) {
            header('Location: user.php');
            exit;
        }

        $error = 'Gagal menambahkan user: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Tambah User - Project UAS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li>
        <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Apakah yakin Log Out?')">
        <i class="fas fa-sign-out-alt mr-1"></i>Log out
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-table"></i>
              <p>Data Master <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="user.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Data User</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">TRANSAKSI</li>
          <li class="nav-item">
            <a href="penjualan.php" class="nav-link">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>Penjualan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pembelian.php" class="nav-link">
              <i class="nav-icon far fa-image"></i>
              <p>Pembelian</p>
            </a>
          </li>
          <li class="nav-header">LAPORAN</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>Laporan Penjualan</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Tambah User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="user.php">Data User</a></li>
              <li class="breadcrumb-item active">Tambah User</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Form Tambah User</h3>
            </div>
            <form method="POST" action="">
              <div class="card-body">
                <?php if ($error !== ''): ?>
                  <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="form-group">
                  <label for="nama_lengkap">Nama Lengkap</label>
                  <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="text" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>
              </div>
              <div class="card-footer">
                <a href="user.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.0.0
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
