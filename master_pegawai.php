<?php
session_start();
$current_page = 'master_pegawai';
$page_title = "Master Pegawai";
include 'config/config.php';

// ================= CEK LOGIN =================
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: auth/login.php');
    exit();
}

// ================= SET ROLE SESSION =================
// Ambil role dari ID_Jabatan, jika belum ada di session
if (!isset($_SESSION['role'])) {
    $role_map = [
        'J001' => 'Admin',
        'J002' => 'Manajer',
        'J003' => 'Kasir'
        // tambah mapping sesuai data jabatan
    ];
    $user_id = $_SESSION['id_pegawai'];
    $q_role = mysqli_query($conn, "SELECT ID_Jabatan FROM pegawai WHERE ID_Pegawai='$user_id'");
    $r_role = mysqli_fetch_assoc($q_role);
    $_SESSION['role'] = $role_map[$r_role['ID_Jabatan']] ?? 'User';
}

// ================= TAMBAH PEGAWAI =================
if (isset($_POST['tambah'])) {
    $q = mysqli_query($conn, "SELECT MAX(ID_Pegawai) as maxID FROM pegawai");
    $d = mysqli_fetch_assoc($q);
    $idMax = $d['maxID'] ?? 'PGW000';

    $no = (int) substr($idMax, 3);
    $no++;
    $idBaru = "PGW" . sprintf("%03s", $no);

    $pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $user = null;
    if (in_array($_SESSION['role'], ['Admin','Manajer']) && !empty($_POST['user'])) {
        $user = $_POST['user'];
    }

    mysqli_query($conn, "INSERT INTO pegawai (
        ID_Pegawai, Nama_Pegawai, ID_Jabatan, No_HP, Alamat, Tanggal_Masuk, Status, Username, Password
    ) VALUES (
        '$idBaru',
        '".mysqli_real_escape_string($conn,$_POST['nama'])."',
        '".mysqli_real_escape_string($conn,$_POST['jabatan'])."',
        '".mysqli_real_escape_string($conn,$_POST['hp'])."',
        '".mysqli_real_escape_string($conn,$_POST['alamat'])."',
        '".mysqli_real_escape_string($conn,$_POST['tgl'])."',
        '".mysqli_real_escape_string($conn,$_POST['status'])."',
        ".($user ? "'$user'" : "NULL").",
        ".($pass ? "'$pass'" : "NULL")."
    )");
    echo "<script>window.location='master_pegawai.php';</script>";
}

// ================= HAPUS PEGAWAI =================
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM pegawai WHERE ID_Pegawai='".mysqli_real_escape_string($conn,$_GET['hapus'])."'");
    echo "<script>window.location='master_pegawai.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if (isset($_GET['edit'])) {
    $idEdit = mysqli_real_escape_string($conn,$_GET['edit']);
    $q = mysqli_query($conn, "SELECT * FROM pegawai WHERE ID_Pegawai='$idEdit'");
    $editData = mysqli_fetch_assoc($q);
}

// ================= UPDATE =================
if (isset($_POST['update'])) {
    $updateFields = "
        Nama_Pegawai = '".mysqli_real_escape_string($conn,$_POST['nama'])."',
        ID_Jabatan = '".mysqli_real_escape_string($conn,$_POST['jabatan'])."',
        No_HP = '".mysqli_real_escape_string($conn,$_POST['hp'])."',
        Alamat = '".mysqli_real_escape_string($conn,$_POST['alamat'])."',
        Tanggal_Masuk = '".mysqli_real_escape_string($conn,$_POST['tgl'])."',
        Status = '".mysqli_real_escape_string($conn,$_POST['status'])."'
    ";

    if (in_array($_SESSION['role'], ['Admin','Manajer'])) {
        if (!empty($_POST['user'])) $updateFields .= ", Username = '".mysqli_real_escape_string($conn,$_POST['user'])."'";
        if (!empty($_POST['password'])) {
            $hashPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $updateFields .= ", Password = '$hashPass'";
        }
    }

    mysqli_query($conn, "UPDATE pegawai SET $updateFields WHERE ID_Pegawai='".mysqli_real_escape_string($conn,$_POST['id'])."'");
    echo "<script>window.location='master_pegawai.php';</script>";
}

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<main class="app-main">
<div class="app-content container-fluid">

