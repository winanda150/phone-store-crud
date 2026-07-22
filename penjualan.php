<?php
  session_start();
  // berasal dari login
  if(!isset($_SESSION['login'])){
    header("location: login.php");
    exit();
  }

  include 'koneksi.php';

  $error = '';
  $success = '';
  if (isset($_SESSION['error'])) { $error = $_SESSION['error']; unset($_SESSION['error']); }
  if (isset($_SESSION['success'])) { $success = $_SESSION['success']; unset($_SESSION['success']); }

  $tanggal_hari_ini = date('Y-m-d');
  $prefix = 'TRJ-' . date('Ymd') . '-';

  // Query untuk mendapatkan no_transaksi terakhir hari ini (lebih aman)
  $stmt_last_gen = $conn->prepare("SELECT no_transaksi FROM penjualan WHERE tanggal = ? ORDER BY id DESC LIMIT 1");
  $stmt_last_gen->bind_param("s", $tanggal_hari_ini);
  $stmt_last_gen->execute();
  $result_last_gen = $stmt_last_gen->get_result();

  if ($result_last_gen->num_rows > 0) {
      $last_transaksi = $result_last_gen->fetch_assoc()['no_transaksi'];
      $last_urut = intval(substr($last_transaksi, strlen($prefix)));
      $urut_baru = $last_urut + 1;
  } else {
      $urut_baru = 1;
  }
  $no_transaksi_otomatis = $prefix . str_pad($urut_baru, 4, '0', STR_PAD_LEFT);
  $stmt_last_gen->close();
  // --- Akhir Logika Generate No Transaksi ---

  // Logika untuk menambah data penjualan
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_penjualan'])) {
      $no_transaksi = mysqli_real_escape_string($conn, trim($_POST['no_transaksi']));
      $tanggal = mysqli_real_escape_string($conn, trim($_POST['tanggal']));
      $customer = mysqli_real_escape_string($conn, trim($_POST['customer']));
      $barang = mysqli_real_escape_string($conn, trim($_POST['barang']));
      $jumlah_barang = intval($_POST['jumlah_barang']);
      $total = mysqli_real_escape_string($conn, trim($_POST['total']));
      // Hapus karakter non-numerik dari total
      $total_numeric = preg_replace('/[^0-9]/', '', $total);

      if ($no_transaksi === '' || $tanggal === '' || $customer === '' || $barang === '' || $jumlah_barang <= 0 || $total_numeric === '') {
          $error = 'Semua field harus diisi.';
      } else {
          $stmt = $conn->prepare("INSERT INTO penjualan (no_transaksi, tanggal, customer, barang, jumlah_barang, total) VALUES (?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssssii", $no_transaksi, $tanggal, $customer, $barang, $jumlah_barang, $total_numeric);
          if ($stmt->execute()) {
              $_SESSION['success'] = 'Transaksi penjualan baru berhasil ditambahkan.';
          } else {
              $_SESSION['error'] = 'Gagal menambahkan transaksi: ' . $stmt->error;
          }
          $stmt->close();
          header("Location: penjualan.php");
          exit();
      }
  }

  // Logika untuk mengedit data penjualan
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_penjualan'])) {
      $id = intval($_POST['id']);
      $no_transaksi = mysqli_real_escape_string($conn, trim($_POST['no_transaksi']));
      $tanggal = mysqli_real_escape_string($conn, trim($_POST['tanggal']));
      $customer = mysqli_real_escape_string($conn, trim($_POST['customer']));
      $barang = mysqli_real_escape_string($conn, trim($_POST['barang']));
      $jumlah_barang = intval($_POST['jumlah_barang']);
      $total = mysqli_real_escape_string($conn, trim($_POST['total']));
      // Hapus karakter non-numerik dari total
      $total_numeric = preg_replace('/[^0-9]/', '', $total);

      if ($no_transaksi === '' || $tanggal === '' || $customer === '' || $barang === '' || $jumlah_barang <= 0 || $total_numeric === '') {
          $error = 'Semua field harus diisi.';
      } else {
          $stmt = $conn->prepare("UPDATE penjualan SET no_transaksi=?, tanggal=?, customer=?, barang=?, jumlah_barang=?, total=? WHERE id=?");
          $stmt->bind_param("ssssiii", $no_transaksi, $tanggal, $customer, $barang, $jumlah_barang, $total_numeric, $id);

          if ($stmt->execute()) {
              $_SESSION['success'] = 'Transaksi berhasil diperbarui.';
          } else {
              $_SESSION['error'] = 'Gagal mengubah transaksi: ' . $stmt->error;
          }
          $stmt->close();
          header("Location: penjualan.php");
          exit();
      }
  }

  $data_penjualan = mysqli_query($conn, "SELECT * FROM penjualan ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Project UAS | Penjualan</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <style>
    .nav-sidebar .nav-header:not(:first-of-type) {
      padding: 1rem 1rem 0.5rem;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
  <!-- Navbar -->
<div class="wrapper">
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
          <a class="d-block">Alexander Pierce</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
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
            <a href="penjualan.php" class="nav-link active">
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
            <h1>Data Penjualan</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Penjualan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Daftar Transaksi Penjualan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addPenjualanModal">
                    <i class="fas fa-plus"></i> Tambah Penjualan
                </button>

                <?php if ($error !== ''): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>
                <?php if ($success !== ''): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <?php endif; ?>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Barang</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          $no = 1;
                          while($row = mysqli_fetch_assoc($data_penjualan)):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['no_transaksi']); ?></td>
                            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal']))); ?></td>
                            <td><?= htmlspecialchars($row['customer']); ?></td>
                            <td><?= htmlspecialchars($row['barang']); ?> (<?= htmlspecialchars($row['jumlah_barang']); ?>x)</td>
                            <td>Rp <?= htmlspecialchars(number_format($row['total'], 0, ',', '.')); ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm edit-penjualan-button" data-toggle="modal" data-target="#editPenjualanModal"
                                    data-id="<?= $row['id']; ?>"
                                    data-no_transaksi="<?= htmlspecialchars($row['no_transaksi'], ENT_QUOTES); ?>"
                                    data-tanggal="<?= $row['tanggal']; ?>"
                                    data-customer="<?= htmlspecialchars($row['customer'], ENT_QUOTES); ?>"
                                    data-barang="<?= htmlspecialchars($row['barang'], ENT_QUOTES); ?>"
                                    data-jumlah_barang="<?= htmlspecialchars($row['jumlah_barang'], ENT_QUOTES); ?>"
                                    data-total="<?= htmlspecialchars($row['total'], ENT_QUOTES); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-penjualan-button" data-toggle="modal" data-target="#deletePenjualanModal" data-id="<?= $row['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
          </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
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

<!-- Modal Tambah Penjualan -->
<div class="modal fade" id="addPenjualanModal" tabindex="-1" role="dialog" aria-labelledby="addPenjualanModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addPenjualanModalLabel">Tambah Penjualan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="add_penjualan" value="1">
          <div class="form-group">
            <label for="addNoTransaksi">No. Transaksi</label>
            <input type="text" name="no_transaksi" id="addNoTransaksi" class="form-control" value="<?= htmlspecialchars($no_transaksi_otomatis); ?>" readonly required>
          </div>
          <div class="form-group">
            <label for="addTanggal">Tanggal</label>
            <input type="date" name="tanggal" id="addTanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label for="addCustomer">Customer</label>
            <input type="text" name="customer" id="addCustomer" class="form-control" placeholder="Nama customer" required>
          </div>
          <div class="form-group">
            <label for="addBarang">Barang</label>
            <input type="text" name="barang" id="addBarang" class="form-control" placeholder="Nama barang" required>
          </div>
          <div class="form-group">
            <label for="addJumlahBarang">Jumlah</label>
            <input type="number" name="jumlah_barang" id="addJumlahBarang" class="form-control" placeholder="Jumlah" value="1" min="1" required>
          </div>
          <div class="form-group">
            <label for="addTotal">Total</label>
            <input type="text" name="total" id="addTotal" class="form-control" placeholder="Total harga" required pattern="[0-9.,]*">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Penjualan -->
<div class="modal fade" id="editPenjualanModal" tabindex="-1" role="dialog" aria-labelledby="editPenjualanModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="editPenjualanModalLabel">Edit Penjualan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_penjualan" value="1">
          <input type="hidden" name="id" id="editId">
          <div class="form-group">
            <label for="editNoTransaksi">No. Transaksi</label>
            <input type="text" name="no_transaksi" id="editNoTransaksi" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editTanggal">Tanggal</label>
            <input type="date" name="tanggal" id="editTanggal" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editCustomer">Customer</label>
            <input type="text" name="customer" id="editCustomer" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editBarang">Barang</label>
            <input type="text" name="barang" id="editBarang" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editJumlahBarang">Jumlah</label>
            <input type="number" name="jumlah_barang" id="editJumlahBarang" class="form-control" required min="1">
          </div>
          <div class="form-group">
            <label for="editTotal">Total</label>
            <input type="text" name="total" id="editTotal" class="form-control" required pattern="[0-9.,]*">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus Penjualan -->
<div class="modal fade" id="deletePenjualanModal" tabindex="-1" role="dialog" aria-labelledby="deletePenjualanModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deletePenjualanModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus transaksi ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <a href="#" id="confirmDeleteButton" class="btn btn-danger">Hapus</a>
      </div>
    </div>
  </div>
</div>

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

<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();

    // Script untuk mengisi modal edit
    $('.edit-penjualan-button').on('click', function () {
      var button = $(this);
      $('#editId').val(button.data('id'));
      $('#editNoTransaksi').val(button.data('no_transaksi'));
      // Ambil tanggal dari data attribute dan pastikan formatnya YYYY-MM-DD
      var tanggal = button.data('tanggal');
      $('#editTanggal').val(tanggal);
      $('#editCustomer').val(button.data('customer'));
      $('#editBarang').val(button.data('barang'));
      $('#editJumlahBarang').val(button.data('jumlah_barang'));
      var totalValue = button.data('total').toString();
      $('#editTotal').val(parseInt(totalValue));
    });

    // Script untuk mengatur link hapus
    $('#deletePenjualanModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      $('#confirmDeleteButton').attr('href', 'hapus_penjualan.php?id=' + button.data('id'));
    });

    // Fungsi untuk format Rupiah
    function formatRupiah(angka) {
        var number_string = angka.replace(/[^0-9]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return rupiah;
    }

    // Terapkan format saat mengetik di modal tambah dan edit
    $('#addTotal, #editTotal').on('keyup', function() {
        $(this).val(formatRupiah($(this).val()));
    });
  });
</script>
</body>
</html>