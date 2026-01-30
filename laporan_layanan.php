<?php
$current_page = 'laporan_layanan';
$page_title = "Rekap Layanan";
include 'config/config.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';

// ================= FILTER TANGGAL =================
$tgl_awal = $_POST['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_POST['tgl_akhir'] ?? date('Y-m-d');

$filter_query = " AND t.Tanggal_Transaksi BETWEEN '$tgl_awal' AND '$tgl_akhir'";
?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Rekap Layanan</h3>
<p>Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d <?= date('d-m-Y', strtotime($tgl_akhir)) ?></p>

<!-- FORM FILTER TANGGAL -->
<form method="POST" class="mb-3">
    <div class="row g-2">
        <div class="col-md-3">
            <input type="text" id="tgl_awal" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>" placeholder="Tanggal Awal" required>
        </div>
        <div class="col-md-3">
            <input type="text" id="tgl_akhir" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>" placeholder="Tanggal Akhir" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
        <div class="col-md-2">
            <a href="print_laporan_layanan.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" 
               target="_blank" class="btn btn-success w-100">
                <i class="bi bi-printer"></i> Print
            </a>
        </div>
    </div>
</form>

<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>No</th>
<th>Layanan</th>
<th>Jumlah Terjual</th>
<th>Total Pendapatan</th>
</tr>
</thead>
<tbody>
<?php
$q = mysqli_query($conn,"
    SELECT l.Nama_Layanan, 
           COUNT(dl.ID_Detail_Layanan) AS Jumlah_Terjual, 
           SUM(dl.Subtotal) AS Total_Pendapatan
    FROM detail_layanan dl
    JOIN layanan l ON dl.ID_Layanan = l.ID_Layanan
    JOIN transaksi t ON dl.ID_Transaksi = t.ID_Transaksi
    WHERE 1=1 $filter_query
    GROUP BY dl.ID_Layanan
    ORDER BY Total_Pendapatan DESC
");

$no = 1;
$total_keseluruhan = 0;
while($row = mysqli_fetch_array($q)):
    $total_keseluruhan += $row['Total_Pendapatan'];
?>
<tr>
<td><?= $no++ ?></td>
<td><?= $row['Nama_Layanan'] ?></td>
<td><?= $row['Jumlah_Terjual'] ?></td>
<td>Rp <?= number_format($row['Total_Pendapatan'], 0, ',', '.') ?></td>
</tr>
<?php endwhile; ?>
<?php if($total_keseluruhan > 0): ?>
<tr style="font-weight: bold; background-color: #E7E6E6;">
<td colspan="3" style="text-align: right;">TOTAL:</td>
<td>Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

</div>
</main>

<!-- FLATPICKR DATE PICKER -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#tgl_awal", {
    dateFormat: "Y-m-d",
    defaultDate: "<?= $tgl_awal ?>",
    allowInput: true
});
flatpickr("#tgl_akhir", {
    dateFormat: "Y-m-d",
    defaultDate: "<?= $tgl_akhir ?>",
    allowInput: true
});
</script>

<?php include 'includes/footer.php'; ?>