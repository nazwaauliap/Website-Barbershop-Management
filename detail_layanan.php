<?php
$current_page = 'master_transaksi';
$page_title = "Master Transaksi";
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

// ================= TAMBAH TRANSAKSI =================
if(isset($_POST['tambah'])){
    $idTransaksi = getNewID("TRX","transaksi","ID_Transaksi",$conn);
    $tanggal = $_POST['tgl'];
    $idPelanggan = $_POST['pelanggan'];
    $idKasir = $_SESSION['id_user']; // ambil dari session
    $status = $_POST['status'];

    // Hitung total harga dari detail layanan
    $totalHarga = 0;
    foreach($_POST['layanan'] as $index => $idLayanan){
        $jumlah = $_POST['jumlah'][$index];
        $idBarberman = $_POST['barberman'][$index];
        $qHarga = mysqli_query($conn,"SELECT Harga FROM layanan WHERE ID_Layanan='$idLayanan'");
        $h = mysqli_fetch_array($qHarga);
        $subtotal = $h['Harga'] * $jumlah;
        $totalHarga += $subtotal;
    }

    // Simpan transaksi
    mysqli_query($conn,"INSERT INTO transaksi 
        (ID_Transaksi, ID_Pelanggan, ID_Kasir, Total_Harga, Tanggal_Transaksi, Status)
        VALUES ('$idTransaksi','$idPelanggan','$idKasir','$totalHarga','$tanggal','$status')"
    );

    // Simpan detail layanan
    foreach($_POST['layanan'] as $index => $idLayanan){
        $jumlah = $_POST['jumlah'][$index];
        $idBarberman = $_POST['barberman'][$index];
        $qHarga = mysqli_query($conn,"SELECT Harga FROM layanan WHERE ID_Layanan='$idLayanan'");
        $h = mysqli_fetch_array($qHarga);
        $subtotal = $h['Harga'] * $jumlah;
        $idDetail = getNewID("DLN","detail_layanan","ID_Detail_Layanan",$conn);

        mysqli_query($conn,"INSERT INTO detail_layanan
            (ID_Detail_Layanan, ID_Transaksi, ID_Layanan, ID_Barberman, Jumlah, Subtotal)
            VALUES ('$idDetail','$idTransaksi','$idLayanan','$idBarberman','$jumlah','$subtotal')"
        );
    }

    echo "<script>window.location='master_transaksi.php';</script>";
}

