<?php
  session_start();
  // berasal dari login
  if(!isset($_SESSION['login'])){
    header("location: login.php");
    exit();
  }

  include 'koneksi.php';

  // Hitung jumlah user
  $stmt_users = $conn->prepare("SELECT COUNT(id) as total_users FROM user");
  $stmt_users->execute();
  $result_users = $stmt_users->get_result();
  $total_users = $result_users->fetch_assoc()['total_users'];
  $stmt_users->close();

  // Hitung jumlah transaksi dan total omset penjualan
  $stmt_penjualan = $conn->prepare("SELECT COUNT(id) as total_penjualan, SUM(total) as total_omset FROM penjualan");
  $stmt_penjualan->execute();
  $result_penjualan = $stmt_penjualan->get_result();
  $data_penjualan = $result_penjualan->fetch_assoc();
  $total_penjualan = $data_penjualan['total_penjualan'] ?? 0;
  $total_omset = $data_penjualan['total_omset'] ?? 0;
  $stmt_penjualan->close();

  // Hitung jumlah transaksi dan total biaya pembelian
  $stmt_pembelian = $conn->prepare("SELECT COUNT(id) as total_pembelian, SUM(total) as total_biaya FROM pembelian");
  $stmt_pembelian->execute();
  $result_pembelian = $stmt_pembelian->get_result();
  $data_pembelian = $result_pembelian->fetch_assoc();
  $total_pembelian = $data_pembelian['total_pembelian'] ?? 0;
  $total_biaya_pembelian = $data_pembelian['total_biaya'] ?? 0;
  $stmt_pembelian->close();

  // Hitung Keuntungan
  $keuntungan = $total_omset - $total_biaya_pembelian;

  // Data untuk Chart Penjualan dan Pembelian Bulanan
  $tahun_sekarang = date('Y');
  // Data Penjualan
  $query_chart_penjualan = "
      SELECT 
          MONTH(tanggal) as bulan, 
          SUM(total) as total_bulanan 
      FROM penjualan 
      WHERE YEAR(tanggal) = ?
      GROUP BY MONTH(tanggal)
      ORDER BY MONTH(tanggal) ASC";
  $stmt_chart_penjualan = $conn->prepare($query_chart_penjualan);
  $stmt_chart_penjualan->bind_param("i", $tahun_sekarang);
  $stmt_chart_penjualan->execute();
  $result_chart_penjualan = $stmt_chart_penjualan->get_result();

  $total_data_penjualan = array_fill(0, 12, 0); // Inisialisasi array penjualan dengan 12 nilai 0
  $nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

  // Proses hasil query penjualan
  while ($row = $result_chart_penjualan->fetch_assoc()) {
      $total_data_penjualan[$row['bulan'] - 1] = $row['total_bulanan'];
  }
  $stmt_chart_penjualan->close();

  // Data Pembelian
  $query_chart_pembelian = "
      SELECT 
          MONTH(tanggal) as bulan, 
          SUM(total) as total_bulanan 
      FROM pembelian 
      WHERE YEAR(tanggal) = ?
      GROUP BY MONTH(tanggal)
      ORDER BY MONTH(tanggal) ASC";
  $stmt_chart_pembelian = $conn->prepare($query_chart_pembelian);
  $stmt_chart_pembelian->bind_param("i", $tahun_sekarang);
  $stmt_chart_pembelian->execute();
  $result_chart_pembelian = $stmt_chart_pembelian->get_result();
  
  $total_data_pembelian = array_fill(0, 12, 0); // Inisialisasi array pembelian dengan 12 nilai 0

  // Proses hasil query pembelian
  while ($row = $result_chart_pembelian->fetch_assoc()) {
      // array index is 0-11, but month is 1-12
      $total_data_pembelian[$row['bulan'] - 1] = $row['total_bulanan'];
  }
  $stmt_chart_pembelian->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Project UAS | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <style>
    .nav-sidebar .nav-header:not(:first-of-type) {
      padding: 1rem 1rem 0.5rem;
    }
    /* Custom style untuk memperbesar font di mobile */
    @media (max-width: 767px) {
      .small-box h3 {
        font-size: 2.3rem;
      }

      .small-box p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li>
        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#logoutModal">
          <i class="fas fa-sign-out-alt mr-1"></i>Log out
        </button>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a class="d-block"><?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Pengguna'; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
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
              <p>
                Penjualan
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pembelian.php" class="nav-link">
              <i class="nav-icon far fa-image"></i>
              <p>
                Pembelian
              </p>
            </a>
          </li>

          <li class="nav-header">LAPORAN</li>
          <li class="nav-item">
            <a href="laporan_penjualan.php" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>Laporan Penjualan</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= number_format($total_penjualan); ?></h3>
                <p>Jumlah Transaksi Penjualan</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="penjualan.php" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>Rp <?= number_format($total_omset, 0, ',', '.'); ?></h3>
                <p>Total Omset Penjualan</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="penjualan.php" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= number_format($total_users); ?></h3>
                <p>Jumlah Pengguna Terdaftar</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="user.php" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                <h3><?= number_format($total_pembelian); ?></h3>
                <p>Jumlah Transaksi Pembelian</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-cart"></i>
              </div>
              <a href="pembelian.php" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>Rp <?= number_format($total_biaya_pembelian, 0, ',', '.'); ?></h3>
                <p>Total Biaya Pembelian</p>
              </div>
              <div class="icon">
                <i class="ion ion-arrow-graph-down-right"></i>
              </div>
              <a href="pembelian.php" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <!-- small box -->
            <div class="small-box bg-purple">
              <div class="inner">
                <h3>Rp <?= number_format($keuntungan, 0, ',', '.'); ?></h3>
                <p>Keuntungan</p>
              </div>
              <div class="icon">
                <i class="ion ion-arrow-graph-up-right"></i>
              </div>
              <a href="laporan_penjualan.php" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
            <!-- Sales chart -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-line mr-1"></i>
                  Grafik Penjualan vs Pembelian Tahun <?= $tahun_sekarang ?>
                </h3>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="chart">
                  <canvas id="monthlyBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
          <!-- /.Left col -->

          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-5 connectedSortable">
            <!-- Donut Chart -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  Komposisi Omset
                </h3>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="profitChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.0.0
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- page script -->
<script>
  $(function() {
    'use strict'

    // This will get the first returned node in the jQuery collection.
    var salesChart = new Chart($('#monthlyBarChart').get(0).getContext('2d'), { 
      type: 'bar', 
      data: {
        labels: <?= json_encode($nama_bulan) ?>,
        datasets: [
          {
            label: 'Omset Penjualan',
            backgroundColor: '#28a745', // Warna hijau (bg-success)
            hoverBackgroundColor: '#218838', // Warna hijau lebih gelap saat hover
            borderColor: '#28a745',
            data: <?= json_encode($total_data_penjualan) ?>,
          },
          {
            label: 'Biaya Pembelian',
            backgroundColor: '#dc3545', // Warna merah (bg-danger)
            hoverBackgroundColor: '#c82333', // Warna merah lebih gelap saat hover
            borderColor: '#dc3545',
            data: <?= json_encode($total_data_pembelian) ?>
          }
        ]
      }, 
      options: {
        maintainAspectRatio: false,
        responsive: true,
        animations: {
          y: {
            duration: 800,
            easing: 'easeInOutQuad'
          }
        },
        plugins: {
          legend: {
            display: true
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            grid: {
              display: true
            },
            ticks: {
              beginAtZero: true
            }
          }
        }
      }
    })

    //-------------
    //- DONUT CHART -
    //-------------
    var profitChartCanvas = $('#profitChart').get(0).getContext('2d')
    var profitChartData = {
      labels: [ 'Biaya Pembelian', 'Keuntungan' ],
      datasets: [ {
          data: [<?= $total_biaya_pembelian ?>, <?= $keuntungan ?>],
          backgroundColor: ['#dc3545', '#6f42c1'], // bg-danger, bg-purple
      }]
    }
    var profitChart = new Chart(profitChartCanvas, {
      type: 'doughnut',
      data: profitChartData
    })
  });
</script>

<!-- Modal Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Log Out</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin keluar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <a href="logout.php" class="btn btn-danger">Log Out</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>