<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Sistem Parkir</title>
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
        .info-section {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 3px;
            margin-bottom: 20px;
            border-left: 4px solid #2980b9;
        }
        .info-section .row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }
        .info-section .label {
            font-weight: bold;
            color: #333;
        }
        .info-section .value {
            color: #555;
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
        .form-group input[type="number"] {
            font-family: 'Courier New', monospace;
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
        <a href="index.php?url=transaksi/index" class="back-link">← Kembali</a>
        
        <div class="form-container">
            <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
                <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
            <?php } ?>
            <h2>✏️ Edit Transaksi Parkir (Checkout)</h2>

            <div class="info-section">
                <div class="row">
                    <span class="label">ID Parkir:</span>
                    <span class="value"><?= htmlspecialchars($transaksi['id_parkir'] ?? '-') ?></span>
                </div>
                <div class="row">
                    <span class="label">Plat Nomor:</span>
                    <span class="value"><?= htmlspecialchars($transaksi['plat_nomor'] ?? '-') ?></span>
                </div>
                <div class="row">
                    <span class="label">Jenis Kendaraan:</span>
                    <span class="value"><?= htmlspecialchars($transaksi['jenis_kendaraan'] ?? '-') ?></span>
                </div>
                <div class="row">
                    <span class="label">Waktu Masuk:</span>
                    <span class="value"><?= $transaksi['waktu_masuk'] ?? '-' ?></span>
                </div>
                <div class="row">
                    <span class="label">Status Saat Ini:</span>
                    <span class="value"><?= htmlspecialchars($transaksi['status'] ?? '-') ?></span>
                </div>
            </div>

            <form method="POST" action="index.php?url=transaksi/update">
                <?php echo Csrf::field(); ?>
                <input type="hidden" name="id" value="<?= $transaksi['id_parkir'] ?? '' ?>">

                <div class="form-group">
                    <label for="biaya_total">Total Biaya (Rp) <span style="color:red;">*</span></label>
                    <div style="display: flex; gap: 10px;">
                        <input type="number" id="biaya_total" name="biaya_total" value="<?= $transaksi['biaya_total'] ?? 0 ?>" min="0" required>
                        <button type="button" id="btnHitung" onclick="hitungBiaya()" style="padding: 8px 15px; background: #3498db; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: bold;">Hitung Otomatis</button>
                    </div>
                    <small style="color: #666; margin-top: 8px; display: block;">
                        Tarif: Rp <?= number_format($tarif['tarif_per_jam'] ?? 0, 0, ',', '.') ?>/jam | Rp <?= number_format($tarif['tarif_per_hari'] ?? 0, 0, ',', '.') ?>/hari
                    </small>
                </div>
                
                <script>
                function hitungBiaya() {
                    const waktuMasuk = new Date('<?= $transaksi['waktu_masuk'] ?>');
                    const waktuKeluar = new Date();
                    const selisihMs = waktuKeluar - waktuMasuk;
                    const selisihJam = Math.ceil(selisihMs / (1000 * 60 * 60));
                    
                    const tarifPerJam = <?= $tarif['tarif_per_jam'] ?? 0 ?>;
                    const tarifPerHari = <?= $tarif['tarif_per_hari'] ?? 0 ?>;
                    
                    let biaya = 0;
                    if (tarifPerHari > 0 && selisihJam >= 24) {
                        const hari = Math.floor(selisihJam / 24);
                        const jamSisa = selisihJam % 24;
                        biaya = (hari * tarifPerHari) + (jamSisa * tarifPerJam);
                    } else {
                        biaya = selisihJam * tarifPerJam;
                    }
                    
                    document.getElementById('biaya_total').value = biaya;
                }
                </script>

                <div class="form-group">
                    <label for="status">Status <span style="color:red;">*</span></label>
                    <select id="status" name="status" required>
                        <option value="masuk" <?= ($transaksi['status'] ?? '') === 'masuk' ? 'selected' : '' ?>>Masuk</option>
                        <option value="selesai" <?= ($transaksi['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Update Transaksi</button>
                    <a href="index.php?url=transaksi/index" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
