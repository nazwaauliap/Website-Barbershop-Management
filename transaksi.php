<?php
$current_page = 'transaksi';
$page_title = "Transaksi";
include 'config/config.php';

// ================== AUTO ID ==================
function getNewID($prefix, $table, $field, $conn){
    $q = mysqli_query($conn,"SELECT MAX($field) as maxID FROM $table");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];
    if(!$idMax){
        return $prefix."001";
    } else {
        $num = (int) substr($idMax, strlen($prefix));
        $num++;
        return $prefix.sprintf("%03d",$num);
    }
}

// ================== TAMBAH ==================
if(isset($_POST['tambah'])){
    $idBaru = getNewID("TRX","transaksi","ID_Transaksi",$conn);

    mysqli_query($conn,"INSERT INTO transaksi (
        ID_Transaksi, ID_Pelanggan, ID_Kasir, Total_Harga, Tanggal_Transaksi, Status
    ) VALUES (
        '$idBaru',
        '$_POST[pelanggan]',
        '$_POST[kasir]',
        '$_POST[total]',
        '$_POST[tgl]',
        '$_POST[status]'
    )");

    echo "<script>window.location='transaksi.php';</script>";
}

// ================== HAPUS ==================
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM transaksi WHERE ID_Transaksi='$id'");
    echo "<script>window.location='transaksi.php';</script>";
}

// ================== MODE EDIT ==================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM transaksi WHERE ID_Transaksi='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// ================== UPDATE ==================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE transaksi SET
        ID_Pelanggan='$_POST[pelanggan]',
        ID_Kasir='$_POST[kasir]',
        Total_Harga='$_POST[total]',
        Tanggal_Transaksi='$_POST[tgl]',
        Status='$_POST[status]'
        WHERE ID_Transaksi='$_POST[id]'
    ");
    echo "<script>window.location='transaksi.php';</script>";
}

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Transaksi</h3>

<!-- FORM TAMBAH / EDIT -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<input type="hidden" name="id" value="<?= $editData['ID_Transaksi'] ?? '' ?>">

<!-- Pilih Pelanggan -->
<div class="col-md-3">
    <select name="pelanggan" class="form-control" required>
        <option value="">Pilih Pelanggan</option>
        <?php
        $pelanggan = mysqli_query($conn,"SELECT * FROM pelanggan");
        while($p = mysqli_fetch_array($pelanggan)):
            $selected = ($editData && $editData['ID_Pelanggan']==$p['ID_Pelanggan']) ? 'selected' : '';
        ?>
        <option value="<?= $p['ID_Pelanggan'] ?>" <?= $selected ?>><?= $p['Nama'] ?></option>
        <?php endwhile; ?>
    </select>
</div>

<!-- Pilih Kasir -->
<div class="col-md-3">
    <select name="kasir" class="form-control" required>
        <option value="">Pilih Kasir</option>
        <?php
        $kasir = mysqli_query($conn,"SELECT * FROM pegawai WHERE ID_Jabatan='KSR'");
        while($k = mysqli_fetch_array($kasir)):
            $selected = ($editData && $editData['ID_Kasir']==$k['ID_Pegawai']) ? 'selected' : '';
        ?>
        <option value="<?= $k['ID_Pegawai'] ?>" <?= $selected ?>><?= $k['Nama_Pegawai'] ?></option>
        <?php endwhile; ?>
    </select>
</div>

<!-- Total -->
<div class="col-md-2">
    <input type="number" name="total" class="form-control" placeholder="Total Harga" 
    value="<?= $editData['Total_Harga'] ?? '' ?>" required>
</div>

<!-- Tanggal -->
<div class="col-md-2">
    <input type="text" id="tgl_transaksi" name="tgl" class="form-control" 
    value="<?= $editData['Tanggal_Transaksi'] ?? date('Y-m-d') ?>" required>
</div>

<!-- Status -->
<div class="col-md-2">
    <select name="status" class="form-control">
        <option value="Lunas" <?= ($editData['Status'] ?? '')=='Lunas' ? 'selected':'' ?>>Lunas</option>
        <option value="Pending" <?= ($editData['Status'] ?? '')=='Pending' ? 'selected':'' ?>>Pending</option>
        <option value="Batal" <?= ($editData['Status'] ?? '')=='Batal' ? 'selected':'' ?>>Batal</option>
    </select>
</div>

<!-- Tombol -->
<div class="col-md-2">
<?php if($editData): ?>
    <button class="btn btn-success w-100" name="update">Update</button>
<?php else: ?>
    <button class="btn btn-primary w-100" name="tambah">Add</button>
<?php endif; ?>
</div>

</div>
</div>
</form>

<!-- TABEL TRANSAKSI + DETAIL LAYANAN + BARBERMAN -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID Transaksi</th>
<th>Pelanggan</th>
<th>Kasir</th>
<th>Barberman</th>
<th>Layanan</th>
<th>Total</th>
<th>Tanggal</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data = mysqli_query($conn,"
SELECT t.ID_Transaksi, p.Nama AS NamaP, k.Nama_Pegawai AS NamaK,
GROUP_CONCAT(DISTINCT b.Nama_Barberman SEPARATOR ', ') AS Barberman,
GROUP_CONCAT(DISTINCT l.Nama_Layanan SEPARATOR ', ') AS Layanan,
t.Total_Harga, t.Tanggal_Transaksi, t.Status
FROM transaksi t
LEFT JOIN pelanggan p ON t.ID_Pelanggan = p.ID_Pelanggan
LEFT JOIN pegawai k ON t.ID_Kasir = k.ID_Pegawai
LEFT JOIN detail_layanan dl ON dl.ID_Transaksi = t.ID_Transaksi
LEFT JOIN barberman b ON dl.ID_Barberman = b.ID_Barberman
LEFT JOIN layanan l ON dl.ID_Layanan = l.ID_Layanan
GROUP BY t.ID_Transaksi
ORDER BY t.Tanggal_Transaksi DESC
");
while($d = mysqli_fetch_array($data)):
?>
<tr>
<td><?= $d['ID_Transaksi'] ?></td>
<td><?= $d['NamaP'] ?></td>
<td><?= $d['NamaK'] ?></td>
<td><?= $d['Barberman'] ?></td>
<td><?= $d['Layanan'] ?></td>
<td>Rp <?= number_format($d['Total_Harga']) ?></td>
<td><?= $d['Tanggal_Transaksi'] ?></td>
<td><?= $d['Status'] ?></td>
<td>
<a href="?edit=<?= $d['ID_Transaksi'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
<a href="?hapus=<?= $d['ID_Transaksi'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

<!-- FLATPICKR DATE PICKER -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#tgl_transaksi", {
    dateFormat: "Y-m-d",
    allowInput: true
});
</script>

</div>
</main>

<?php include 'includes/footer.php'; ?>
