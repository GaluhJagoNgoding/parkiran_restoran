<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tarif - Sistem Parkir</title>
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
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        .form-group input:focus {
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
        .flash{padding:12px;border-radius:6px;margin-bottom:16px;font-weight:700}
        .flash.success{background:#e6ffed;color:#0b6624;border:1px solid #0f9b3a}
        .flash.error{background:#ffe6e6;color:#a81c1c;border:1px solid #e03a3a}
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?url=tarif/index" class="back-link">← Kembali</a>
        
        <div class="form-container">
            <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
                <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
            <?php } ?>
            <h2>✏️ Edit Tarif Parkir</h2>

            <form method="POST" action="index.php?url=tarif/update">
                <?php echo Csrf::field(); ?>
                <input type="hidden" name="id" value="<?= $tarif['id_tarif'] ?>">

                <div class="form-group">
                    <label for="jenis_kendaraan">Jenis Kendaraan <span style="color:red;">*</span></label>
                    <input type="text" id="jenis_kendaraan" name="jenis_kendaraan" value="<?= htmlspecialchars($tarif['jenis_kendaraan']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="tarif_per_jam">Tarif Per Jam (Rp) <span style="color:red;">*</span></label>
                    <input type="number" id="tarif_per_jam" name="tarif_per_jam" value="<?= $tarif['tarif_per_jam'] ?? 0 ?>" min="1" required>
                </div>

                <div class="form-group">
                    <label for="tarif_per_hari">Tarif Per Hari (Rp) <span style="color:red;">*</span></label>
                    <input type="number" id="tarif_per_hari" name="tarif_per_hari" value="<?= $tarif['tarif_per_hari'] ?? 0 ?>" min="1" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Update Tarif</button>
                    <a href="index.php?url=tarif/index" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>