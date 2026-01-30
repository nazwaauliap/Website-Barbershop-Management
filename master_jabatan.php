<?php
$current_page = 'master_jabatan';
$page_title = "Master Jabatan";
include 'config/config.php';

// ================= TAMBAH =================
if(isset($_POST['tambah'])){
    $q = mysqli_query($conn,"SELECT MAX(ID_Jabatan) as maxID FROM jabatan");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    $no = (int) substr($idMax,3,3);
    $no++;
    $idBaru = "JBT".sprintf("%03s",$no);

    mysqli_query($conn,"INSERT INTO jabatan (ID_Jabatan, Nama_Jabatan, Gaji_Pokok) 
    VALUES('$idBaru','$_POST[nama]','$_POST[gaji]')");
}

// ================= HAPUS =================
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM jabatan WHERE ID_Jabatan='$_GET[hapus]'");
    echo "<script>window.location='master_jabatan.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM jabatan WHERE ID_Jabatan='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE jabatan SET
        Nama_Jabatan='$_POST[nama]',
        Gaji_Pokok='$_POST[gaji]'
        WHERE ID_Jabatan='$_POST[id]'"
    );
    echo "<script>window.location='master_jabatan.php';</script>";
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Jabatan</h3>

<!-- FORM TAMBAH / EDIT JABATAN -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<?php if($editData): ?>
    <input type="hidden" name="id" value="<?= $editData['ID_Jabatan'] ?>">
<?php endif; ?>

<div class="col-md-5">
    <input type="text" name="nama" class="form-control" placeholder="Nama Jabatan"
           value="<?= $editData['Nama_Jabatan'] ?? '' ?>" required>
</div>

<div class="col-md-5">
    <input type="number" name="gaji" class="form-control" placeholder="Gaji Pokok"
           value="<?= $editData['Gaji_Pokok'] ?? '' ?>" required>
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

<!-- TABEL JABATAN -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID</th>
<th>Nama Jabatan</th>
<th>Gaji Pokok</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data=mysqli_query($conn,"SELECT * FROM jabatan");
while($d=mysqli_fetch_array($data)):
?>
<tr>
<td><?= $d['ID_Jabatan'] ?></td>
<td><?= $d['Nama_Jabatan'] ?></td>
<td>Rp <?= number_format($d['Gaji_Pokok']) ?></td>
<td>
    <!-- Tombol Edit -->
    <a href="?edit=<?= $d['ID_Jabatan'] ?>" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i>
    </a>

    <!-- Tombol Hapus -->
    <a href="?hapus=<?= $d['ID_Jabatan'] ?>" class="btn btn-danger btn-sm"
       onclick="return confirm('Hapus data?')">
        <i class="bi bi-trash"></i>
    </a>
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
