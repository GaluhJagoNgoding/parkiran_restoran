<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Parkir Restoran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .split {
            display: flex;
            height: 100vh;
        }
        .left, .right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        .left {
            background: rgba(0, 0, 0, 0.2);
            color: white;
        }
        .brand {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .brand strong {
            color: #ffd700;
        }
        .hero {
            font-size: 18px;
            line-height: 1.6;
            max-width: 400px;
            text-align: center;
            opacity: 0.95;
        }
        .right {
            background: white;
        }
        .card {
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .card h2 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        @media (max-width: 768px) {
            .split {
                flex-direction: column;
            }
            .left {
                display: none;
            }
            .card {
                max-width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="split">
        <!-- Bagian Kiri: Informasi Branding & Hero -->
        <div class="left">
            <div class="brand">Parkir <strong>Restoran</strong></div>
            <div class="hero">Kelola parkir restoran Anda dengan mudah. Masuk untuk mengatur kendaraan, transaksi, tarif, dan area secara cepat.</div>
            <div style="margin-top: 40px; text-align: center; font-size: 12px; opacity: 0.8;">
                <p style="margin-bottom: 20px;">Atau pilih login khusus role:</p>
                <a href="index.php?url=auth/login_admin" style="display: inline-block; color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px; margin: 4px; transition: all 0.3s;">👤 Admin</a>
                <a href="index.php?url=auth/login_petugas" style="display: inline-block; color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px; margin: 4px; transition: all 0.3s;">👷 Petugas</a>
                <a href="index.php?url=auth/login_owner" style="display: inline-block; color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px; margin: 4px; transition: all 0.3s;">👨‍💼 Owner</a>
            </div>
        </div>
        <!-- Bagian Kanan: Form Login -->
        <div class="right">
            <div class="card">
                <h2>Masuk ke Sistem</h2>
                <!-- Form mengirim data ke controller Auth method proses -->
                <form method="POST" action="index.php?url=auth/proses">
                    <?php require_once __DIR__ . '/../../helpers/Csrf.php';
echo Csrf::field(); ?>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                    <button class="btn" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
