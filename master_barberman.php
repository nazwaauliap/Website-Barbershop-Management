<?php
$current_page = 'master_barberman';
$page_title = "Master Barberman";
include 'config/config.php';

// ================= TAMBAH =================
if(isset($_POST['tambah'])){
    $q = mysqli_query($conn,"SELECT MAX(ID_Barberman) as maxID FROM barberman");
    $d = mysqli_fetch_array($q);
    $idMax = $d['maxID'];

    $no = (int) substr($idMax,3,3);
    $no++;
    $idBaru = "BBR".sprintf("%03s",$no);

    mysqli_query($conn,"INSERT INTO barberman 
        VALUES ('$idBaru','$_POST[nama]','$_POST[keahlian]','$_POST[hp]','Aktif')");
}

// ================= HAPUS =================
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM barberman 
        WHERE ID_Barberman='$_GET[hapus]'");
    echo "<script>window.location='master_barberman.php';</script>";
}

// ================= MODE EDIT =================
$editData = null;
if(isset($_GET['edit'])){
    $idEdit = $_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM barberman WHERE ID_Barberman='$idEdit'");
    $editData = mysqli_fetch_array($q);
}

// ================= UPDATE =================
if(isset($_POST['update'])){
    mysqli_query($conn,"UPDATE barberman SET
        Nama_Barberman='$_POST[nama]',
        Keahlian='$_POST[keahlian]',
        No_HP='$_POST[hp]',
        Status='$_POST[status]'
        WHERE ID_Barberman='$_POST[id]'"
    );

    echo "<script>window.location='master_barberman.php';</script>";
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content container-fluid">

        <h3 class="mb-3">Barberman</h3>

        <!-- FORM TAMBAH / EDIT BARBERMAN -->
        <form method="POST">
            <div class="card card-body mb-3">
                <div class="row g-2">

                    <?php if($editData): ?>
                        <input type="hidden" name="id" value="<?= $editData['ID_Barberman'] ?>">
                    <?php endif; ?>

                    <div class="col-md-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama"
                               value="<?= $editData['Nama_Barberman'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-3">
                        <input type="text" name="keahlian" class="form-control" placeholder="Keahlian"
                               value="<?= $editData['Keahlian'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-2">
                        <input type="text" name="hp" class="form-control" placeholder="No HP"
                               value="<?= $editData['No_HP'] ?? '' ?>" required>
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="Aktif" <?= ($editData['Status'] ?? '')=='Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?= ($editData['Status'] ?? '')=='Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="Cuti" <?= ($editData['Status'] ?? '')=='Cuti' ? 'selected' : '' ?>>Cuti</option>
                        </select>
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

        <!-- TABEL BARBERMAN -->
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped table-datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Keahlian</th>
                            <th>No HP</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = mysqli_query($conn,"SELECT * FROM barberman");
                        while($d = mysqli_fetch_array($data)):
                        ?>
                        <tr>
                            <td><?= $d['ID_Barberman'] ?></td>
                            <td><?= $d['Nama_Barberman'] ?></td>
                            <td><?= $d['Keahlian'] ?></td>
                            <td><?= $d['No_HP'] ?></td>
                            <td><?= $d['Status'] ?></td>
                            <td>
                                <a href="?edit=<?= $d['ID_Barberman'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?hapus=<?= $d['ID_Barberman'] ?>" class="btn btn-danger btn-sm"
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
