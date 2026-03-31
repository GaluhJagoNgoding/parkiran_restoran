<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Sistem Parkir</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h2 {
            font-size: 24px;
        }
        .btn-add {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-add:hover {
            background-color: #229954;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table thead {
            background-color: #34495e;
            color: white;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .role-admin {
            background-color: #e74c3c;
        }
        .role-petugas {
            background-color: #3498db;
        }
        .role-owner {
            background-color: #27ae60;
        }
        .status-aktif {
            background-color: #27ae60;
        }
        .status-nonaktif {
            background-color: #e74c3c;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn-edit, .btn-delete {
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        .btn-edit {
            background-color: #3498db;
        }
        .btn-edit:hover {
            background-color: #2980b9;
        }
        .btn-delete {
            background-color: #e74c3c;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
            background-color: white;
            border-radius: 5px;
        }
        .flash{padding:12px;border-radius:6px;margin-bottom:16px;font-weight:700}
        .flash.success{background:#e6ffed;color:#0b6624;border:1px solid #0f9b3a}
        .flash.error{background:#ffe6e6;color:#a81c1c;border:1px solid #e03a3a}
    </style>
</head>
<body>
    <div class="container">
        <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
            <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
        <?php } ?>
        <a href="index.php?url=dashboard/index" class="back-link">← Kembali ke Dashboard</a>
        
        <div class="header">
            <h2>👥 Manajemen User</h2>
            <a href="index.php?url=user/create" class="btn-add">+ Tambah User</a>
        </div>

        <?php if ($data && mysqli_num_rows($data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($user = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <span class="role-badge role-<?= $user['role'] ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="role-badge <?= $user['status_aktif'] == 1 ? 'status-aktif' : 'status-nonaktif' ?>">
                                    <?= $user['status_aktif'] == 1 ? 'Aktif' : 'Non-Aktif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?url=user/edit&id=<?= $user['id_user'] ?>" class="btn-edit">Edit</a>
                                        <form method="POST" action="index.php?url=user/delete" style="display:inline;" onsubmit="return confirm('Yakin hapus user ini?');">
                                        <?php echo Csrf::field(); ?>
                                        <input type="hidden" name="id" value="<?= $user['id_user'] ?>">
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
                <p>Belum ada user. <a href="index.php?url=user/create" style="color:#2980b9;">Buat user baru</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>