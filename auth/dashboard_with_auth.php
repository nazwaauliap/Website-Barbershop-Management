<?php
session_start();
require_once 'auth_check.php'; // Proteksi halaman
require_once 'config/config.php'; // koneksi database

// Set current page untuk active menu
$current_page = 'dashboard';
$page_title = 'Dashboard';

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka,0,",",".");
    }
}

// =================== QUERY DATA ===================

// Total reservasi aktif
$query_reservasi = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE Status = 'Dikonfirmasi'");
$total_reservasi = $query_reservasi ? $query_reservasi->fetch_assoc()['total'] : 0;

// Jumlah pelanggan hari ini
$query_pelanggan = $conn->query("SELECT COUNT(DISTINCT ID_Pelanggan) as total FROM transaksi WHERE DATE(Tanggal_Transaksi) = CURDATE()");
$total_pelanggan_hari_ini = $query_pelanggan ? $query_pelanggan->fetch_assoc()['total'] : 0;

// Daftar tunggu
$query_tunggu = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE Status = 'Pending'");
$total_tunggu = $query_tunggu ? $query_tunggu->fetch_assoc()['total'] : 0;

// Pembatalan hari ini
$query_batal = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE Status = 'Batal' AND DATE(Tanggal_Reservasi) = CURDATE()");
$total_batal = $query_batal ? $query_batal->fetch_assoc()['total'] : 0;

// Chart penjualan 7 bulan terakhir
$query_chart = $conn->query("SELECT DATE_FORMAT(Tanggal_Transaksi,'%Y-%m-01') as bulan, SUM(Total_Harga) as pendapatan 
    FROM transaksi WHERE Tanggal_Transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 MONTH) AND Status = 'Selesai' 
    GROUP BY DATE_FORMAT(Tanggal_Transaksi,'%Y-%m') ORDER BY bulan ASC");

$chart_labels = [];
$chart_data = [];
if($query_chart){
    while($row = $query_chart->fetch_assoc()){
        $chart_labels[] = $row['bulan'];
        $chart_data[] = (int)$row['pendapatan'];
    }
}

// Total pendapatan hari ini
$query_pendapatan = $conn->query("SELECT COALESCE(SUM(Total_Harga),0) as total FROM transaksi WHERE DATE(Tanggal_Transaksi)=CURDATE() AND Status='Selesai'");
$pendapatan_hari_ini = $query_pendapatan ? $query_pendapatan->fetch_assoc()['total'] : 0;

// Barberman aktif
$query_barberman = $conn->query("SELECT COUNT(*) as total FROM barberman WHERE Status='Aktif'");
$barberman_aktif = $query_barberman ? $query_barberman->fetch_assoc()['total'] : 0;

?>
<!doctype html>
<html lang="id">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>NZ Barbershop | Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light dark" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="./css/adminlte.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

