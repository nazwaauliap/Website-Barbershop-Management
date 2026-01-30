<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
<a href="dashboard.php" class="brand-link d-flex align-items-center text-decoration-none">
    <i class="fas fa-scissors me-2" style="font-size: 1.5rem; transform: rotate(-45deg); color: #333;"></i>
    <span class="brand-text fw-bold" style="font-size: 1.3rem;">NZ BARBERSHOP</span>
</a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" role="menu">
        <!-- Dashboard -->
        <li class="nav-item">
          <a href="./dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Master Data -->
        <li class="nav-header">MASTER DATA</li>
        <li class="nav-item">
          <a href="./master_pelanggan.php" class="nav-link <?php echo ($current_page == 'master_pelanggan') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-person"></i>
            <p>Pelanggan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_barberman.php" class="nav-link <?php echo ($current_page == 'master_barberman') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-scissors"></i>
            <p>Barberman</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_layanan.php" class="nav-link <?php echo ($current_page == 'master_layanan') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-list-check"></i>
            <p>Layanan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_produk.php" class="nav-link <?php echo ($current_page == 'master_produk') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-bag"></i>
            <p>Produk</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_pemasok.php" class="nav-link <?php echo ($current_page == 'master_pemasok') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-truck"></i>
            <p>Pemasok</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_jabatan.php" class="nav-link <?php echo ($current_page == 'master_jabatan') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-diagram-3"></i>
            <p>Jabatan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_pegawai.php" class="nav-link <?php echo ($current_page == 'master_pegawai') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-people"></i>
            <p>Pegawai</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./master_diskon.php" class="nav-link <?php echo ($current_page == 'master_diskon') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-percent"></i>
            <p>Diskon</p>
          </a>
        </li>

        <!-- Transaksi -->
        <li class="nav-header">TRANSAKSI</li>
        <li class="nav-item">
          <a href="./transaksi.php" class="nav-link <?php echo ($current_page == 'transaksi') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-receipt-cutoff"></i>
            <p>Transaksi</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./detail_layanan.php" class="nav-link <?php echo ($current_page == 'detail_layanan') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-scissors"></i>
            <p>Detail Layanan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./detail_produk.php" class="nav-link <?php echo ($current_page == 'detail_produk') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-bag-check"></i>
            <p>Detail Produk</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./reservasi.php" class="nav-link <?php echo ($current_page == 'reservasi') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-calendar-check"></i>
            <p>Reservasi</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./komisi_barberman.php" class="nav-link <?php echo ($current_page == 'komisi_barberman') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-cash-coin"></i>
            <p>Komisi Barberman</p>
          </a>
        </li>

        <!-- Laporan -->
        <li class="nav-header">LAPORAN</li>
        <li class="nav-item">
          <a href="./laporan_layanan.php" class="nav-link <?php echo ($current_page == 'laporan_layanan') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-list-task"></i>
            <p>Rekap Layanan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./laporan_produk.php" class="nav-link <?php echo ($current_page == 'laporan_produk') ? 'active' : ''; ?>">
            <i class="nav-icon bi bi-basket3"></i>
            <p>Rekap Produk</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>