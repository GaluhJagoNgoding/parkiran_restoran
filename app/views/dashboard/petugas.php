<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Sistem Parkir Restoran</title>
    <style>
        :root {
            --sidebar: #0b1b2b;
            --accent: #2d6cdf;
            --muted: #6b7280;
            --card: #ffffff;
            --bg: #f3f6fb;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            height: 100%;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--bg);
        }
        .app {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background: linear-gradient(180deg, var(--sidebar), #0e273a);
            color: #e6eef8;
            padding: 28px 16px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .brand {
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 20px;
            color: white;
        }
        .brand strong {
            color: #ffd700;
        }
        .sidebar nav {
            margin-top: 20px;
        }
        .sidebar nav a {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 14px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        .sidebar .role {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            font-size: 12px;
            border-left: 3px solid #ffd700;
        }
        .sidebar .role strong {
            color: #ffd700;
        }
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 24px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: var(--card);
            padding: 16px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .topbar h1 {
            font-size: 24px;
            color: #0b1220;
            margin: 0;
        }
        .logout {
            background: linear-gradient(90deg, var(--accent), #4aa2ff);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(45, 108, 223, 0.3);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .card {
            background: var(--card);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            border-left: 4px solid var(--accent);
        }
        .card h3 {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 12px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .card .num {
            font-size: 32px;
            font-weight: 800;
            color: var(--sidebar);
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #0b1220;
            margin: 20px 0 16px 0;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
        }
        .menu-item {
            padding: 16px;
            background: linear-gradient(135deg, var(--accent), #4aa2ff);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            display: block;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(45, 108, 223, 0.2);
        }
        .menu-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(45, 108, 223, 0.3);
        }
        .info-box {
            background: var(--card);
            padding: 16px;
            border-radius: 10px;
            margin-top: 16px;
            border-left: 4px solid var(--accent);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .info-box p {
            color: #555;
            line-height: 1.6;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 16px;
            }
            .main {
                margin-left: 0;
                padding: 16px;
            }
            .topbar {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
            .topbar h1 {
                font-size: 20px;
            }
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">Parkir <strong>Restoran</strong></div>
            <nav>
                <a href="../public/index.php?url=dashboard/index">📊 Dashboard</a>
                <a href="../public/index.php?url=transaksi/index">💳 Transaksi</a>
                <a href="../public/index.php?url=kendaraan/index">🚗 Kendaraan</a>
            </nav>
            <div class="role">Role: <strong><?= strtoupper($_SESSION['user']['role'] ?? 'PETUGAS') ?></strong></div>
        </aside>

        <div class="main">
            <div class="topbar">
                <h1>Dashboard Petugas</h1>
                <a href="../public/index.php?url=auth/logout" class="logout">🚪 Logout</a>
            </div>

            <div class="grid">
                <div class="card">
                    <h3>📝 Total Transaksi</h3>
                    <div class="num"><?= $transaksi['total'] ?? '0' ?></div>
                </div>
                <div class="card">
                    <h3>🚗 Kendaraan Aktif</h3>
                    <div class="num"><?= $kendaraan['aktif'] ?? '0' ?></div>
                </div>
                <div class="card">
                    <h3>💰 Pendapatan Hari Ini</h3>
                    <div class="num">Rp <?= isset($transaksi['pendapatan']) ? number_format($transaksi['pendapatan'], 0, ',', '.') : '0' ?></div>
                </div>
            </div>

            <div class="section-title">Transaksi Terbaru</div>
            <div class="info-box" style="margin-bottom:18px;">
                <?php if (isset($recent_transactions) && $recent_transactions && mysqli_num_rows($recent_transactions) > 0): ?>
                    <table style="width:100%;border-collapse:collapse">
                        <thead><tr style="text-align:left"><th>Waktu</th><th>Plat</th><th>Jenis</th><th>Status</th><th>Biaya</th></tr></thead>
                        <tbody>
                        <?php while($r = mysqli_fetch_assoc($recent_transactions)): ?>
                            <tr>
                                <td style="padding:6px 0"><?= $r['waktu_masuk'] ?></td>
                                <td><?= htmlspecialchars($r['plat_nomor'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['jenis_kendaraan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['status']) ?></td>
                                <td>Rp <?= number_format($r['biaya_total'] ?? 0,0,',','.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div>Tidak ada transaksi terbaru.</div>
                <?php endif; ?>
            </div>

            <div class="section-title">Akses Cepat</div>
            <div class="menu-grid">
                <a href="../public/index.php?url=transaksi/index" class="menu-item">💳 Manajemen Transaksi</a>
                <a href="../public/index.php?url=kendaraan/index" class="menu-item">🚗 Data Kendaraan</a>
            </div>

            <div class="info-box">
                <p><strong>📌 Informasi:</strong> Sebagai petugas parkir, Anda dapat mencatat transaksi masuk dan keluar kendaraan, serta melihat data kendaraan yang sedang parkir di restoran.</p>
            </div>
        </div>
    </div>
</body>
</html>