<div class="app-wrapper">
  <!-- Header -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-bell-fill"></i>
            <?php 
            $query_notif = "SELECT COUNT(*) as total FROM reservasi WHERE DATE(Tanggal_Reservasi) = CURDATE() AND Status = 'Pending'";
            $result_notif = $conn->query($query_notif);
            $notif_count = $result_notif ? $result_notif->fetch_assoc()['total'] : 0;
            if($notif_count > 0): ?>
            <span class="navbar-badge badge text-bg-warning"><?php echo $notif_count; ?></span>
            <?php endif; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <span class="dropdown-item dropdown-header"><?php echo $notif_count; ?> Notifikasi</span>
            <div class="dropdown-divider"></div>
            <a href="reservasi.php" class="dropdown-item">
              <i class="bi bi-calendar-check me-2"></i> <?php echo $notif_count; ?> reservasi pending
              <span class="float-end text-secondary fs-7">hari ini</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="reservasi.php" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-lte-toggle="fullscreen">
            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
          </a>
        </li>
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <img src="./assets/img/user.jpg" class="user-image rounded-circle shadow" alt="User Image" />
            <span class="d-none d-md-inline"><?php echo isset($_SESSION['nama_pegawai']) ? $_SESSION['nama_pegawai'] : 'Guest'; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <li class="user-header text-bg-primary">
              <img src="./assets/img/user.jpg" class="rounded-circle shadow" alt="User Image" />
              <p>
                <?php echo isset($_SESSION['nama_pegawai']) ? $_SESSION['nama_pegawai'] : 'Guest'; ?> - Web Developer
                <small>Member since Nov. 2023</small>
              </p>
            </li>
            <li class="user-footer">
              <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
              <a href="logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="./dashboard.php" class="brand-link">
        <span class="brand-text fw-bold">NZ Barbershop</span>
      </a>
    </div>
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column" role="menu">
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="./dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-speedometer"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <!-- Master Data -->
          <li class="nav-header">MASTER DATA</li>
          <li class="nav-item">
            <a href="./master_pelanggan.php" class="nav-link <?php echo ($current_page == 'master_pelanggan') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-person"></i>
              <p>Pelanggan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_barberman.php" class="nav-link <?php echo ($current_page == 'master_barberman') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-scissors"></i>
              <p>Barberman</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_layanan.php" class="nav-link <?php echo ($current_page == 'master_layanan') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-list-check"></i>
              <p>Layanan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_produk.php" class="nav-link <?php echo ($current_page == 'master_produk') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-bag"></i>
              <p>Produk</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_pemasok.php" class="nav-link <?php echo ($current_page == 'master_pemasok') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-truck"></i>
              <p>Pemasok</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_jabatan.php" class="nav-link <?php echo ($current_page == 'master_jabatan') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-diagram-3"></i>
              <p>Jabatan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_pegawai.php" class="nav-link <?php echo ($current_page == 'master_pegawai') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-people"></i>
              <p>Pegawai</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./master_diskon.php" class="nav-link <?php echo ($current_page == 'master_diskon') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-percent"></i>
              <p>Diskon</p>
            </a>
          </li>

          <!-- Transaksi -->
          <li class="nav-header">TRANSAKSI</li>
          <li class="nav-item">
            <a href="./transaksi.php" class="nav-link <?php echo ($current_page == 'transaksi') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-receipt-cutoff"></i>
              <p>Transaksi</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./detail_layanan.php" class="nav-link <?php echo ($current_page == 'detail_layanan') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-scissors"></i>
              <p>Detail Layanan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./detail_produk.php" class="nav-link <?php echo ($current_page == 'detail_produk') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-bag-check"></i>
              <p>Detail Produk</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./reservasi.php" class="nav-link <?php echo ($current_page == 'reservasi') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-calendar-check"></i>
              <p>Reservasi</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./komisi_barberman.php" class="nav-link <?php echo ($current_page == 'komisi_barberman') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-cash-coin"></i>
              <p>Komisi Barberman</p>
            </a>
          </li>

          <!-- Laporan -->
          <li class="nav-header">LAPORAN</li>
          <li class="nav-item">
            <a href="./laporan_layanan.php" class="nav-link <?php echo ($current_page == 'laporan_layanan') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-list-task"></i>
              <p>Rekap Layanan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./laporan_produk.php" class="nav-link <?php echo ($current_page == 'laporan_produk') ? 'active' : ''; ?>">
              <i class="nav-icon bi bi-basket3"></i>
              <p>Rekap Produk</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Main content -->
  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h3 class="mb-0">Dashboard</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    
    <div class="app-content">
      <div class="container-fluid">
        <!-- Info Boxes -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
              <div class="inner">
                <h3><?= $total_reservasi ?></h3>
                <p>Daftar Reservasi</p>
              </div>
              <i class="bi bi-calendar-check small-box-icon"></i>
              <a href="reservasi.php" class="small-box-footer link-light">
                More info <i class="bi bi-link-45deg"></i>
              </a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
              <div class="inner">
                <h3><?= $total_pelanggan_hari_ini ?></h3>
                <p>Jumlah Pelanggan Hari Ini</p>
              </div>
              <i class="bi bi-people small-box-icon"></i>
              <a href="transaksi.php" class="small-box-footer link-light">
                More info <i class="bi bi-link-45deg"></i>
              </a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
              <div class="inner">
                <h3><?= $total_tunggu ?></h3>
                <p>Daftar Tunggu</p>
              </div>
              <i class="bi bi-hourglass-split small-box-icon"></i>
              <a href="reservasi.php" class="small-box-footer link-dark">
                More info <i class="bi bi-link-45deg"></i>
              </a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger">
              <div class="inner">
                <h3><?= $total_batal ?></h3>
                <p>Pembatalan Reservasi Hari Ini</p>
              </div>
              <i class="bi bi-x-circle small-box-icon"></i>
              <a href="reservasi.php" class="small-box-footer link-light">
                More info <i class="bi bi-link-45deg"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Chart -->
        <div class="row mt-3">
          <div class="col-lg-7">
            <div class="card mb-4">
              <div class="card-header">
                <h3 class="card-title">Nilai Penjualan 7 Bulan Terakhir</h3>
              </div>
              <div class="card-body">
                <div id="revenue-chart"></div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-5">
            <div class="card mb-4">
              <div class="card-header">
                <h3 class="card-title">Statistik Hari Ini</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="info-box mb-3 text-bg-info">
                      <span class="info-box-icon"><i class="bi bi-cash-stack"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Pendapatan Hari Ini</span>
                        <span class="info-box-number"><?= rupiah($pendapatan_hari_ini) ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="info-box mb-3 text-bg-success">
                      <span class="info-box-icon"><i class="bi bi-scissors"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Barberman Aktif</span>
                        <span class="info-box-number"><?= $barberman_aktif ?> Orang</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="./js/adminlte.js"></script>
<script>
  const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
  const Default = {scrollbarTheme: 'os-theme-light', scrollbarAutoHide: 'leave', scrollbarClickScroll: true};
  document.addEventListener('DOMContentLoaded', function () {
    const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
    if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
      OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
        scrollbars: {
          theme: Default.scrollbarTheme,
          autoHide: Default.scrollbarAutoHide,
          clickScroll: Default.scrollbarClickScroll
        }
      });
    }
  });
</script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    $('.table-datatable').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
      }
    });
  });
  
  function confirmDelete(id, name, url) {
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: "Data " + name + " akan dihapus!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url + '?id=' + id + '&action=delete';
      }
    });
  }
</script>

<!-- Chart Script -->
<script>
// ApexCharts dynamic
const chartLabels = <?= json_encode($chart_labels) ?>;
const chartData = <?= json_encode($chart_data) ?>;

const sales_chart_options = {
  series: [{
    name: 'Pendapatan',
    data: chartData
  }],
  chart: {
    height: 300,
    type: 'area',
    toolbar: {
      show: false
    }
  },
  colors: ['#20c997'],
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth'
  },
  xaxis: {
    type: 'datetime',
    categories: chartLabels
  },
  yaxis: {
    labels: {
      formatter: function(val) {
        return 'Rp ' + val.toLocaleString('id-ID');
      }
    }
  },
  tooltip: {
    y: {
      formatter: function(val) {
        return 'Rp ' + val.toLocaleString('id-ID');
      }
    }
  }
};

const sales_chart = new ApexCharts(document.querySelector('#revenue-chart'), sales_chart_options);
sales_chart.render();
</script>

</body>
</html>