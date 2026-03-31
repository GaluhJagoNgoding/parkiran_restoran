<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Sistem Parkir</title>
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
            max-width: 600px;
            margin: 0 auto;
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
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2980b9;
            box-shadow: 0 0 5px rgba(41, 128, 185, 0.2);
        }
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn-submit, .btn-cancel {
            padding: 12px 30px;
            border: none;
            border-radius: 3px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }
        .btn-submit {
            background-color: #3498db;
            color: white;
        }
        .btn-submit:hover {
            background-color: #2980b9;
        }
        .btn-cancel {
            background-color: #95a5a6;
            color: white;
        }
        .btn-cancel:hover {
            background-color: #7f8c8d;
        }
        .form-info {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 3px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 13px;
        }
        .readonly-field {
            background-color: #f5f5f5;
            color: #666;
        }
        .flash{padding:12px;border-radius:6px;margin-bottom:16px;font-weight:700}
        .flash.success{background:#e6ffed;color:#0b6624;border:1px solid #0f9b3a}
        .flash.error{background:#ffe6e6;color:#a81c1c;border:1px solid #e03a3a}
        .readonly-field:focus {
            outline: none;
            border-color: #ddd;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?url=user/index" class="back-link">← Kembali</a>
        
        <div class="form-container">
            <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
                <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
            <?php } ?>
            <h2>✏️ Edit User</h2>
            
            <div class="form-info">
                <strong>Catatan:</strong> Kosongkan password jika tidak ingin mengubahnya.
            </div>

            <form method="POST" action="index.php?url=user/update">
                <?php echo Csrf::field(); ?>
                <input type="hidden" name="id" value="<?= $user['id_user'] ?>">

                <div class="form-group">
                    <label for="username">Username <span style="color:red;">*</span></label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password baru">
                </div>

                <div class="form-group">
                    <label for="role">Role <span style="color:red;">*</span></label>
                    <select id="role" name="role" required>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="petugas" <?= $user['role'] == 'petugas' ? 'selected' : '' ?>>Petugas</option>
                        <option value="owner" <?= $user['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="created_at">Dibuat</label>
                    <input type="text" id="created_at" value="<?= $user['created_at'] ?>" class="readonly-field" readonly>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Update User</button>
                    <a href="../public/index.php?url=user/index" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>