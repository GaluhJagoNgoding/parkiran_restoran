<?php
/**
 * Test & Debug Page - Sistem Parkir Restoran
 * Jalankan file ini untuk memastikan setup sudah benar
 */

require_once '../app/config/database.php';
require_once '../app/models/User.php';

$db = Database::connect();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Setup - Sistem Parkir Restoran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f3f6fb;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .test-section h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .test-item {
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .test-item.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .test-item.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .test-item.warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        .test-item::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            font-weight: bold;
        }
        .test-item.success::before {
            content: '✓';
            color: #28a745;
            font-size: 18px;
        }
        .test-item.error::before {
            content: '✕';
            color: #dc3545;
            font-size: 18px;
        }
        .test-item.warning::before {
            content: '⚠';
            color: #ffc107;
            font-size: 18px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .table tr:hover {
            background: #f8f9fa;
        }
        .code {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            margin: 10px 0;
            overflow-x: auto;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            text-align: center;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-primary:hover, .btn-success:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Test Setup Sistem Parkir Restoran</h1>
            <p>Periksa konfigurasi dan data untuk memastikan login berfungsi</p>
        </div>

        <!-- DATABASE CONNECTION TEST -->
        <div class="test-section">
            <h2>🗄️ Koneksi Database</h2>
            <?php
            if ($db && !mysqli_connect_error()) {
                echo '<div class="test-item success">Database terkoneksi dengan baik</div>';
            } else {
                echo '<div class="test-item error">Koneksi database GAGAL: ' . mysqli_connect_error() . '</div>';
                exit;
            }
            ?>
        </div>

        <!-- TABLE EXISTENCE TEST -->
        <div class="test-section">
            <h2>📋 Status Tabel Database</h2>
            <?php
            $tables = ['tb_user', 'tb_kendaraan', 'tb_transaksi', 'tb_tarif', 'tb_area', 'tb_log'];
            foreach ($tables as $table) {
                $result = mysqli_query($db, "SHOW TABLES LIKE '$table'");
                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='test-item success'>Tabel <strong>$table</strong> sudah ada</div>";
                } else {
                    echo "<div class='test-item error'>Tabel <strong>$table</strong> BELUM dibuat</div>";
                }
            }
            ?>
        </div>

        <!-- USER DATA TEST -->
        <div class="test-section">
            <h2>👥 Data User di Database</h2>
            <?php
            $user_result = mysqli_query($db, "SELECT id, username, role, status_aktif FROM tb_user");
            if ($user_result && mysqli_num_rows($user_result) > 0) {
                echo '<table class="table">';
                echo '<tr><th>ID</th><th>Username</th><th>Role</th><th>Status</th></tr>';
                while ($row = mysqli_fetch_assoc($user_result)) {
                    $status = $row['status_aktif'] ? '✓ Aktif' : '✕ Tidak Aktif';
                    echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['role']}</td><td>$status</td></tr>";
                }
                echo '</table>';
                
                // Test login dengan kredensial default
                echo '<div style="margin-top: 20px; padding: 12px; background: #e3f2fd; border-radius: 4px;">';
                echo '<strong>📝 Hasil Test Login:</strong><br>';
                
                $test_users = [
                    ['username' => 'admin', 'password' => 'admin123', 'role' => 'admin'],
                    ['username' => 'petugas', 'password' => 'petugas123', 'role' => 'petugas'],
                    ['username' => 'owner', 'password' => 'owner123', 'role' => 'owner']
                ];
                
                foreach ($test_users as $test) {
                    $user = User::findByCredentials($test['username'], $test['password']);
                    if ($user) {
                        echo "<div style='color: #155724; margin: 5px 0;'>✓ {$test['username']} ({$test['role']}): Login BERHASIL</div>";
                    } else {
                        echo "<div style='color: #721c24; margin: 5px 0;'>✕ {$test['username']} ({$test['role']}): Login GAGAL</div>";
                    }
                }
                
                echo '</div>';
            } else {
                echo '<div class="test-item error">Tidak ada data user di database!</div>';
                echo '<div style="margin-top: 10px; padding: 12px; background: #fff3cd; border-radius: 4px; color: #856404;">';
                echo '<strong>⚠️ Solusi:</strong> Jalankan file <code>setup.php</code> untuk membuat tabel dan sample data<br>';
                echo '<a href="../setup.php" class="btn btn-primary" style="margin-top: 10px;">→ Jalankan Setup</a>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- TROUBLESHOOTING -->
        <div class="test-section">
            <h2>🔍 Troubleshooting</h2>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
                <p><strong>Jika masih tidak bisa login, coba:</strong></p>
                <ol style="padding-left: 20px; line-height: 1.8;">
                    <li>Pastikan database <code>parkir_restoran</code> sudah dibuat</li>
                    <li>Jalankan <code>setup.php</code> untuk membuat tabel dan sample data</li>
                    <li>Cek konfigurasi di <code>app/config/database.php</code></li>
                    <li>Pastikan MySQL/MariaDB service sudah berjalan</li>
                    <li>Gunakan kredensial: admin/admin123, petugas/petugas123, owner/owner123</li>
                </ol>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="button-group">
            <a href="../setup.php" class="btn btn-success">🔧 Jalankan Setup</a>
            <a href="index.php?url=auth/index" class="btn btn-primary">🔐 Ke Halaman Login</a>
        </div>
    </div>
</body>
</html>
