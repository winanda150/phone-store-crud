<?php
session_start();
include 'koneksi.php';

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';

// Hapus session notifikasi setelah diambil
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0; // Mengubah status menjadi integer (1 atau 0)
    
    if ($nama_lengkap === '' || $username === '' || $password === '') {
        $error = 'Semua field harus diisi.';
    } else {
        if (isset($_POST['edit_user'])) {
            $id = intval($_POST['id']);
            $update = mysqli_query(
                $conn,
                "UPDATE user SET nama_lengkap='$nama_lengkap', username='$username', password='$password', status=$status WHERE id=$id"
            );

            if ($update) {
                $success = 'Data user berhasil diperbarui.';
            } else {
                $error = 'Gagal mengubah data: ' . mysqli_error($conn);
            }
        } elseif (isset($_POST['add_user'])) {
            $insert = mysqli_query(
                $conn,
                "INSERT INTO user (nama_lengkap, username, password, status) VALUES ('$nama_lengkap', '$username', '$password', $status)"
            );

            if ($insert) {
                $success = 'User baru berhasil ditambahkan.';
            } else {
                $error = 'Gagal menambahkan user: ' . mysqli_error($conn);
            }
        }
    }
}

$data = mysqli_query($conn, "SELECT * FROM user");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Project UAS | User</title>
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
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Data Master
                <i class="fas fa-angle-left right"></i>
              </p>
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
            <h1>Data User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="user.php">Data Master</a></li>
              <li class="breadcrumb-item active">Data User</li>
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
              <h3 class="card-title">Daftar User</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                  <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">
                      <i class="fas fa-plus"></i> Tambah User
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
                              <th>Nama Lengkap</th>
                              <th>Username</th>
                              <th>Password</th>
                              <th>Status</th>
                              <th>Aksi</th>
                          </tr>
                      </thead>

                      <tbody>

                          <?php $no = 1; ?>
                          <?php while($row = mysqli_fetch_assoc($data)): ?>

                          <tr>
                              <td><?= $no++; ?></td>
                              <td><?= $row['nama_lengkap']; ?></td>
                              <td><?= $row['username']; ?></td>
                              <td><?= $row['password']; ?></td>
                              <td><span class="badge badge-<?= $row['status'] == 1 ? 'success' : 'danger'; ?>"><?= $row['status'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?></span></td>

                              <td>
                                  <button type="button"
                                    class="btn btn-warning btn-sm edit-user-button"
                                    data-toggle="modal"
                                    data-target="#editUserModal"
                                    data-id="<?= $row['id']; ?>"
                                    data-nama="<?= htmlspecialchars($row['nama_lengkap'], ENT_QUOTES); ?>"
                                    data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES); ?>"
                                    data-password="<?= htmlspecialchars($row['password'], ENT_QUOTES); ?>"
                                    data-status="<?= htmlspecialchars($row['status'], ENT_QUOTES); ?>">
                                      <i class="fas fa-edit"></i>
                                  </button>

                                  <button type="button" class="btn btn-danger btn-sm delete-user-button" data-toggle="modal" data-target="#deleteUserModal" data-id="<?= $row['id']; ?>">
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
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
    });

    $('.edit-user-button').on('click', function () {
      var button = $(this);
      $('#editUserId').val(button.data('id'));
      $('#editUserNama').val(button.data('nama'));
      $('#editUserUsername').val(button.data('username'));
      $('#editUserPassword').val(button.data('password'));
      $('#editUserStatus').val(button.data('status')); // Set value 1 atau 0
    });

    $('#deleteUserModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      $('#confirmDeleteButton').attr('href', 'hapus_user.php?id=' + button.data('id'));
    });
  });
</script>

<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Tambah User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="add_user" value="1">
          <div class="form-group">
            <label for="addUserNama">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="addUserNama" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="addUserUsername">Username</label>
            <input type="text" name="username" id="addUserUsername" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="addUserPassword">Password</label>
            <input type="text" name="password" id="addUserPassword" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="addUserStatus">Status</label>
            <select name="status" id="addUserStatus" class="form-control" required>
              <option value="1">Aktif</option>
              <option value="0">Tidak Aktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_user" value="1">
          <input type="hidden" name="id" id="editUserId">
          <div class="form-group">
            <label for="editUserNama">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="editUserNama" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editUserUsername">Username</label>
            <input type="text" name="username" id="editUserUsername" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editUserPassword">Password</label>
            <input type="text" name="password" id="editUserPassword" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editUserStatus">Status</label>
            <select name="status" id="editUserStatus" class="form-control" required>
              <option value="1">Aktif</option>
              <option value="0">Tidak Aktif</option>
            </select>
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

<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus user ini?
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
</body>
</html>