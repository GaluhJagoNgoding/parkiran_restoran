<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Parkir Restoran</title>
</head>
<body>
            <div class="topbar">
                <h1>Dashboard <?= strtoupper($_SESSION['user']['role']) ?></h1>
                <a href="index.php?url=auth/logout" class="logout">Logout</a>
            </div>

            <!-- Kartu Statistik Ringkasan -->
            <div class="grid">
                <div class="card">
                    <h3>Total User</h3>
                    <div class="num"><?= $user['total'] ?></div>
                </div>
                <div class="card">
                    <h3>Total Kendaraan</h3>
                    <div class="num"><?= $kendaraan['total'] ?></div>
                </div>
                <div class="card">
                    <h3>Total Transaksi</h3>
                    <div class="num"><?= $transaksi['total'] ?></div>
                </div>
            </div>

            <!-- Shortcut Navigasi Cepat -->
            <div class="nav-short">
                <a href="index.php?url=kendaraan/index">Kendaraan</a>
                <a href="index.php?url=transaksi/index">Transaksi</a>
            </div>

            <!-- Bagian Menu Admin (Grid) -->
            <?php if($_SESSION['user']['role']=='admin'){ ?>
                <div class="admin-section">
                    <h2>Menu Admin</h2>
                    <div class="menu-grid">
                        <a href="index.php?url=user/index" class="menu-item">👥 Manajemen User</a>
                        <a href="index.php?url=tarif/index" class="menu-item">💰 Tarif Parkir</a>
                        <a href="index.php?url=area/index" class="menu-item">📍 Area Parkir</a>
                        <a href="index.php?url=log/index" class="menu-item">📊 Log Aktivitas</a>
                    </div>
                </div>
            <?php } ?>
</body>
</html>