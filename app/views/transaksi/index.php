<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi - Sistem Parkir</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f6f8fb;padding:20px}
        .container{max-width:1100px;margin:0 auto}
        .back-link{display:inline-block;margin-bottom:16px;padding:8px 16px;background:#95a5a6;color:#fff;border-radius:6px;text-decoration:none;font-weight:600}
        .back-link:hover{background:#7f8c8d}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:6px;overflow:hidden}
        th,td{padding:10px 12px;border-bottom:1px solid #eee;text-align:left}
        thead{background:#27374d;color:#fff}
        a.button{display:inline-block;padding:8px 12px;background:#2d6cdf;color:#fff;border-radius:6px;text-decoration:none}
        .btn-danger{background:#e74c3c}
        .actions{display:flex;gap:6px}
        .flash{padding:12px;border-radius:6px;margin-bottom:16px;font-weight:700}
        .flash.success{background:#e6ffed;color:#0b6624;border:1px solid #0f9b3a}
        .flash.error{background:#ffe6e6;color:#a81c1c;border:1px solid #e03a3a}
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?url=dashboard/index" class="back-link">← Kembali ke Dashboard</a>
        <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
            <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
        <?php } ?>
        <div class="header">
            <h2>📋 Daftar Transaksi</h2>
            <div>
                <a class="button" href="index.php?url=transaksi/create">+ Tambah Transaksi</a>
            </div>
        </div>

        <?php if ($data && mysqli_num_rows($data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Plat</th>
                        <th>Jenis</th>
                        <th>Area</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Status</th>
                        <th>Biaya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['plat_nomor'] ?? $row['plat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jenis_kendaraan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['id_area'] ?? '-') ?></td>
                            <td><?= $row['waktu_masuk'] ?></td>
                            <td><?= $row['waktu_keluar'] ?? '-' ?></td>
                            <td>
                                <?php 
                                    $status = strtolower(trim($row['status'] ?? 'masuk'));
                                    $statusText = $status === 'selesai' ? 'Selesai' : 'Masuk';
                                    $statusColor = $status === 'selesai' ? '#27ae60' : '#3498db';
                                ?>
                                <span style="background-color: <?= $statusColor ?>; color: white; padding: 6px 12px; border-radius: 3px; font-size: 13px; font-weight: bold; display: inline-block;">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($row['biaya_total'] ?? 0, 0, ',', '.') ?></td>
                            <td>
                                <div class="actions">
                                    <a class="button" href="index.php?url=transaksi/struk&id=<?= $row['id_parkir'] ?>">Struk</a>
                                    <a class="button" href="index.php?url=transaksi/edit&id=<?= $row['id_parkir'] ?>">Edit</a>
                                    <form method="POST" action="index.php?url=transaksi/delete" style="display:inline;">
                                        <?php echo Csrf::field(); ?>
                                        <input type="hidden" name="id" value="<?= $row['id_parkir'] ?>">
                                        <button class="button btn-danger" onclick="return confirm('Yakin hapus transaksi?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="background:#fff;padding:24px;border-radius:6px;text-align:center">Belum ada transaksi.</div>
        <?php endif; ?>
    </div>
</body>
</html>
