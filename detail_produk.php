<?php
$current_page = 'master_transaksi_produk';
$page_title = "Transaksi Produk";
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

// ================= TAMBAH DETAIL PRODUK =================
if(isset($_POST['tambah'])){
    $idTransaksi = $_POST['transaksi']; // Pilih transaksi yang sudah ada

    // Hitung total produk
    $totalProduk = 0;
    foreach($_POST['produk'] as $index => $idProduk){
        $jumlah = $_POST['jumlah'][$index];
        $qHarga = mysqli_query($conn,"SELECT Harga FROM produk WHERE ID_Produk='$idProduk'");
        $h = mysqli_fetch_array($qHarga);
        $subtotal = $h['Harga'] * $jumlah;
        $totalProduk += $subtotal;

        $idDetail = getNewID("DPR","detail_produk","ID_Detail_Produk",$conn);
        mysqli_query($conn,"INSERT INTO detail_produk
            (ID_Detail_Produk, ID_Transaksi, ID_Produk, Jumlah, Subtotal)
            VALUES ('$idDetail','$idTransaksi','$idProduk','$jumlah','$subtotal')");
    }

    // Update total harga transaksi dengan produk
    mysqli_query($conn,"UPDATE transaksi SET Total_Harga = Total_Harga + $totalProduk WHERE ID_Transaksi='$idTransaksi'");

    echo "<script>window.location='master_transaksi_produk.php';</script>";
}

// ================= HAPUS DETAIL PRODUK =================
if(isset($_GET['hapus'])){
    $idDetail = $_GET['hapus'];

    // Kurangi total harga di transaksi
    $q = mysqli_query($conn,"SELECT * FROM detail_produk WHERE ID_Detail_Produk='$idDetail'");
    $d = mysqli_fetch_array($q);
    mysqli_query($conn,"UPDATE transaksi SET Total_Harga = Total_Harga - {$d['Subtotal']} WHERE ID_Transaksi='{$d['ID_Transaksi']}'");

    // Hapus detail
    mysqli_query($conn,"DELETE FROM detail_produk WHERE ID_Detail_Produk='$idDetail'");
    echo "<script>window.location='master_transaksi_produk.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM detail_produk WHERE ID_Detail_Produk='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    $idDetail = $_POST['id'];
    $idProduk = $_POST['produk'];
    $jumlah = $_POST['jumlah'];

    // Hitung subtotal baru
    $qHarga = mysqli_query($conn,"SELECT Harga FROM produk WHERE ID_Produk='$idProduk'");
    $h = mysqli_fetch_array($qHarga);
    $subtotalBaru = $h['Harga'] * $jumlah;

    // Kurangi subtotal lama dari transaksi
    $qOld = mysqli_query($conn,"SELECT * FROM detail_produk WHERE ID_Detail_Produk='$idDetail'");
    $old = mysqli_fetch_array($qOld);
    mysqli_query($conn,"UPDATE transaksi SET Total_Harga = Total_Harga - {$old['Subtotal']} + $subtotalBaru WHERE ID_Transaksi='{$old['ID_Transaksi']}'");

    // Update detail produk
    mysqli_query($conn,"UPDATE detail_produk SET ID_Produk='$idProduk', Jumlah='$jumlah', Subtotal='$subtotalBaru' WHERE ID_Detail_Produk='$idDetail'");

    echo "<script>window.location='master_transaksi_produk.php';</script>";
}

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Transaksi Produk</h3>

<!-- FORM TAMBAH / EDIT DETAIL PRODUK -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<input type="hidden" name="id" value="<?= $editData['ID_Detail_Produk'] ?? '' ?>">

<div class="col-md-3">
<select name="transaksi" class="form-control" required <?= isset($editData)?'disabled':'' ?>>
<option value="">Pilih Transaksi</option>
<?php
$q = mysqli_query($conn,"SELECT * FROM transaksi ORDER BY Tanggal_Transaksi DESC");
while($t = mysqli_fetch_array($q)):
    $selected = ($editData['ID_Transaksi'] ?? '')==$t['ID_Transaksi']?'selected':'';
?>
<option value="<?= $t['ID_Transaksi'] ?>" <?= $selected ?>><?= $t['ID_Transaksi'] ?> - <?= $t['Status'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<select name="produk" class="form-control" required>
<option value="">Pilih Produk</option>
<?php
$q = mysqli_query($conn,"SELECT * FROM produk");
while($p = mysqli_fetch_array($q)):
    $selected = ($editData['ID_Produk'] ?? '')==$p['ID_Produk']?'selected':'';
?>
<option value="<?= $p['ID_Produk'] ?>" <?= $selected ?>><?= $p['Nama_Produk'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<input type="number" name="jumlah" class="form-control" min="1" value="<?= $editData['Jumlah'] ?? 1 ?>" required>
</div>

<div class="col-md-2">
<input type="number" class="form-control" value="<?= $editData['Subtotal'] ?? 0 ?>" readonly>
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

<hr>

<!-- TABEL DETAIL PRODUK -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID Detail</th>
<th>Transaksi</th>
<th>Produk</th>
<th>Jumlah</th>
<th>Subtotal</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data = mysqli_query($conn,"SELECT dp.*, pr.Nama_Produk, t.ID_Transaksi AS trx
                            FROM detail_produk dp
                            LEFT JOIN produk pr ON dp.ID_Produk = pr.ID_Produk
                            LEFT JOIN transaksi t ON dp.ID_Transaksi = t.ID_Transaksi
                            ORDER BY dp.ID_Detail_Produk DESC");
while($d = mysqli_fetch_array($data)):
?>
<tr>
<td><?= $d['ID_Detail_Produk'] ?></td>
<td><?= $d['trx'] ?></td>
<td><?= $d['Nama_Produk'] ?></td>
<td><?= $d['Jumlah'] ?></td>
<td>Rp <?= number_format($d['Subtotal']) ?></td>
<td>
<a href="?edit=<?= $d['ID_Detail_Produk'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
<a href="?hapus=<?= $d['ID_Detail_Produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus detail produk?')"><i class="bi bi-trash"></i></a>
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
