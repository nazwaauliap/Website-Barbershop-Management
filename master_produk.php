<?php
$current_page = 'master_produk';
$page_title = "Master Produk";
include 'config/config.php';


// ================= TAMBAH & UPDATE =================
if(isset($_POST['simpan'])){

    $nama = $_POST['nama'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $pemasok = $_POST['pemasok'];

    // MODE EDIT
    if($_POST['id_produk'] != ''){
        mysqli_query($conn,"UPDATE produk SET
            Nama_Produk='$nama',
            Stok='$stok',
            Harga_Jual='$harga',
            ID_Pemasok='$pemasok'
            WHERE ID_Produk='$_POST[id_produk]'
        ");
    }

    // MODE TAMBAH
    else{
        $q = mysqli_query($conn,"SELECT MAX(ID_Produk) as maxID FROM produk");
        $d = mysqli_fetch_array($q);
        $idMax = $d['maxID'];

        $no = (int) substr($idMax,3,3);
        $no++;
        $idBaru = "PRD".sprintf("%03s",$no);

        mysqli_query($conn,"INSERT INTO produk 
        VALUES ('$idBaru','$nama','$stok','$harga','$pemasok')");
    }

    echo "<script>window.location='master_produk.php';</script>";
}


// ================= HAPUS =================
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM produk 
    WHERE ID_Produk='$_GET[hapus]'");
    echo "<script>window.location='master_produk.php';</script>";
}


// ================= MODE EDIT =================
$editData = null;

if(isset($_GET['edit'])){
    $q = mysqli_query($conn,"SELECT * FROM produk 
    WHERE ID_Produk='$_GET[edit]'");
    $editData = mysqli_fetch_array($q);
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>


<!-- CONTENT -->
<main class="app-main">
<div class="app-content container-fluid">

<h3 class="mb-3">Master Produk</h3>

<!-- FORM -->
<form method="POST">
<input type="hidden" name="id_produk" 
value="<?= $editData['ID_Produk'] ?? '' ?>">

<div class="card card-body mb-3">
<div class="row g-2">

<div class="col-md-3">
<input type="text" name="nama" class="form-control"
placeholder="Nama Produk"
value="<?= $editData['Nama_Produk'] ?? '' ?>" required>
</div>

<div class="col-md-2">
<input type="number" name="stok" class="form-control"
placeholder="Stok"
value="<?= $editData['Stok'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<input type="number" name="harga" class="form-control"
placeholder="Harga"
value="<?= $editData['Harga_Jual'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<select name="pemasok" class="form-control" required>
<option value="">-- Pilih Pemasok --</option>
<?php
$p = mysqli_query($conn,"SELECT * FROM pemasok");
while($ps=mysqli_fetch_array($p)){
    $selected = ($editData['ID_Pemasok'] ?? '') == $ps['ID_Pemasok'] ? 'selected' : '';
    echo "<option value='$ps[ID_Pemasok]' $selected>
            $ps[Nama_Pemasok]
          </option>";
}
?>
</select>
</div>

<div class="col-md-1">
<button class="btn btn-primary w-100" name="simpan">
<?= isset($editData) ? 'Update' : 'Add' ?>
</button>
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
<th>Stok</th>
<th>Harga</th>
<th>Pemasok</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php
$data = mysqli_query($conn,"
SELECT produk.*, pemasok.Nama_Pemasok 
FROM produk 
LEFT JOIN pemasok 
ON produk.ID_Pemasok=pemasok.ID_Pemasok
");

while($d=mysqli_fetch_array($data)){
?>
<tr>
<td><?= $d['ID_Produk'] ?></td>
<td><?= $d['Nama_Produk'] ?></td>
<td><?= $d['Stok'] ?></td>
<td>Rp <?= number_format($d['Harga_Jual']) ?></td>
<td><?= $d['Nama_Pemasok'] ?></td>

<td>
<a href="?edit=<?= $d['ID_Produk'] ?>" 
class="btn btn-warning btn-sm">
<i class="bi bi-pencil"></i>
</a>

<a href="?hapus=<?= $d['ID_Produk'] ?>" 
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