// ================= HAPUS TRANSAKSI =================
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM detail_layanan WHERE ID_Transaksi='$id'");
    mysqli_query($conn,"DELETE FROM transaksi WHERE ID_Transaksi='$id'");
    echo "<script>window.location='master_transaksi.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM transaksi WHERE ID_Transaksi='$idEdit'");
    $editData = mysqli_fetch_array($q);

    $detailData = [];
    $qd = mysqli_query($conn,"SELECT * FROM detail_layanan WHERE ID_Transaksi='$idEdit'");
    while($row = mysqli_fetch_array($qd)){
        $detailData[] = $row;
    }
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    $idTransaksi = $_POST['id'];
    $tanggal = $_POST['tgl'];
    $idPelanggan = $_POST['pelanggan'];
    $status = $_POST['status'];

    // Hitung total harga baru
    $totalHarga = 0;
    foreach($_POST['layanan'] as $index => $idLayanan){
        $jumlah = $_POST['jumlah'][$index];
        $qHarga = mysqli_query($conn,"SELECT Harga FROM layanan WHERE ID_Layanan='$idLayanan'");
        $h = mysqli_fetch_array($qHarga);
        $totalHarga += $h['Harga'] * $jumlah;
    }

    mysqli_query($conn,"UPDATE transaksi SET 
        ID_Pelanggan='$idPelanggan', 
        Tanggal_Transaksi='$tanggal', 
        Status='$status', 
        Total_Harga='$totalHarga' 
        WHERE ID_Transaksi='$idTransaksi'");

    // Hapus detail lama
    mysqli_query($conn,"DELETE FROM detail_layanan WHERE ID_Transaksi='$idTransaksi'");

    // Tambah detail baru
    foreach($_POST['layanan'] as $index => $idLayanan){
        $jumlah = $_POST['jumlah'][$index];
        $idBarberman = $_POST['barberman'][$index];
        $qHarga = mysqli_query($conn,"SELECT Harga FROM layanan WHERE ID_Layanan='$idLayanan'");
        $h = mysqli_fetch_array($qHarga);
        $subtotal = $h['Harga'] * $jumlah;
        $idDetail = getNewID("DLN","detail_layanan","ID_Detail_Layanan",$conn);

        mysqli_query($conn,"INSERT INTO detail_layanan
            (ID_Detail_Layanan, ID_Transaksi, ID_Layanan, ID_Barberman, Jumlah, Subtotal)
            VALUES ('$idDetail','$idTransaksi','$idLayanan','$idBarberman','$jumlah','$subtotal')"
        );
    }

    echo "<script>window.location='master_transaksi.php';</script>";
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

<div class="col-md-3">
<select name="pelanggan" class="form-control" required>
<option value="">Pilih Pelanggan</option>
<?php
$q = mysqli_query($conn,"SELECT * FROM pelanggan");
while($p = mysqli_fetch_array($q)):
    $selected = ($editData['ID_Pelanggan'] ?? '')==$p['ID_Pelanggan'] ? 'selected' : '';
?>
<option value="<?= $p['ID_Pelanggan'] ?>" <?= $selected ?>><?= $p['Nama'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<input type="date" name="tgl" class="form-control" required
value="<?= $editData['Tanggal_Transaksi'] ?? date('Y-m-d') ?>">
</div>

<div class="col-md-2">
<select name="status" class="form-control">
<option value="Selesai" <?= ($editData['Status']??'')=='Selesai'?'selected':'' ?>>Selesai</option>
<option value="Proses" <?= ($editData['Status']??'')=='Proses'?'selected':'' ?>>Proses</option>
</select>
</div>

</div>

<hr>

<h5>Detail Layanan</h5>
<div id="detail-layanan">
<?php
if(isset($detailData) && count($detailData) > 0){
    foreach($detailData as $index => $d):
?>
<div class="row g-2 mb-2 layanan-item">
<div class="col-md-3">
<select name="layanan[]" class="form-control" required>
<option value="">Pilih Layanan</option>
<?php
$ql = mysqli_query($conn,"SELECT * FROM layanan");
while($l = mysqli_fetch_array($ql)):
    $selected = ($d['ID_Layanan']==$l['ID_Layanan'])?'selected':'';
?>
<option value="<?= $l['ID_Layanan'] ?>" <?= $selected ?>><?= $l['Nama_Layanan'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<select name="barberman[]" class="form-control" required>
<option value="">Pilih Barberman</option>
<?php
$qb = mysqli_query($conn,"SELECT * FROM barberman WHERE Status='Aktif'");
while($b = mysqli_fetch_array($qb)):
    $selected = ($d['ID_Barberman']==$b['ID_Barberman'])?'selected':'';
?>
<option value="<?= $b['ID_Barberman'] ?>" <?= $selected ?>><?= $b['Nama_Barberman'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<input type="number" name="jumlah[]" class="form-control" value="<?= $d['Jumlah'] ?>" min="1" required>
</div>

<div class="col-md-2">
<input type="number" class="form-control" value="<?= $d['Subtotal'] ?>" readonly>
</div>

<div class="col-md-2">
<button type="button" class="btn btn-danger remove-item">Hapus</button>
</div>

</div>
<?php
    endforeach;
} else {
?>
<!-- Default satu baris kosong -->
<div class="row g-2 mb-2 layanan-item">
<div class="col-md-3">
<select name="layanan[]" class="form-control" required>
<option value="">Pilih Layanan</option>
<?php
$ql = mysqli_query($conn,"SELECT * FROM layanan");
while($l = mysqli_fetch_array($ql)):
?>
<option value="<?= $l['ID_Layanan'] ?>"><?= $l['Nama_Layanan'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<select name="barberman[]" class="form-control" required>
<option value="">Pilih Barberman</option>
<?php
$qb = mysqli_query($conn,"SELECT * FROM barberman WHERE Status='Aktif'");
while($b = mysqli_fetch_array($qb)):
?>
<option value="<?= $b['ID_Barberman'] ?>"><?= $b['Nama_Barberman'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<input type="number" name="jumlah[]" class="form-control" value="1" min="1" required>
</div>

<div class="col-md-2">
<input type="number" class="form-control" value="0" readonly>
</div>

<div class="col-md-2">
<button type="button" class="btn btn-success add-item">Tambah</button>
</div>
</div>
<?php } ?>
</div>

<br>
<div class="col-md-2">
<?php if(isset($editData)): ?>
<button class="btn btn-success w-100" name="update">Update</button>
<?php else: ?>
<button class="btn btn-primary w-100" name="tambah">Add</button>
<?php endif; ?>
</div>

</div>
</form>

<hr>

<!-- TABEL TRANSAKSI -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID</th>
<th>Pelanggan</th>
<th>Tanggal</th>
<th>Status</th>
<th>Total Harga</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data = mysqli_query($conn,"SELECT t.*, p.Nama AS Nama_Pelanggan 
                            FROM transaksi t 
                            LEFT JOIN pelanggan p ON t.ID_Pelanggan=p.ID_Pelanggan
                            ORDER BY t.Tanggal_Transaksi DESC");
while($d = mysqli_fetch_array($data)):
?>
<tr>
<td><?= $d['ID_Transaksi'] ?></td>
<td><?= $d['Nama_Pelanggan'] ?></td>
<td><?= $d['Tanggal_Transaksi'] ?></td>
<td><?= $d['Status'] ?></td>
<td>Rp <?= number_format($d['Total_Harga']) ?></td>
<td>
<a href="?edit=<?= $d['ID_Transaksi'] ?>" class="btn btn-warning btn-sm">
<i class="bi bi-pencil"></i>
</a>
<a href="?hapus=<?= $d['ID_Transaksi'] ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Hapus transaksi?')">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // Tambah baris baru
    $(document).on('click','.add-item',function(){
        var row = $(this).closest('.layanan-item').clone();
        row.find('input').val(1);
        row.find('.add-item').removeClass('btn-success add-item').addClass('btn-danger remove-item').text('Hapus');
        $('#detail-layanan').append(row);
    });

    // Hapus baris
    $(document).on('click','.remove-item',function(){
        $(this).closest('.layanan-item').remove();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
