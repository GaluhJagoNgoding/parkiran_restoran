<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - Sistem Parkir</title>
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
        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            padding: 8px 12px;
            border-radius: var(--border-radius);
        }
        .back:hover {
            background: rgba(45, 108, 223, 0.1);
            transform: translateX(-3px);
        }
        h2 {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .action-bar {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-clear {
            background: linear-gradient(90deg, #f093fb, var(--danger));
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }
        .btn-clear:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(245, 87, 108, 0.3);
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
        table td:nth-child(1) {
            color: var(--muted);
            font-weight: 600;
            width: 50px;
        }
        table td:nth-child(2) {
            color: var(--primary);
            font-weight: 500;
            font-size: 13px;
        }
        table td:nth-child(3) {
            font-weight: 600;
            color: var(--dark);
        }
        table td:nth-child(4) {
            color: var(--secondary);
            font-weight: 600;
        }
        table td:nth-child(5) {
            color: var(--muted);
            font-size: 13px;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-delete {
            background: linear-gradient(90deg, #f093fb, var(--danger));
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(245, 87, 108, 0.3);
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
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
            font-size: 16px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .empty::before {
            content: '📭';
            display: block;
            font-size: 48px;
            margin-bottom: 12px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 16px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            text-align: center;
            border-left: 4px solid var(--secondary);
        }
        .stat-card .label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
        }
        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }
            h2 {
                font-size: 22px;
            }
            table {
                font-size: 13px;
            }
            table th, table td {
                padding: 10px 8px;
            }
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-clear {
                width: 100%;
                text-align: center;
            }
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .container {
                padding: 8px;
            }
            h2 {
                font-size: 18px;
            }
            table {
                font-size: 12px;
            }
            table th, table td {
                padding: 8px 6px;
            }
            .back {
                font-size: 13px;
                padding: 6px 10px;
            }
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?url=dashboard/index" class="back">← Kembali ke Dashboard</a>
        
        <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
            <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
        <?php } ?>
        
        <div class="header">
            <h2>📊 Log Aktivitas</h2>
            <form method="POST" action="index.php?url=log/clear" onsubmit="return confirm('Yakin ingin membersihkan semua log?');">
                <button type="submit" class="btn-clear">🧹 Bersihkan Semua Log</button>
            </form>
        </div>

        <?php if ($data && mysqli_num_rows($data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Meta</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $r['created_at'] ?? '-' ?></td>
                            <td><?= htmlspecialchars($r['username'] ?? 'System') ?></td>
                            <td><?= htmlspecialchars($r['action'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($r['meta'] ?? '-') ?></td>
                            <td>
                                <form method="POST" action="index.php?url=log/delete" style="display:inline;" onsubmit="return confirm('Yakin hapus log ini?');">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?? '' ?>">
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">Belum ada log aktivitas.</div>
        <?php endif; ?>
    </div>
</body>
</html>