<?php
$current_page = 'komisi_barberman';
$page_title = "Komisi Barberman";
include 'config/config.php';

// ================= AUTO ID KMS =================
function getNewIDKomisi($conn){
    $q = mysqli_query($conn,"SELECT MAX(ID_Komisi) as maxID FROM komisi_barberman");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    if(!$idMax){
        return "KMS001";
    } else {
        $num = (int) substr($idMax,3) + 1;
        return "KMS".sprintf("%03d",$num);
    }
}

// ================= HITUNG KOMISI OTOMATIS =================
$persenKomisi = 0.1; // 10%

if(isset($_POST['hitung_komisi'])){
    // Ambil semua detail layanan yang belum punya komisi
    $q = mysqli_query($conn,"
        SELECT dl.*, t.ID_Transaksi, dl.ID_Barberman
        FROM detail_layanan dl
        LEFT JOIN komisi_barberman kb
        ON dl.ID_Transaksi=kb.ID_Transaksi AND dl.ID_Barberman=kb.ID_Barberman
        LEFT JOIN transaksi t ON dl.ID_Transaksi=t.ID_Transaksi
        WHERE kb.ID_Komisi IS NULL
    ");

    while($d = mysqli_fetch_array($q)){
        $komisi = $d['Subtotal'] * $persenKomisi;
        $idKomisi = getNewIDKomisi($conn);

        mysqli_query($conn,"INSERT INTO komisi_barberman (
            ID_Komisi, ID_Transaksi, ID_Barberman, Komisi
        ) VALUES (
            '$idKomisi',
            '".$d['ID_Transaksi']."',
            '".$d['ID_Barberman']."',
            '$komisi'
        )");
    }

    echo "<script>window.location='komisi_barberman.php';</script>";
}

// ================= HAPUS =================
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM komisi_barberman WHERE ID_Komisi='$id'");
    echo "<script>window.location='komisi_barberman.php';</script>";
}

// ================= TAMPILKAN DATA =================
$data = mysqli_query($conn,"
    SELECT kb.*, b.Nama_Barberman, t.ID_Pelanggan, t.Total_Harga, t.Tanggal_Transaksi
    FROM komisi_barberman kb
    LEFT JOIN barberman b ON kb.ID_Barberman=b.ID_Barberman
    LEFT JOIN transaksi t ON kb.ID_Transaksi=t.ID_Transaksi
    ORDER BY t.Tanggal_Transaksi DESC
");
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Komisi Barberman</h3>

<form method="POST">
    <button class="btn btn-primary mb-3" name="hitung_komisi">Hitung Komisi Baru</button>
</form>

<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID Komisi</th>
<th>ID Transaksi</th>
<th>Barberman</th>
<th>Total Transaksi</th>
<th>Tanggal Transaksi</th>
<th>Komisi (Rp)</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php while($d = mysqli_fetch_array($data)): ?>
<tr>
<td><?= $d['ID_Komisi'] ?></td>
<td><?= $d['ID_Transaksi'] ?></td>
<td><?= $d['Nama_Barberman'] ?></td>
<td>Rp <?= number_format($d['Total_Harga']) ?></td>
<td><?= $d['Tanggal_Transaksi'] ?></td>
<td>Rp <?= number_format($d['Komisi']) ?></td>
<td>
    <a href="?hapus=<?= $d['ID_Komisi'] ?>" class="btn btn-danger btn-sm"
    onclick="return confirm('Hapus komisi?')">Hapus</a>
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
