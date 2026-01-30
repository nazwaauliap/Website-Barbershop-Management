<?php
$current_page = 'master_reservasi';
$page_title = "Reservasi";
include 'config/config.php';

// ================= AUTO ID =================
function getNewID($prefix, $table, $field, $conn){
    $q = mysqli_query($conn,"SELECT MAX($field) as maxID FROM $table");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];
    if(!$idMax){
        return $prefix."001";
    } else {
        $num = (int) substr($idMax, strlen($prefix));
        $num++;
        return $prefix.sprintf("%03d", $num);
    }
}

// ================= TAMBAH =================
if(isset($_POST['tambah'])){
    $idBaru = getNewID("RES","reservasi","ID_Reservasi",$conn);

    mysqli_query($conn,"INSERT INTO reservasi (
        ID_Reservasi, ID_Pelanggan, ID_Barberman, ID_Layanan, Tanggal_Reservasi, Jam_Reservasi, Status
    ) VALUES (
        '$idBaru',
        '$_POST[pelanggan]',
        '$_POST[barberman]',
        '$_POST[layanan]',
        '$_POST[tanggal]',
        '$_POST[jam]',
        '$_POST[status]'
    )");
    echo "<script>window.location='master_reservasi.php';</script>";
}

// ================= HAPUS =================
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM reservasi WHERE ID_Reservasi='$id'");
    echo "<script>window.location='master_reservasi.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM reservasi WHERE ID_Reservasi='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE reservasi SET
        ID_Pelanggan='$_POST[pelanggan]',
        ID_Barberman='$_POST[barberman]',
        ID_Layanan='$_POST[layanan]',
        Tanggal_Reservasi='$_POST[tanggal]',
        Jam_Reservasi='$_POST[jam]',
        Status='$_POST[status]'
        WHERE ID_Reservasi='$_POST[id]'"
    );
    echo "<script>window.location='master_reservasi.php';</script>";
}

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Reservasi</h3>

<!-- FORM TAMBAH / EDIT RESERVASI -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<input type="hidden" name="id" value="<?= $editData['ID_Reservasi'] ?? '' ?>">

<!-- Pelanggan -->
<div class="col-md-3">
<select name="pelanggan" class="form-control" required>
<option value="">Pilih Pelanggan</option>
<?php
$q = mysqli_query($conn,"SELECT * FROM pelanggan");
while($p = mysqli_fetch_array($q)):
    $selected = ($editData['ID_Pelanggan'] ?? '')==$p['ID_Pelanggan']?'selected':'';
?>
<option value="<?= $p['ID_Pelanggan'] ?>" <?= $selected ?>><?= $p['Nama'] ?></option>
<?php endwhile; ?>
</select>
</div>

<!-- Barberman -->
<div class="col-md-3">
<select name="barberman" class="form-control" required>
<option value="">Pilih Barberman</option>
<?php
$q = mysqli_query($conn,"SELECT * FROM barberman WHERE Status='Aktif'");
while($b = mysqli_fetch_array($q)):
    $selected = ($editData['ID_Barberman'] ?? '')==$b['ID_Barberman']?'selected':'';
?>
<option value="<?= $b['ID_Barberman'] ?>" <?= $selected ?>><?= $b['Nama_Barberman'] ?></option>
<?php endwhile; ?>
</select>
</div>

<!-- Layanan -->
<div class="col-md-3">
<select name="layanan" class="form-control" required>
<option value="">Pilih Layanan</option>
<?php
$q = mysqli_query($conn,"SELECT * FROM layanan");
while($l = mysqli_fetch_array($q)):
    $selected = ($editData['ID_Layanan'] ?? '')==$l['ID_Layanan']?'selected':'';
?>
<option value="<?= $l['ID_Layanan'] ?>" <?= $selected ?>><?= $l['Nama_Layanan'] ?></option>
<?php endwhile; ?>
</select>
</div>

<!-- Tanggal -->
<div class="col-md-2">
<input type="text" name="tanggal" id="tanggal_reservasi" class="form-control" placeholder="Tanggal" value="<?= $editData['Tanggal_Reservasi'] ?? '' ?>" required>
</div>

<!-- Jam -->
<div class="col-md-2">
<input type="text" name="jam" id="jam_reservasi" class="form-control" placeholder="Jam" value="<?= $editData['Jam_Reservasi'] ?? '' ?>" required>
</div>

<!-- Status -->
<div class="col-md-2">
<select name="status" class="form-control">
<option value="Pending" <?= ($editData['Status'] ?? '')=='Pending'?'selected':'' ?>>Pending</option>
<option value="Selesai" <?= ($editData['Status'] ?? '')=='Selesai'?'selected':'' ?>>Selesai</option>
<option value="Batal" <?= ($editData['Status'] ?? '')=='Batal'?'selected':'' ?>>Batal</option>
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

<!-- TABEL RESERVASI -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID Reservasi</th>
<th>Pelanggan</th>
<th>Barberman</th>
<th>Layanan</th>
<th>Tanggal</th>
<th>Jam</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data = mysqli_query($conn,"SELECT r.*, p.Nama AS pelanggan, b.Nama_Barberman, l.Nama_Layanan
                            FROM reservasi r
                            LEFT JOIN pelanggan p ON r.ID_Pelanggan=p.ID_Pelanggan
                            LEFT JOIN barberman b ON r.ID_Barberman=b.ID_Barberman
                            LEFT JOIN layanan l ON r.ID_Layanan=l.ID_Layanan
                            ORDER BY r.Tanggal_Reservasi DESC, r.Jam_Reservasi ASC");
while($d = mysqli_fetch_array($data)):
?>
<tr>
<td><?= $d['ID_Reservasi'] ?></td>
<td><?= $d['pelanggan'] ?></td>
<td><?= $d['Nama_Barberman'] ?></td>
<td><?= $d['Nama_Layanan'] ?></td>
<td><?= $d['Tanggal_Reservasi'] ?></td>
<td><?= $d['Jam_Reservasi'] ?></td>
<td><?= $d['Status'] ?></td>
<td>
<a href="?edit=<?= $d['ID_Reservasi'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
<a href="?hapus=<?= $d['ID_Reservasi'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus reservasi?')"><i class="bi bi-trash"></i></a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</div>
</main>

<!-- FLATPICKR DATE & TIME PICKER -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#tanggal_reservasi", {
    dateFormat: "Y-m-d",
    allowInput: true
});
flatpickr("#jam_reservasi", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    minuteIncrement: 30  // Pilihan jam bulat tiap 30 menit
});
</script>

<?php include 'includes/footer.php'; ?>
