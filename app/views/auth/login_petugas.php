<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Sistem Parkir Restoran</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .role-badge {
            display: inline-block;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
            width: 100%;
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
            margin-top: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .meta {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
            text-align: center;
            line-height: 1.5;
        }
        @media (max-width: 480px) {
            .card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="card">
            <div class="role-badge">👷 LOGIN PETUGAS</div>
            <h2>Sistem Parkir Restoran</h2>
            <!-- Form Login Khusus Petugas -->
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
                <!-- Input hidden role petugas -->
                <input type="hidden" name="role" value="petugas">
                <button class="btn" type="submit">Login</button>
                <div class="meta">Akses petugas: catat transaksi masuk/keluar kendaraan.</div>
            </form>
        </div>
    </div>
</body>
</html>