<?php
$current_page = 'master_pemasok';
$page_title = "Master Pemasok";
include 'config/config.php';

# ================= TAMBAH =================
if(isset($_POST['tambah'])){

    $q = mysqli_query($conn,"SELECT MAX(ID_Pemasok) as maxID FROM pemasok");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    $no = (int) substr($idMax,3,3);
    $no++;
    $idBaru = "PMS".sprintf("%03s",$no);

    mysqli_query($conn,"INSERT INTO pemasok 
    VALUES ('$idBaru',
            '$_POST[nama]',
            '$_POST[kontak]',
            '$_POST[alamat]',
            '$_POST[email]')");
}

# ================= HAPUS =================
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM pemasok 
    WHERE ID_Pemasok='$_GET[hapus]'");
    echo "<script>window.location='master_pemasok.php';</script>";
}

# ================= EDIT =================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE pemasok SET
        Nama_Pemasok='$_POST[nama]',
        Kontak='$_POST[kontak]',
        Alamat='$_POST[alamat]',
        Email='$_POST[email]'
        WHERE ID_Pemasok='$_POST[id]'
    ");

    echo "<script>window.location='master_pemasok.php';</script>";
}

# MODE EDIT
$editData = null;
if(isset($_GET['edit'])){
    $q = mysqli_query($conn,"SELECT * FROM pemasok 
                            WHERE ID_Pemasok='$_GET[edit]'");
    $editData = mysqli_fetch_array($q);
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="app-main">
<div class="app-content container-fluid">

<h3 class="mb-3">Master Pemasok</h3>

<!-- FORM -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<input type="hidden" name="id" 
value="<?= $editData['ID_Pemasok'] ?? '' ?>">

<div class="col-md-3">
<input type="text" name="nama" class="form-control"
placeholder="Nama Pemasok"
value="<?= $editData['Nama_Pemasok'] ?? '' ?>" required>
</div>

<div class="col-md-2">
<input type="text" name="kontak" class="form-control"
placeholder="Kontak"
value="<?= $editData['Kontak'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<input type="text" name="alamat" class="form-control"
placeholder="Alamat"
value="<?= $editData['Alamat'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<input type="email" name="email" class="form-control"
placeholder="Email"
value="<?= $editData['Email'] ?? '' ?>">
</div>

<div class="col-md-1">
<?php if($editData){ ?>
<button class="btn btn-success w-100" name="update">Edit</button>
<?php } else { ?>
<button class="btn btn-primary w-100" name="tambah">Add</button>
<?php } ?>
</div>

</div>
</div>
</form>

<!-- TABEL -->
<div class="card">
<div class="card-body">

<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID</th>
<th>Nama</th>
<th>Kontak</th>
<th>Alamat</th>
<th>Email</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php
$data = mysqli_query($conn,"SELECT * FROM pemasok");
while($d=mysqli_fetch_array($data)){
?>
<tr>
<td><?= $d['ID_Pemasok'] ?></td>
<td><?= $d['Nama_Pemasok'] ?></td>
<td><?= $d['Kontak'] ?></td>
<td><?= $d['Alamat'] ?></td>
<td><?= $d['Email'] ?></td>
<td>
<a href="?edit=<?= $d['ID_Pemasok'] ?>" 
class="btn btn-warning btn-sm">
<i class="bi bi-pencil"></i>
</a>

<a href="?hapus=<?= $d['ID_Pemasok'] ?>" 
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
