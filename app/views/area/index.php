<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Parkir - Sistem Parkir</title>
    <style>
        :root {
            --primary: #2d6cdf;
            --secondary: #667eea;
            --success: #00d084;
            --danger: #f5576c;
            --warning: #ffc107;
            --muted: #6b7280;
            --light: #f3f6fb;
            --dark: #0b1220;
            --border-radius: 8px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            padding: 8px 12px;
            border-radius: var(--border-radius);
        }
        .back-link:hover {
            background: rgba(45, 108, 223, 0.1);
            transform: translateX(-3px);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .header h2 {
            font-size: 28px;
            color: var(--dark);
        }
        .btn-add {
            background: linear-gradient(90deg, var(--success), #00a86b);
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 208, 132, 0.3);
        }
        .flash {
            padding: 12px 16px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }
        .flash.success {
            background: rgba(0, 208, 132, 0.1);
            border-left-color: var(--success);
            color: #00a86b;
        }
        .flash.error {
            background: rgba(245, 87, 108, 0.1);
            border-left-color: var(--danger);
            color: var(--danger);
        }
        .flash.warning {
            background: rgba(255, 193, 7, 0.1);
            border-left-color: var(--warning);
            color: #f57f17;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        table thead {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
        }
        table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e0e0e0;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        table tbody tr:hover {
            background: var(--light);
            transition: all 0.2s;
        }
        .capacity {
            font-weight: 600;
            color: var(--success);
            padding: 6px 12px;
            background: rgba(0, 208, 132, 0.1);
            border-radius: 4px;
            display: inline-block;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-edit, .btn-delete {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-edit {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(45, 108, 223, 0.3);
        }
        .btn-delete {
            background: linear-gradient(90deg, #f093fb, var(--danger));
            color: white;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(245, 87, 108, 0.3);
        }
        .empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            .header h2 {
                font-size: 22px;
            }
            .btn-add {
                width: 100%;
                text-align: center;
            }
            table {
                font-size: 13px;
            }
            table th, table td {
                padding: 10px 8px;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn-edit, .btn-delete {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Menampilkan notifikasi flash message -->
        <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
            <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
        <?php } ?>
        <a href="index.php?url=dashboard/index" class="back-link">← Kembali ke Dashboard</a>
        
        <div class="header">
            <h2>📍 Area Parkir</h2>
            <a href="index.php?url=area/create" class="btn-add">+ Tambah Area</a>
        </div>

        <!-- Cek apakah ada data area yang ditampilkan -->
        <?php if ($data && mysqli_num_rows($data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Area</th>
                        <th>Kapasitas</th>
                        <th>Lokasi</th>

                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($area = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($area['nama_area']) ?></td>
                            <td class="capacity"><?= $area['kapasitas'] ?> Slot</td>
                            <td><?= htmlspecialchars($area['lokasi'] ?? '-') ?></td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Tombol Edit dan Hapus -->
                                    <a href="index.php?url=area/edit&id=<?= $area['id_area'] ?>" class="btn-edit">Edit</a>
                                        <form method="POST" action="index.php?url=area/delete" style="display:inline;" onsubmit="return confirm('Yakin hapus area ini?');">
                                        <?php echo Csrf::field(); ?>
                                        <input type="hidden" name="id" value="<?= $area['id_area'] ?>">
                                        <button type="submit" class="btn-delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                <p>Belum ada area. <a href="../public/index.php?url=area/create" style="color:#2980b9;">Buat area baru</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>