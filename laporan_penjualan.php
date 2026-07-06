<?php
  session_start();
  // berasal dari login
  if(!isset($_SESSION['login'])){
    header("location: login.php");
    exit();
  }

  include 'koneksi.php';

  $start_date = date('Y-m-01');
  $end_date = date('Y-m-t');

  if(isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])){
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
  }

  $query_laporan = "SELECT * FROM penjualan WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC";
  $stmt = $conn->prepare($query_laporan);
  $stmt->bind_param("ss", $start_date, $end_date);
  $stmt->execute();
  $result_laporan = $stmt->get_result();

  $total_omset = 0;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Project UAS | Laporan Penjualan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <style>
    @media print {
      .no-print, .main-sidebar, .main-header, .content-header, .main-footer, .dataTables_filter, .dataTables_paginate, .dataTables_info, .dataTables_length {
        display: none !important;
      }
      .content-wrapper {
        margin-left: 0 !important;
      }
      .card-title-print {
        display: block !important;
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
      }
    }
    .card-title-print {
      display: none;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light no-print">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li>
        <a href="logout.php" class="btn btn-danger btn-sm">
          <i class="fas fa-sign-out-alt mr-1"></i>Log out
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4 no-print">
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a class="d-block">Alexander Pierce</a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Data Master
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="user.php" class="nav-link">
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
            <a href="laporan_penjualan.php" class="nav-link active">
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
            <h1>Laporan Penjualan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Laporan Penjualan</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header no-print">
              <h3 class="card-title">Filter Laporan</h3>
            </div>
            <div class="card-body no-print">
              <form method="GET" action="" class="form-inline">
                <div class="form-group mb-2">
                  <label for="start_date" class="mr-2">Dari Tanggal</label>
                  <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                  <label for="end_date" class="mr-2">Sampai Tanggal</label>
                  <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Tampilkan</button>
                <button type="button" class="btn btn-secondary mb-2 ml-2" onclick="window.print()">
                  <i class="fas fa-print"></i> Cetak
                </button>
              </form>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title-print">Laporan Penjualan <br> Periode <?= htmlspecialchars(date('d-m-Y', strtotime($start_date))) ?> s/d <?= htmlspecialchars(date('d-m-Y', strtotime($end_date))) ?></h3>
              <h3 class="card-title no-print">Daftar Transaksi Penjualan</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $no = 1;
                    while($row = $result_laporan->fetch_assoc()):
                      $total_omset += $row['total'];
                  ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['no_transaksi']); ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal']))); ?></td>
                    <td><?= htmlspecialchars($row['customer']); ?></td>
                    <td><?= htmlspecialchars($row['barang']); ?></td>
                    <td><?= htmlspecialchars($row['jumlah_barang']); ?></td>
                    <td class="text-right">Rp <?= htmlspecialchars(number_format($row['total'], 0, ',', '.')); ?></td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="6" class="text-right">Total Omset</th>
                    <th class="text-right">Rp <?= htmlspecialchars(number_format($total_omset, 0, ',', '.')); ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer no-print">
    <div class="float-right d-none d-sm-block"><b>Version</b> 3.0.0</div>
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>