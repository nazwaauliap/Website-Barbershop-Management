<?php
$current_page = 'master_layanan';
$page_title = "Master Layanan";
include 'config/config.php';

// ================= TAMBAH =================
if(isset($_POST['tambah'])){
    $q = mysqli_query($conn,"SELECT MAX(ID_Layanan) as maxID FROM layanan");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    $no = (int) substr($idMax,3,3);
    $no++;
    $idBaru = "LYN".sprintf("%03s",$no);

    mysqli_query($conn,"INSERT INTO layanan 
    VALUES ('$idBaru','$_POST[nama]','$_POST[durasi]','$_POST[harga]')");
}

// ================= HAPUS =================
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM layanan 
    WHERE ID_Layanan='$_GET[hapus]'");
    echo "<script>window.location='master_layanan.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $q = mysqli_query($conn,"SELECT * FROM layanan WHERE ID_Layanan='$_GET[edit]'");
    $editData = mysqli_fetch_array($q);
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE layanan SET
        Nama_Layanan='$_POST[nama]',
        Durasi_Menit='$_POST[durasi]',
        Harga='$_POST[harga]'
        WHERE ID_Layanan='$_POST[id]'
    ");

    echo "<script>window.location='master_layanan.php';</script>";
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="app-main">
<div class="app-content container-fluid">

<h3 class="mb-3">Layanan</h3>

<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<?php if($editData): ?>
<input type="hidden" name="id" value="<?= $editData['ID_Layanan'] ?>">
<?php endif; ?>

<div class="col-md-4">
<input type="text" name="nama" class="form-control" placeholder="Nama Layanan"
value="<?= $editData['Nama_Layanan'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<input type="number" name="durasi" class="form-control" placeholder="Durasi (menit)"
value="<?= $editData['Durasi_Menit'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<input type="number" name="harga" class="form-control" placeholder="Harga"
value="<?= $editData['Harga'] ?? '' ?>" required>
</div>

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

<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">

<thead>
<tr>
<th>ID</th>
<th>Nama Layanan</th>
<th>Durasi</th>
<th>Harga</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php
$data = mysqli_query($conn,"SELECT * FROM layanan");
while($d=mysqli_fetch_array($data)){
?>
<tr>
<td><?= $d['ID_Layanan'] ?></td>
<td><?= $d['Nama_Layanan'] ?></td>
<td><?= $d['Durasi_Menit'] ?> menit</td>
<td>Rp <?= number_format($d['Harga']) ?></td>
<td>

<a href="?edit=<?= $d['ID_Layanan'] ?>" 
   class="btn btn-warning btn-sm">
<i class="bi bi-pencil"></i>
</a>

<a href="?hapus=<?= $d['ID_Layanan'] ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Hapus data?')">
<i class="bi bi-trash"></i>
</a>

</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>
</div>

</div>
</main>

<?php include 'includes/footer.php'; ?>
