<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Area - Sistem Parkir</title>
    <style>
        :root {
            --primary: #2d6cdf;
            --secondary: #667eea;
            --success: #00d084;
            --danger: #f5576c;
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
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 24px;
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
        .form-container {
            background: white;
            padding: 32px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 0.3s ease;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .form-container h2 {
            font-size: 24px;
            margin-bottom: 24px;
            color: var(--dark);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 12px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
            font-size: 14px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(45, 108, 223, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-buttons {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }
        .btn-submit, .btn-cancel {
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            flex: 1;
            min-width: 140px;
            text-align: center;
        }
        .btn-submit {
            background: linear-gradient(90deg, var(--success), #00a86b);
            color: white;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 208, 132, 0.3);
        }
        .btn-cancel {
            background: #e0e0e0;
            color: var(--dark);
        }
        .btn-cancel:hover {
            background: #d0d0d0;
            transform: translateY(-2px);
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
        @media (max-width: 480px) {
            .container {
                padding: 12px;
            }
            .form-container {
                padding: 20px 16px;
            }
            .form-container h2 {
                font-size: 20px;
            }
            .form-buttons {
                flex-direction: column;
            }
            .btn-submit, .btn-cancel {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?url=area/index" class="back-link">← Kembali</a>
        
        <div class="form-container">
            <!-- Menampilkan pesan flash (sukses/gagal) jika ada -->
            <?php require_once __DIR__ . '/../../helpers/Flash.php'; if ($f = Flash::get()) { ?>
                <div class="flash <?= $f['type'] ?>"><?= htmlspecialchars($f['message']) ?></div>
            <?php } ?>
            <h2>➕ Tambah Area Parkir</h2>

            <!-- Form untuk menyimpan data area baru ke database -->
            <form method="POST" action="index.php?url=area/store">
                <?php echo Csrf::field(); ?>
                <div class="form-group">
                    <label for="nama_area">Nama Area <span style="color:red;">*</span></label>
                    <input type="text" id="nama_area" name="nama_area" placeholder="Contoh: Area A, Area Depan" required>
                </div>

                <div class="form-group">
                    <label for="kapasitas">Kapasitas (Slot) <span style="color:red;">*</span></label>
                    <input type="number" id="kapasitas" name="kapasitas" placeholder="Contoh: 50" min="1" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <textarea id="lokasi" name="lokasi" placeholder="Deskripsi lokasi area parkir" rows="3"></textarea>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Simpan Area</button>
                    <a href="index.php?url=area/index" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>