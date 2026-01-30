<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-bell-fill"></i>
          <?php 
          $query_notif = "SELECT COUNT(*) as total FROM reservasi WHERE DATE(Tanggal_Reservasi) = CURDATE() AND Status = 'Pending'";
          $result_notif = $conn->query($query_notif);
          $notif_count = $result_notif ? $result_notif->fetch_assoc()['total'] : 0;
          if($notif_count > 0): ?>
          <span class="navbar-badge badge text-bg-warning"><?php echo $notif_count; ?></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <span class="dropdown-item dropdown-header"><?php echo $notif_count; ?> Notifikasi</span>
          <div class="dropdown-divider"></div>
          <a href="reservasi.php" class="dropdown-item">
            <i class="bi bi-calendar-check me-2"></i> <?php echo $notif_count; ?> reservasi pending
            <span class="float-end text-secondary fs-7">hari ini</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="reservasi.php" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
          <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
          <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
        </a>
      </li>
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
          <img src="./assets/img/user.jpeg" class="user-image rounded-circle shadow" alt="User Image" />
          <span class="d-none d-md-inline"><?php echo isset($_SESSION['nama_pegawai']) ? $_SESSION['nama_pegawai'] : 'Nazwa Arutomo'; ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <li class="user-header text-bg-primary">
            <img src="./assets/img/user.jpeg" class="rounded-circle shadow" alt="User Image" />
            <p>
              <?php echo isset($_SESSION['nama_pegawai']) ? $_SESSION['nama_pegawai'] : 'Nazwa Arutomo'; ?> - Administrator
              <small>Member since Nov. 2023</small>
            </p>
          </li>
          <li class="user-footer">
            <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
            <a href="logout.php" class="btn btn-default btn-flat float-end">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>