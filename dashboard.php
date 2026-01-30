<?php
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

// Include header
include 'includes/header.php';
?>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Sidebar -->
<?php include 'includes/sidebar.php'; ?>

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

<?php include 'includes/footer.php'; ?>