<h3>Pegawai</h3>

<!-- FORM TAMBAH / EDIT PEGAWAI -->
<form method="POST">
<div class="card card-body mb-3">
<div class="row g-2">

<?php if($editData): ?>
<input type="hidden" name="id" value="<?= $editData['ID_Pegawai'] ?>">
<?php endif; ?>

<div class="col-md-3">
<input type="text" name="nama" class="form-control" placeholder="Nama"
value="<?= $editData['Nama_Pegawai'] ?? '' ?>" required>
</div>

<div class="col-md-3">
<select name="jabatan" class="form-control" required>
<option value="">Pilih Jabatan</option>
<?php
$j = mysqli_query($conn, "SELECT * FROM jabatan");
while($jb = mysqli_fetch_assoc($j)):
$selected = ($editData['ID_Jabatan'] ?? '') == $jb['ID_Jabatan'] ? 'selected' : '';
?>
<option value="<?= $jb['ID_Jabatan'] ?>" <?= $selected ?>><?= $jb['Nama_Jabatan'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<input type="text" name="hp" class="form-control" placeholder="No HP"
value="<?= $editData['No_HP'] ?? '' ?>">
</div>

<div class="col-md-2">
<input type="text" name="alamat" class="form-control" placeholder="Alamat"
value="<?= $editData['Alamat'] ?? '' ?>">
</div>

<div class="col-md-2">
<select name="status" class="form-control">
<option value="Aktif" <?= ($editData['Status'] ?? '')=='Aktif'?'selected':'' ?>>Aktif</option>
<option value="Nonaktif" <?= ($editData['Status'] ?? '')=='Nonaktif'?'selected':'' ?>>Nonaktif</option>
<option value="Cuti" <?= ($editData['Status'] ?? '')=='Cuti'?'selected':'' ?>>Cuti</option>
</select>
</div>

<?php if(in_array($_SESSION['role'], ['Admin','Manajer'])): ?>
<div class="col-md-2">
<input type="text" name="user" class="form-control" placeholder="Username"
value="<?= $editData['Username'] ?? '' ?>">
</div>
<div class="col-md-2">
<input type="password" name="password" class="form-control" placeholder="Password">
<?php if($editData) echo '<small>Kosongkan jika tidak ingin mengubah password</small>'; ?>
</div>
<?php endif; ?>

<div class="col-md-3">
<input type="date" name="tgl" class="form-control"
value="<?= $editData['Tanggal_Masuk'] ?? '' ?>" required>
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

<!-- TABEL PEGAWAI -->
<div class="card">
<div class="card-body">
<table class="table table-bordered table-striped table-datatable">
<thead>
<tr>
<th>ID</th>
<th>Nama</th>
<th>Jabatan</th>
<th>No HP</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$data = mysqli_query($conn,"SELECT p.*, j.Nama_Jabatan FROM pegawai p LEFT JOIN jabatan j ON p.ID_Jabatan = j.ID_Jabatan");
while ($d = mysqli_fetch_assoc($data)):
?>
<tr>
<td><?= $d['ID_Pegawai'] ?></td>
<td><?= $d['Nama_Pegawai'] ?></td>
<td><?= $d['Nama_Jabatan'] ?></td>
<td><?= $d['No_HP'] ?></td>
<td><?= $d['Status'] ?></td>
<td>
<a href="?edit=<?= $d['ID_Pegawai'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
<a href="?hapus=<?= $d['ID_Pegawai'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</div>
</main>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("input[name='tgl']", {
    dateFormat: "Y-m-d",
    allowInput: true
});
</script>

<?php include 'includes/footer.php'; ?>
