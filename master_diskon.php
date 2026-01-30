<?php
$current_page = 'master_diskon';
$page_title = "Master Diskon";
include 'config/config.php';

// ================= TAMBAH =================
if(isset($_POST['tambah'])){
    $q = mysqli_query($conn,"SELECT MAX(ID_Diskon) as maxID FROM diskon");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    $no = $idMax ? (int) substr($idMax,3) : 0;
    $no++;
    $idBaru = "DSK".sprintf("%03d",$no);

    mysqli_query($conn,"INSERT INTO diskon (
        ID_Diskon, Nama_Diskon, Persentase, Keterangan, Status
    ) VALUES (
        '$idBaru',
        '$_POST[nama]',
        '$_POST[persentase]',
        '$_POST[keterangan]',
        '$_POST[status]'
    )");
}

// ================= HAPUS =================
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM diskon WHERE ID_Diskon='$_GET[hapus]'");
    echo "<script>window.location='master_diskon.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM diskon WHERE ID_Diskon='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE diskon SET
        Nama_Diskon='$_POST[nama]',
        Persentase='$_POST[persentase]',
        Keterangan='$_POST[keterangan]',
        Status='$_POST[status]'
        WHERE ID_Diskon='$_POST[id]'
    ");
    echo "<script>window.location='master_diskon.php';</script>";
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Diskon</h3>

<!-- FORM TAMBAH / EDIT -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<?php if($editData): ?>
<input type="hidden" name="id" value="<?= $editData['ID_Diskon'] ?>">
<?php endif; ?>

<div class="col-md-3">
<input type="text" name="nama" class="form-control" placeholder="Nama Diskon"
value="<?= $editData['Nama_Diskon'] ?? '' ?>" required>
</div>

<div class="col-md-2">
<input type="number" name="persentase" class="form-control" placeholder="Persentase (%)"
value="<?= $editData['Persentase'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<input type="text" name="keterangan" class="form-control" placeholder="Keterangan"
value="<?= $editData['Keterangan'] ?? '' ?>">
</div>

<div class="col-md-2">
<select name="status" class="form-control">
<option value="Aktif" <?= ($editData['Status'] ?? '')=='Aktif'?'selected':'' ?>>Aktif</option>
<option value="Nonaktif" <?= ($editData['Status'] ?? '')=='Nonaktif'?'selected':'' ?>>Nonaktif</option>
</select>
</div>

<div class="col-md-1">
<?php if($editData): ?>
<button class="btn btn-success w-100" name="update">Update</button>
<?php else: ?>
<button class="btn btn-primary w-100" name="tambah">Add</button>
<?php endif; ?>
</div>

</div>
</div>
</form>

<!-- TABEL DISKON -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID</th>
<th>Nama Diskon</th>
<th>Persentase</th>
<th>Keterangan</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data = mysqli_query($conn,"SELECT * FROM diskon");
while($d=mysqli_fetch_array($data)):
?>
<tr>
<td><?= $d['ID_Diskon'] ?></td>
<td><?= $d['Nama_Diskon'] ?></td>
<td><?= $d['Persentase'] ?>%</td>
<td><?= $d['Keterangan'] ?></td>
<td><?= $d['Status'] ?></td>
<td>
<a href="?edit=<?= $d['ID_Diskon'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
<a href="?hapus=<?= $d['ID_Diskon'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</div>
</main>

<?php include 'includes/footer.php'; ?>
