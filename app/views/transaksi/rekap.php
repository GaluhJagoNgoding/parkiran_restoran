<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Transaksi - Sistem Parkir Restoran</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #f3f6fb; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .back-link { display: inline-block; margin-bottom: 16px; padding: 8px 16px; background: #95a5a6; color: #fff; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .back-link:hover { background: #7f8c8d; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; background: white; padding: 16px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header h1 { font-size: 24px; color: #0b1220; }
        .filter-box { background: white; padding: 16px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .filter-row { display: flex; gap: 12px; align-items: flex-end; }
        .filter-row input, .filter-row button { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .filter-row input { flex: 1; }
        .filter-row button { background: #2d6cdf; color: white; border: none; cursor: pointer; font-weight: 600; }
        .filter-row button:hover { background: #1a5cc4; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid #2d6cdf; }
        .stat-card h3 { font-size: 12px; color: #6b7280; text-transform: uppercase; margin-bottom: 8px; font-weight: 600; letter-spacing: 1px; }
        .stat-card .value { font-size: 28px; font-weight: 800; color: #2d6cdf; }
        .stat-card.secondary { border-left-color: #10b981; }
        .stat-card.secondary .value { color: #10b981; }
        .stat-card.tertiary { border-left-color: #f59e0b; }
        .stat-card.tertiary .value { color: #f59e0b; }
        .chart-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .chart-section h2 { font-size: 18px; font-weight: 700; color: #0b1220; margin-bottom: 16px; }
        .breakdown-table { width: 100%; border-collapse: collapse; }
        .breakdown-table th, .breakdown-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .breakdown-table th { background: #27374d; color: white; font-weight: 600; }
        .breakdown-table tr:hover { background: #f9f9f9; }
        .percentage-bar { width: 100%; height: 20px; background: #eee; border-radius: 4px; overflow: hidden; }
        .percentage-fill { height: 100%; background: linear-gradient(90deg, #2d6cdf, #4aa2ff); transition: width 0.3s; }
        .recent-trans { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .recent-trans h2 { font-size: 18px; font-weight: 700; color: #0b1220; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #27374d; color: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        tbody tr:hover { background: #f9f9f9; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-selesai { background: #d1e8d9; color: #0b6624; }
        .export-btn { background: #10b981; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 16px; }
        .export-btn:hover { background: #059669; }
        .footer { text-align: center; margin-top: 40px; padding: 20px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?url=dashboard/index" class="back-link">← Kembali ke Dashboard</a>
        
        <div class="header">
            <h1>📊 Rekap Transaksi & Pendapatan</h1>
            <span style="color: #999; font-size: 14px;">Owner Report - <?= date('d M Y') ?></span>
        </div>

        <!-- Filter Tanggal -->
        <div class="filter-box">
            <form method="GET" action="index.php?url=transaksi/rekap" class="filter-row">
                <input type="date" name="dari" value="<?= htmlspecialchars($tgl_dari) ?>" placeholder="Dari Tanggal">
                <input type="date" name="sampai" value="<?= htmlspecialchars($tgl_sampai) ?>" placeholder="Sampai Tanggal">
                <button type="submit">🔍 Filter</button>
                <a href="index.php?url=transaksi/rekap" style="margin-left: auto; padding: 8px 12px; border-radius: 6px; background: #95a5a6; color: white; text-decoration: none; font-weight: 600;">Reset</a>
            </form>
        </div>

        <!-- Statistik Ringkasan -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Transaksi Selesai</h3>
                <div class="value"><?= number_format($stat['total_transaksi'] ?? 0) ?></div>
            </div>
            <div class="stat-card secondary">
                <h3>Total Pendapatan Periode</h3>
                <div class="value">Rp <?= number_format($stat['total_pendapatan'] ?? 0, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card tertiary">
                <h3>Rata-rata per Transaksi</h3>
                <div class="value">Rp <?= number_format($stat['rata_pendapatan'] ?? 0, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <h3>Kendaraan Sedang Parkir</h3>
                <div class="value"><?= $aktif ?></div>
            </div>
        </div>

        <!-- Breakdown Jenis Kendaraan -->
        <?php if ($q_breakdown && mysqli_num_rows($q_breakdown) > 0): ?>
            <div class="chart-section">
                <h2>📈 Performa Berdasarkan Jenis Kendaraan</h2>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Jenis Kendaraan</th>
                            <th style="text-align: center;">Jumlah</th>
                            <th>Total Pendapatan</th>
                            <th style="width: 30%;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $total_semua = $stat['total_pendapatan'] ?? 1;
                            while ($row = mysqli_fetch_assoc($q_breakdown)): 
                                $persen = ($total_semua > 0) ? ($row['total'] / $total_semua * 100) : 0;
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['jenis_kendaraan']) ?></strong></td>
                                <td style="text-align: center;"><?= $row['jumlah'] ?></td>
                                <td>Rp <?= number_format($row['total'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <div class="percentage-bar">
                                        <div class="percentage-fill" style="width: <?= round($persen) ?>%"></div>
                                    </div>
                                    <small><?= round($persen, 1) ?>%</small>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Transaksi Terbaru -->
        <div class="recent-trans">
            <h2>📋 Transaksi Terbaru Periode (Max 20)</h2>
            <?php if ($q_recent && mysqli_num_rows($q_recent) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Area</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($q_recent)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['plat_nomor'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($row['jenis_kendaraan'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['nama_area'] ?? '-') ?></td>
                                <td><?= $row['waktu_masuk'] ?></td>
                                <td><?= $row['waktu_keluar'] ?? '-' ?></td>
                                <td><strong>Rp <?= number_format($row['biaya_total'] ?? 0, 0, ',', '.') ?></strong></td>
                                <td>
                                    <span class="badge badge-selesai">✓ Selesai</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p>Tidak ada transaksi selesai dalam periode yang dipilih.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Export Button -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php?url=transaksi/rekap&dari=<?= urlencode($tgl_dari) ?>&sampai=<?= urlencode($tgl_sampai) ?>&print=1" class="export-btn" target="_blank">
                🖨️ Print / Export PDF
            </a>
        </div>

        <div class="footer">
            <p>Laporan Rekap Transaksi Sistem Parkir Restoran | Generated: <?= date('d-m-Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
