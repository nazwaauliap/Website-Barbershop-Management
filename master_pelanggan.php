<?php
$current_page = 'master_pelanggan';
$page_title = "Master Pelanggan";
include 'config/config.php';

// ================== CRUD ==================

// TAMBAH
if(isset($_POST['tambah'])){
    $q = mysqli_query($conn,"SELECT MAX(ID_Pelanggan) as maxID FROM pelanggan");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    $no = (int) substr($idMax,3,3);
    $no++;
    $idBaru = "PLG".sprintf("%03s",$no);

    mysqli_query($conn,"INSERT INTO pelanggan 
    VALUES ('$idBaru','$_POST[nama]','$_POST[tgl]','$_POST[alamat]')");
}

// HAPUS
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM pelanggan 
    WHERE ID_Pelanggan='$_GET[hapus]'");
    echo "<script>window.location='master_pelanggan.php';</script>";
}

// MODE EDIT
$editData = null;

if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM pelanggan WHERE ID_Pelanggan='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// UPDATE
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE pelanggan SET
        Nama='$_POST[nama]',
        Tgl_Registrasi='$_POST[tgl]',
        Alamat='$_POST[alamat]'
        WHERE ID_Pelanggan='$_POST[id]'
    ");

    echo "<script>window.location='master_pelanggan.php';</script>";
}

if(isset($_POST['tambah'])){

    // Ambil ID terbesar
    $q = mysqli_query($conn,"SELECT MAX(ID_Pelanggan) as maxID FROM pelanggan");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    // Jika database kosong
    if(!$idMax){
        $idBaru = "PLG001";
    } else {
        $num = (int) substr($idMax,3);
        $num++;
        $idBaru = "PLG".sprintf("%03d",$num);
    }

    mysqli_query($conn,"INSERT INTO pelanggan
    VALUES ('$idBaru','$_POST[nama]','$_POST[tgl]','$_POST[alamat]')");
}

?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- CONTENT -->
<main class="app-main">
<div class="app-content container-fluid">

<h3 class="mb-3">Pelanggan</h3>

<form method="POST">
<div class="card card-body mb-3">
  <div class="row g-2">

    <?php if($editData): ?>
      <input type="hidden" name="id" value="<?= $editData['ID_Pelanggan'] ?>">
    <?php endif; ?>

    <div class="col-md-4">
      <input type="text" name="nama" class="form-control" placeholder="Nama"
      value="<?= $editData['Nama'] ?? '' ?>" required>
    </div>

    <div class="col-md-3">
      <input type="date" name="tgl" class="form-control" placeholder="Tanggal Registrasi"
      value="<?= $editData['Tgl_Registrasi'] ?? '' ?>" required>
    </div>

    <div class="col-md-4">
      <input type="text" name="alamat" class="form-control" placeholder="Alamat"
      value="<?= $editData['Alamat'] ?? '' ?>" required>
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

<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


<thead>
<tr>
<th>ID</th>
<th>Nama</th>
<th>Tgl Registrasi</th>
<th>Alamat</th>
<th>Aksi</th>
</tr>
</thead>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
flatpickr("input[name='tgl']", {
    dateFormat: "Y-m-d",
    allowInput: true
});
</script>


<tbody>
<?php
$data = mysqli_query($conn,"SELECT * FROM pelanggan");
while($d=mysqli_fetch_array($data)){
?>
<tr>
<td><?= $d['ID_Pelanggan'] ?></td>
<td><?= $d['Nama'] ?></td>
<td><?= $d['Tgl_Registrasi'] ?></td>
<td><?= $d['Alamat'] ?></td>
<td>

<a href="?edit=<?= $d['ID_Pelanggan'] ?>" 
   class="btn btn-warning btn-sm">
   <i class="bi bi-pencil"></i>
</a>

<a href="?hapus=<?= $d['ID_Pelanggan'] ?>" 
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